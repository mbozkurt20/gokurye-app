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
            ->orderBy('created_at', 'asc')
            ->whereIn('status', [OrderStatus::PREPARED, OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->get();

        return Json::success('Aktif Siparişlerim', OrderResource::collection($orders));
    }
    public function pastOrdes(Request $request)
    {
        $startDate = $request->query('startDate')
            ? Carbon::parse($request->query('startDate'))->startOfDay() // Change to startOfDay
            : Carbon::today()->startOfDay();

        $endDate = $request->query('endDate')
            ? Carbon::parse($request->query('endDate'))->endOfDay()
            : Carbon::today()->endOfDay();

        $courier = auth('courier')->user();
        $orders = Order::query()->where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->whereNotIn('status', [OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->get();

        return Json::success('Geçmiş Siparişlerim', OrderResource::collection($orders));
    }

    public function transfer(Request $request, $orderId)
    {
        $courier = auth('courier')->user();

        // Feature 4: Kurye Paket İptal Edebilsin
        $feature4Active = AdminSystemFeature::where('admin_id', $courier->admin_id)->where('system_feature_id', 4)->exists();
        if (!$feature4Active) {
            return Json::error('Sipariş transferi için yetkiniz bulunmamaktadır.', 403);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'status' => 'required|in:accident,fault,other',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors());
        }

       $fi = CourierOrder::query()->where('order_id', $order->id)
           ->where('courier_id',$courier->id)
           ->whereNull('status')
           ->whereNull('reason')
           ->first();

        $fi->update([
           'reason' => $request->reason,
           'status' => $request->status,
        ]);

        $order->update([
            'courier_id' => -1,
            'assigned_at' => null,
            'status' => OrderStatus::PREPARED,
        ]);

        $courier->status = CourierStatus::passive;
        $courier->update();

        return Json::success('Sipariş boşa çıkarıldı, kurye müsait kuryeye atanıcaktır.');
    }

    public function availableOrders()
    {
        $courier = auth('courier')->user();

        // Feature 5: Kurye Boş Paketleri Görebilsin
        $feature5Active = AdminSystemFeature::where('admin_id', $courier->admin_id)->where('system_feature_id', 5)->exists();
        if (!$feature5Active) {
            return Json::error('Bu özellik admininiz tarafından aktif edilmemiş.', 403);
        }

        $restaurantIds = Restaurant::where('admin_id', $courier->admin_id)->pluck('id');

        $orders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', OrderStatus::PREPARED)
            ->where('courier_id', -1)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        return Json::success('Boş Siparişler', OrderResource::collection($orders));
    }

    public function changeStatus(Request $request, $orderId)
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }

        $statusId = $request->input('order_status_id');
        // Feature 3: Kurye Paket Statüleri Bildir — admin_id'yi order üzerinden al (courier guard'da admin null döner)
        $adminId = $order->restaurant?->admin_id;
        $feature3Active = $adminId && AdminSystemFeature::where('admin_id', $adminId)->where('system_feature_id', 3)->exists();

        try {
            return DB::transaction(function () use ($order, $courier, $statusId, $feature3Active) {

                // --- DURUM 4: KURYE TESLİM ALDI (ASSIGNED) ---
                if ($statusId == 4) {
                    $order->update([
                        'courier_id' => $courier->id,
                        'status' => OrderStatus::ASSIGNED
                    ]);
                }

                // --- DURUM 3: KURYE YOLA ÇIKTI (HANDOVER) ---
                elseif ($statusId == 3) {
                    if (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        if ($order->entegra_current_status == EntegraStatusEnum::PREPARING) {
                            $response = EntegraService::updateOrder($order->pid);
                            if (!$response->success) {
                                throw new \Exception('Entegra güncellenemedi: ' . ($response->message ?? 'Hata'));
                            }
                            $order->entegra_current_status = $response->status;
                            $order->entegra_next_status = $response->orderStatus;
                        }
                    }

                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::HANDOVER;
                    $order->update();

                    $courier->update(['status' => CourierStatus::service]);
                }

                // --- DURUM 1: PAKET TESLİM EDİLDİ (DELIVERED) ---
                elseif ($statusId == 1) {
                   if (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {

                        // Sadece statü HANDOVER ise Entegra'yı güncelle
                        if ($order->entegra_current_status == EntegraStatusEnum::HANDOVER) {
                            $response = EntegraService::updateOrder($order->pid);

                            if (!$response->success) {
                                // Hata durumunda Helper'ı kullanarak statüyü kaydet ve durdur
                                $order->entegra_current_status = $response->status;
                                $order->entegra_next_status = $response->orderStatus;
                                $order->status = EntegraStatusHelper::getNameByValue($response->orderStatus);
                                $order->update();

                                throw new \Exception('Entegra teslimat hatası: ' . $order->status);
                            }

                            // Başarılıysa Entegra'dan gelen güncel verileri işle
                            $order->entegra_current_status = $response->status;
                            $order->entegra_next_status = $response->orderStatus;
                            $order->status = OrderStatus::DELIVERED;
                        } else {
                            // Zaten teslim edilmişse veya statü uygun değilse de ana statüyü güncelle
                            $order->status = OrderStatus::DELIVERED;
                        }
                    } else {
                        // Diğer platformlar için varsayılan işlem
                        $order->status = OrderStatus::DELIVERED;
                    }

                    $order->update();
                    $courier->update(['status' => CourierStatus::active]);

                    if ($feature3Active) {
                        NotificationHelper::add([
                            'title' => 'Paket Teslim Edildi',
                            'description' => "{$order->tracking_id} takip numaralı paket teslim edildi.",
                            'url' => route('admin.balance')
                        ]);
                    }
                }

                // --- DURUM 2: PAKET REDDEDİLDİ ---
                elseif ($statusId == 2) {
                    $courier->update(['status' => CourierStatus::active]);
                    $order->update([
                        'courier_id' => -1,
                        'assigned_at' => null,
                        'status' => OrderStatus::PREPARED
                    ]);

                    CourierOrder::where('order_id', $order->id)->where('courier_id', $courier->id)->delete();

                    if ($feature3Active) {
                        NotificationHelper::add([
                            'title' => 'Kurye Paketi Reddetti',
                            'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından reddedildi..',
                            'url' => route('admin.balance')
                        ]);
                    }
                }

                return Json::success('Sipariş Durumu Güncellendi', new OrderResource($order));
            });

        } catch (\Exception $e) {
            Log::error("Sipariş Güncelleme Hatası: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function report(Request $request)
    {
        $courier = auth('courier')->user();

        $startDate = Carbon::parse($request->startDate)->startOfDay();
        $endDate   = Carbon::parse($request->endDate)->endOfDay();

        $orderCount = Order::where('courier_id', $courier->id)
            ->where('status',OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $courierOrderIds = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('order_id');

        // Sipariş listesi (admin ekranında tablo için)
        $orders = Order::whereIn('id', $courierOrderIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $deliveredOrders = $orders->where('status', OrderStatus::DELIVERED);

        $total = 0;

        $info = "";

        if ($courier->price_type == 'package') {
            $pricePerPackage = (float) $courier->price;
            $total += $orderCount * $pricePerPackage;

            // Paket başı ücret bilgilendirmesi
            $info = "Paket başı sabit ücret sistemine göre; {$orderCount} adet teslimat için paket başı " .
                number_format($pricePerPackage, 2) . " TL üzerinden hesaplama yapılmıştır.";

        } else {
            $kmPrice = (float) $courier->km_price;
            $externalKm = (float) $courier->km_distance_later;
            $fixedPrice = (float) $courier->fixed_price;

            $distanceTotal = $deliveredOrders->sum(function($o) use ($kmPrice, $externalKm) {
                $orderKm = (float) $o->distance;
                $payableKm = max(0, $orderKm - $externalKm);
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $total += ($distanceTotal + $fixedTotal);

            // Mesafe + Sabit ücret bilgilendirmesi
            $info = "Paket başı sabit " . number_format($fixedPrice, 2) . " TL'ye ek olarak; " .
                "her siparişte ilk {$externalKm} km'den sonraki mesafe için km başına " .
                number_format($kmPrice, 2) . " TL eklenerek hesaplama yapılmıştır.";
        }

        return response()->json([
            'order_count' => $orderCount,
            'total_progress_payment' => number_format($total, 2, '.', ''),
            'calculation_info' => $info // Bilgilendirme metni
        ]);
    }

    public function verifyOrderCode(Request $request, $orderId): JsonResponse
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        $code = $request->code;

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }

        if (Order::where('verify_code', $code)->where('id', $order->id)->exists()) {
            $order->status = OrderStatus::DELIVERED;
            $order->verify_code = null;
            $order->update();

            $courier->status = CourierStatus::active;
            $courier->update();
            return Json::success('Kod başarıyla doğrulandı ve sipariş başarıyla teslim edildi.');
        } else {
            return Json::success('Doğrulama Kodu Eşleşmiyor', null, 401);
        }
    }

    /*
     *     public function changeStatus(Request $request, $orderId)
    {
        //gpsyemek PREPARED - HANDOVER - DELIVERED - REJECTED
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }
//
        //KURYE TESLİM ALDI ASSIGNED
        if ($request->input('order_status_id') == 4) {
            try {
                $order->courier_id = $courier->id;
                $order->status = OrderStatus::ASSIGNED;
                $order->update();
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

        // KURYE YOLA ÇIKTI HANDOVER
        if ($request->input('order_status_id') == 3) {
            try {
                DB::transaction(function () use ($order, $courier) {

                    // 1. API KONTROLLERİ (Sadece entegrasyonu olanlar için)
                    if ($order->platform === 'gpsyemek') {
                        $gpsResponse = app(GpsYemekController::class)->updateOrder(new Request([
                            'action'      => OrderStatus::HANDOVER,
                            'tracking_id' => $order->tracking_id,
                        ]));

                        if (!$gpsResponse) throw new \Exception("GpsYemek API hatası.");
                    }

                    elseif (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        if ($order->entegra_current_status == EntegraStatusEnum::PREPARING) {
                            $response = EntegraHelper::updateOrder($order->pid);
                            Log::info("Response". json_encode($response));

                            if ($response->success) {
                                Log::info("Success" . json_encode($response));
                                $order->entegra_current_status = $response->status;
                                $order->entegra_next_status = $response->orderStatus;
                            } else {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'Üzgünüz, lütfen yeniden deneyiniz.'
                                ], 404);
                            }
                        }
                    }

                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::HANDOVER;
                    $order->update();

                    $courier->status = CourierStatus::service;
                    $courier->update();
                });

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

        // PAKET TESLİM EDİLDİ DELIVERED
        if ($request->input('order_status_id') == 1) {
            try {
                DB::transaction(function () use ($order, $courier) {

                    if ($order->platform === 'gpsyemek') {
                        $gpsResponse = app(GpsYemekController::class)->updateOrder(new Request([
                            'action'      => OrderStatus::DELIVERED,
                            'tracking_id' => $order->tracking_id,
                        ]));

                        // API tarafında bir sorun varsa işlemi iptal et
                        if (!$gpsResponse) throw new \Exception("GpsYemek servis hatası.");
                    }

                    elseif (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        if ($order->entegra_current_status == EntegraStatusEnum::HANDOVER) {
                            $response = EntegraHelper::updateOrder($order->pid);
                            Log::info("Response". json_encode($response));

                            if ($response->success) {
                                Log::info("Success" . json_encode($response));
                                $order->entegra_current_status = $response->status;
                                $order->entegra_next_status = $response->orderStatus;
                            } else {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'Üzgünüz, lütfen yeniden deneyiniz.'
                                ], 404);
                            }
                        }
                    }

                    // Siparişi teslim edildi yap
                    $order->status = OrderStatus::DELIVERED;
                    $order->update();

                    // Kuryeyi boşa çıkar (Aktif yap)
                    $courier->status = CourierStatus::active;
                    $courier->update();

                    // 3. BİLDİRİM İŞLEMİ
                    if ($feature3Active) {
                        NotificationHelper::add([
                            'title' => 'Paket Teslim Edildi',
                            'description' => "{$order->tracking_id} takip numaralı paket {$courier->name} kurye tarafından teslim edildi.",
                            'url' => route('admin.balance')
                        ]);
                    }
                });

            } catch (\Exception $e) {
                // API hatası olduğunda buraya düşer ve hiçbir statü değişmez
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

        // PAKET REDDEDİLDİ ASSIGNED | OTOMATIK PREPARED DÜŞER ATAMA BEKLER
        if ($request->input('order_status_id') == 2) {
           $courier->status = CourierStatus::active;
           $courier->update();

           $order->courier_id = -1;
           $order->assigned_at = null;
           $order->status = OrderStatus::PREPARED;
           $order->update();

           $courierOrder = CourierOrder::where('order_id',$order->id)->where('courier_id',$courier->id)->first();
           if ($courierOrder) {
               $courierOrder->delete();
           }

            if ($feature3Active) {
                NotificationHelper::add([
                    'title' => 'Kurye Paketi Reddetti',
                    'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından reddedildi..',
                    'url' => route('admin.balance')
                ]);
            }
        }

        return Json::success('Sipariş Durumu Güncellendi', new OrderResource($order));
    }
     */
}
