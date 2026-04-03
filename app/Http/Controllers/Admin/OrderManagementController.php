<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManagementController extends Controller
{
    public function index()
    {
        $adminId = auth('admin')->id();

        // Atanmayı bekleyen siparişler (bugün, PREPARED)
        $pendingOrders = Order::with('restaurant')
            ->whereHas('restaurant', fn($q) => $q->where('admin_id', $adminId))
            ->where('status', OrderStatus::PREPARED)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        // Aktif kuryeler (active veya service)
        $couriers = Courier::where('admin_id', $adminId)
            ->whereIn('status', [CourierStatus::active, CourierStatus::service])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Transfer kayıtları (kurye reddetmiş/devretmiş paketler)
        $transfers = CourierOrder::with(['order.restaurant', 'courier'])
            ->whereHas('order.restaurant', fn($q) => $q->where('admin_id', $adminId))
            ->whereNotNull('status')
            ->whereNotNull('reason')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.management', compact('pendingOrders', 'couriers', 'transfers'));
    }

    // Seçili siparişleri tek kuryeye birleştir ve ata
    public function merge(Request $request)
    {
        $request->validate([
            'order_ids'  => 'required|array|min:1',
            'order_ids.*'=> 'integer|exists:orders,id',
            'courier_id' => 'required|integer|exists:couriers,id',
        ]);

        $adminId   = auth('admin')->id();
        $courierId = $request->courier_id;
        $orderIds  = $request->order_ids;

        $courier = Courier::where('id', $courierId)
            ->where('admin_id', $adminId)
            ->firstOrFail();

        $orders = Order::whereIn('id', $orderIds)
            ->whereHas('restaurant', fn($q) => $q->where('admin_id', $adminId))
            ->where('status', OrderStatus::PREPARED)
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Geçerli sipariş bulunamadı.');
        }

        DB::transaction(function () use ($orders, $courier) {
            foreach ($orders as $order) {
                $order->update([
                    'courier_id'  => $courier->id,
                    'assigned_at' => now(),
                    'status'      => OrderStatus::PREPARED,
                ]);

                CourierOrder::firstOrCreate([
                    'courier_id' => $courier->id,
                    'order_id'   => $order->id,
                ]);
            }

            // Kurye servis moduna al
            $courier->update([
                'status'           => CourierStatus::service,
                'last_assigned_at' => now(),
            ]);
        });

        // FCM bildirimi gönder
        if ($courier->fcm_token) {
            try {
                (new PushNotificationService())->sendNotification(
                    $courier->fcm_token,
                    'Paket Grubu Atandı',
                    count($orders) . ' adet paket size atandı.',
                    ['type' => 'new_order', 'order_id' => (string)($orders[0]->id ?? '')]
                );
            } catch (\Exception $e) {
                Log::warning('FCM gönderilemedi: ' . $e->getMessage());
            }
        }

        return back()->with('success', count($orders) . ' sipariş ' . $courier->name . ' adlı kuryeye atandı.');
    }

    // Home tablosundan toplu kurye atama (JSON)
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'courier_id'  => 'required|integer|exists:couriers,id',
        ]);

        $adminId   = auth('admin')->id();
        $courier   = Courier::where('id', $request->courier_id)->where('admin_id', $adminId)->firstOrFail();

        $orders = Order::whereIn('id', $request->order_ids)
            ->whereHas('restaurant', fn($q) => $q->where('admin_id', $adminId))
            ->where('status', OrderStatus::PREPARED)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Geçerli sipariş bulunamadı.']);
        }

        DB::transaction(function () use ($orders, $courier) {
            foreach ($orders as $order) {
                $order->update([
                    'courier_id'  => $courier->id,
                    'assigned_at' => now(),
                    'status'      => OrderStatus::PREPARED,
                ]);
                CourierOrder::firstOrCreate(['courier_id' => $courier->id, 'order_id' => $order->id]);
            }
            $courier->update(['status' => CourierStatus::service, 'last_assigned_at' => now()]);
        });

        if ($courier->fcm_token) {
            try {
                (new PushNotificationService())->sendNotification(
                    $courier->fcm_token,
                    'Paket Grubu Atandı',
                    count($orders) . ' adet paket size atandı.',
                    ['type' => 'new_order', 'order_id' => (string)($orders[0]->id ?? '')]
                );
            } catch (\Exception $e) {
                Log::warning('FCM gönderilemedi: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => count($orders) . ' sipariş ' . $courier->name . ' adlı kuryeye atandı.']);
    }

    // Transfer edilmiş paketi başka kuryeye ata
    public function reassign(Request $request, int $orderId)
    {
        $request->validate([
            'courier_id' => 'required|integer|exists:couriers,id',
        ]);

        $adminId = auth('admin')->id();

        $order = Order::whereHas('restaurant', fn($q) => $q->where('admin_id', $adminId))
            ->where('status', OrderStatus::PREPARED)
            ->findOrFail($orderId);

        $courier = Courier::where('id', $request->courier_id)
            ->where('admin_id', $adminId)
            ->firstOrFail();

        DB::transaction(function () use ($order, $courier) {
            $order->update([
                'courier_id'  => $courier->id,
                'assigned_at' => now(),
            ]);

            CourierOrder::firstOrCreate([
                'courier_id' => $courier->id,
                'order_id'   => $order->id,
            ]);

            $courier->update(['last_assigned_at' => now()]);
        });

        if ($courier->fcm_token) {
            try {
                (new PushNotificationService())->sendNotification(
                    $courier->fcm_token,
                    'Yeni Sipariş Atandı',
                    'Transfer edilen sipariş size yönlendirildi. Takip: ' . $order->tracking_id,
                    ['type' => 'new_order', 'order_id' => (string)$order->id]
                );
            } catch (\Exception $e) {
                Log::warning('FCM gönderilemedi: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Sipariş ' . $courier->name . ' adlı kuryeye yeniden atandı.');
    }
}
