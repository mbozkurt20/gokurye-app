<?php

namespace App\Jobs;

use App\Helpers\CourierStatus;
use App\Helpers\MapHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\Courier;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignPendingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ BAŞLADI ---');

        $orders = Order::with('restaurant.admin')
            ->where('courier_id', -1)
            ->where('status', OrderStatus::PREPARED)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            Log::info('Otomatik Atama: Atanacak uygun sipariş bulunamadı.');
            return;
        }

        foreach ($orders as $order) {
            $restaurant = $order->restaurant;

            if (!$restaurant) continue;

            $admin = $restaurant->admin;
            $distLimit       = $admin->distance_limit    ?? 50;
            $maxPackageLimit = $admin->max_package_limit ?? 4;

            // Adminin tüm aktif kuryelerini çek
            $allCouriers = Courier::where('status', CourierStatus::active)
                ->where('admin_id', $restaurant->admin_id)
                ->get();

            if ($allCouriers->isEmpty()) {
                Log::warning("Sipariş #{$order->id} için müsait kurye yok.");
                continue;
            }

            // 1. Adım: Her kurye için restorana mesafeyi hesapla ve limiti aşanları filtrele
            $couriersWithinRange = $allCouriers->filter(function ($courier) use ($restaurant, $distLimit) {
                $distanceMeters = MapHelper::getGoogleDistance(
                    $courier->latitude, $courier->longitude,
                    $restaurant->latitude, $restaurant->longitude
                );

                if ($distanceMeters === null) {
                    $distanceMeters = OrdersHelper::haversineDistance(
                        $courier->latitude, $courier->longitude,
                        $restaurant->latitude, $restaurant->longitude
                    );
                }

                $distKm = ($distanceMeters ?? PHP_INT_MAX) / 1000;

                Log::info("Kurye #{$courier->id}: restoran mesafesi {$distKm} km (limit: {$distLimit} km)");

                return $distKm <= $distLimit;
            });

            if ($couriersWithinRange->isEmpty()) {
                Log::warning("Sipariş #{$order->id} için uygun mesafede kurye bulunamadı (limit: {$distLimit} km).");
                continue;
            }

            // 2. Adım: Mesafe içindeki kuryeler arasından en uzun bekleyeni seç (adil rotasyon)
            $assignedCourier = $couriersWithinRange->sortBy('last_assigned_at')->first();

            // --- ATAMA İŞLEMİ ---
            try {
                $order->update([
                    'courier_id'  => $assignedCourier->id,
                    'assigned_at' => now(),
                ]);

                $assignedCourier->update(['last_assigned_at' => now()]);

                CourierOrder::firstOrCreate([
                    'courier_id' => $assignedCourier->id,
                    'order_id'   => $order->id,
                ]);

                // Atama sonrası aktif paket sayısını kontrol et
                $activePackagesCount = Order::where('courier_id', $assignedCourier->id)
                    ->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::UNSUPPLIED])
                    ->count();

                if ($activePackagesCount >= $maxPackageLimit) {
                    $assignedCourier->update(['status' => CourierStatus::service]);
                    Log::info("Kurye #{$assignedCourier->id} paket limitine ulaştı ({$maxPackageLimit}): servis moduna alındı.");
                }

                CheckCourierTimeoutJob::dispatch($order->id)->delay(now()->addMinutes(2));

                if ($assignedCourier->fcm_token) {
                    (new PushNotificationService())->sendNotification(
                        $assignedCourier->fcm_token,
                        ($restaurant->restaurant_name ?? $restaurant->name) . ' - Yeni Sipariş',
                        'Takip: ' . $order->tracking_id
                    );
                }

                Log::info("BAŞARILI: Sipariş #{$order->id} → Kurye #{$assignedCourier->id}");

            } catch (\Exception $e) {
                Log::error("Atama Hatası (Sipariş #{$order->id}): " . $e->getMessage());
            }
        }

        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ TAMAMLANDI ---');
    }
}
