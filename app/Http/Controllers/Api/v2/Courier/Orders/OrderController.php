<?php

namespace App\Http\Controllers\Api\v2\Courier\Orders;

use App\Enums\EntegraStatusEnum;
use App\Helpers\CourierStatus;
use App\Helpers\EntegraStatusHelper;
use App\Helpers\Json;
use App\Helpers\NotificationHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\AdminSystemFeature;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\Restaurant;
use App\Services\EntegraService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function activeOrders()
    {
        $courier = auth('courier')->user();

        $orders = Order::where('courier_id', $courier->id)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [OrderStatus::PREPARED, OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->orderBy('created_at', 'asc')
            ->get();

        return Json::success('Aktif siparişlerim', OrderResource::collection($orders));
    }

    public function pastOrders(Request $request)
    {
        $startDate = $request->query('startDate')
            ? Carbon::parse($request->query('startDate'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->query('endDate')
            ? Carbon::parse($request->query('endDate'))->endOfDay()
            : Carbon::today()->endOfDay();

        $courier = auth('courier')->user();

        $orders = Order::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', [OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->orderBy('created_at', 'desc')
            ->get();

        return Json::success('Geçmiş siparişlerim', OrderResource::collection($orders));
    }

    public function availableOrders()
    {
        $courier = auth('courier')->user();

        // Feature 5: Kurye Boş Paketleri Görebilsin
        $feature5Active = AdminSystemFeature::where('admin_id', $courier->admin_id)
            ->where('system_feature_id', 5)
            ->exists();

        if (!$feature5Active) {
            return Json::error('Bu özellik admininiz tarafından aktif edilmemiş.', 403);
        }

        $restaurantIds = Restaurant::where('admin_id', $courier->admin_id)->pluck('id');

        $orders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', OrderStatus::PREPARED)
            ->whereNull('courier_id')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        return Json::success('Atanmayı bekleyen siparişler', OrderResource::collection($orders));
    }

    public function changeStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'order_status_id' => 'required|integer|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return Json::error('Sipariş bulunamadı.', 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz.', 403);
        }

        $statusId = (int) $request->input('order_status_id');
        $adminId = $order->restaurant?->admin_id;
        $feature3Active = $adminId && AdminSystemFeature::where('admin_id', $adminId)
            ->where('system_feature_id', 3)
            ->exists();

        try {
            return DB::transaction(function () use ($order, $courier, $statusId, $feature3Active) {

                // DURUM 4: KURYE TESLİM ALDI (ASSIGNED)
                if ($statusId === 4) {
                    $order->update([
                        'courier_id' => $courier->id,
                        'status' => OrderStatus::ASSIGNED,
                    ]);
                }

                // DURUM 3: KURYE YOLA ÇIKTI (HANDOVER)
                elseif ($statusId === 3) {
                    if (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        if ($order->entegra_current_status == EntegraStatusEnum::PREPARING) {
                            $response = EntegraService::updateOrder($order->pid);
                            if (!$response->success) {
                                throw new \Exception('Entegra güncellenemedi: ' . ($response->message ?? 'Bilinmeyen hata'));
                            }
                            $order->entegra_current_status = $response->status;
                            $order->entegra_next_status = $response->orderStatus;
                        }
                    }

                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::HANDOVER;
                    $order->save();

                    $courier->update(['status' => CourierStatus::service]);
                }

                // DURUM 1: PAKET TESLİM EDİLDİ (DELIVERED)
                elseif ($statusId === 1) {
                    if (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        if ($order->entegra_current_status == EntegraStatusEnum::HANDOVER) {
                            $response = EntegraService::updateOrder($order->pid);

                            if (!$response->success) {
                                $order->entegra_current_status = $response->status;
                                $order->entegra_next_status = $response->orderStatus;
                                $order->status = EntegraStatusHelper::getNameByValue($response->orderStatus);
                                $order->save();

                                throw new \Exception('Entegra teslimat hatası: ' . $order->status);
                            }

                            $order->entegra_current_status = $response->status;
                            $order->entegra_next_status = $response->orderStatus;
                        }
                    }

                    $order->status = OrderStatus::DELIVERED;
                    $order->save();

                    $courier->update(['status' => CourierStatus::active]);

                    if ($feature3Active) {
                        NotificationHelper::add([
                            'title' => 'Paket Teslim Edildi',
                            'description' => "{$order->tracking_id} takip numaralı paket teslim edildi.",
                            'url' => route('admin.balance'),
                        ]);
                    }
                }

                // DURUM 2: PAKET REDDEDİLDİ
                elseif ($statusId === 2) {
                    $order->update([
                        'courier_id' => null,
                        'assigned_at' => null,
                        'status' => OrderStatus::PREPARED,
                    ]);

                    $courier->update(['status' => CourierStatus::active]);

                    CourierOrder::where('order_id', $order->id)
                        ->where('courier_id', $courier->id)
                        ->delete();

                    if ($feature3Active) {
                        NotificationHelper::add([
                            'title' => 'Kurye Paketi Reddetti',
                            'description' => "{$order->tracking_id} takip numaralı paket {$courier->name} kurye tarafından reddedildi.",
                            'url' => route('admin.balance'),
                        ]);
                    }
                }

                return Json::success('Sipariş durumu güncellendi.', new OrderResource($order->fresh()));
            });

        } catch (\Exception $e) {
            Log::error('Sipariş güncelleme hatası: ' . $e->getMessage());
            return Json::error($e->getMessage(), 400);
        }
    }

    public function transfer(Request $request, $orderId)
    {
        $courier = auth('courier')->user();

        // Feature 4: Kurye Paket Transfer Edebilsin
        $feature4Active = AdminSystemFeature::where('admin_id', $courier->admin_id)
            ->where('system_feature_id', 4)
            ->exists();

        if (!$feature4Active) {
            return Json::error('Sipariş transferi için yetkiniz bulunmamaktadır.', 403);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return Json::error('Sipariş bulunamadı.', 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi transfer edemezsiniz.', 403);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'status' => 'required|in:accident,fault,other',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courierOrder = CourierOrder::where('order_id', $order->id)
            ->where('courier_id', $courier->id)
            ->whereNull('status')
            ->whereNull('reason')
            ->first();

        if (!$courierOrder) {
            return Json::error('Bu sipariş için transfer kaydı bulunamadı.', 404);
        }

        $courierOrder->update([
            'reason' => $request->reason,
            'status' => $request->status,
        ]);

        $order->update([
            'courier_id' => null,
            'assigned_at' => null,
            'status' => OrderStatus::PREPARED,
        ]);

        $courier->update(['status' => CourierStatus::active]);

        return Json::success('Sipariş transfer edildi, en kısa sürede başka bir kuryeye atanacaktır.');
    }

    public function verifyOrderCode(Request $request, $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return Json::error('Sipariş bulunamadı.', 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz.', 403);
        }

        if (!Order::where('verify_code', $request->code)->where('id', $order->id)->exists()) {
            return Json::error('Doğrulama kodu eşleşmiyor.', 422);
        }

        $order->status = OrderStatus::DELIVERED;
        $order->verify_code = null;
        $order->save();

        $courier->update(['status' => CourierStatus::active]);

        return Json::success('Kod doğrulandı, sipariş teslim edildi.');
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();

        $startDate = Carbon::parse($request->startDate)->startOfDay();
        $endDate = Carbon::parse($request->endDate)->endOfDay();

        $orderCount = Order::where('courier_id', $courier->id)
            ->where('status', OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $courierOrderIds = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('order_id');

        $deliveredOrders = Order::whereIn('id', $courierOrderIds)
            ->where('status', OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $total = 0;
        $info = '';

        if ($courier->price_type == 'package') {
            $pricePerPackage = (float) $courier->price;
            $total = $orderCount * $pricePerPackage;
            $info = "Paket başı sabit ücret sistemine göre; {$orderCount} adet teslimat için paket başı " .
                number_format($pricePerPackage, 2) . " TL üzerinden hesaplama yapılmıştır.";
        } else {
            $kmPrice = (float) $courier->km_price;
            $externalKm = (float) $courier->km_distance_later;
            $fixedPrice = (float) $courier->fixed_price;

            $distanceTotal = $deliveredOrders->sum(function ($order) use ($kmPrice, $externalKm) {
                $orderKm = (float) $order->distance;
                $payableKm = max(0, $orderKm - $externalKm);
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $total = $distanceTotal + $fixedTotal;

            $info = "Paket başı sabit " . number_format($fixedPrice, 2) . " TL'ye ek olarak; " .
                "her siparişte ilk {$externalKm} km'den sonraki mesafe için km başına " .
                number_format($kmPrice, 2) . " TL eklenerek hesaplama yapılmıştır.";
        }

        return Json::success('Rapor', [
            'order_count' => $orderCount,
            'total_progress_payment' => number_format($total, 2, '.', ''),
            'calculation_info' => $info,
        ]);
    }
}
