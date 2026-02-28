<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function index()
    {
        $adminId = Auth::guard('admin')->id();
        $period30 = Carbon::now()->subDays(30);
        $period7  = Carbon::now()->subDays(7);

        // Restoran hazırlanma süresi - son 30 gün
        $restaurantStats = DB::table('order_status_logs')
            ->join('restaurants', 'order_status_logs.restaurant_id', '=', 'restaurants.id')
            ->where('restaurants.admin_id', $adminId)
            ->where('order_status_logs.changed_at', '>=', $period30)
            ->where('order_status_logs.status', 'PENDING')
            ->whereNotNull('order_status_logs.duration_seconds')
            ->select(
                'restaurants.restaurant_name',
                'order_status_logs.restaurant_id',
                DB::raw('ROUND(AVG(order_status_logs.duration_seconds / 60), 1) as avg_prepared_min'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('order_status_logs.restaurant_id', 'restaurants.restaurant_name')
            ->orderByDesc('avg_prepared_min')
            ->get();

        $overallAvgPrepared = $restaurantStats->avg('avg_prepared_min') ?: 0;

        // Kurye aktif/mola/servis süreleri - son 7 gün
        $courierStats = DB::table('courier_status_movements')
            ->join('couriers', 'courier_status_movements.courier_id', '=', 'couriers.id')
            ->where('couriers.admin_id', $adminId)
            ->where('courier_status_movements.started_at', '>=', $period7)
            ->whereNotNull('courier_status_movements.duration_seconds')
            ->select(
                'couriers.name as courier_name',
                'courier_status_movements.courier_id',
                DB::raw('ROUND(SUM(CASE WHEN courier_status_movements.status = "active"  THEN courier_status_movements.duration_seconds ELSE 0 END) / 60, 0) as active_min'),
                DB::raw('ROUND(SUM(CASE WHEN courier_status_movements.status = "break"   THEN courier_status_movements.duration_seconds ELSE 0 END) / 60, 0) as break_min'),
                DB::raw('ROUND(SUM(CASE WHEN courier_status_movements.status = "service" THEN courier_status_movements.duration_seconds ELSE 0 END) / 60, 0) as service_min')
            )
            ->groupBy('courier_status_movements.courier_id', 'couriers.name')
            ->orderByDesc('break_min')
            ->get();

        // Öneriler
        $suggestions = [];

        // Restoran önerileri
        foreach ($restaurantStats as $r) {
            if ($overallAvgPrepared > 0 && $r->avg_prepared_min > $overallAvgPrepared * 1.3) {
                $diffPct = round((($r->avg_prepared_min - $overallAvgPrepared) / $overallAvgPrepared) * 100);
                $suggestions[] = [
                    'type'  => 'warning',
                    'icon'  => 'fa-shop',
                    'title' => $r->restaurant_name . ' — Mutfak Hazırlık Gecikmesi',
                    'body'  => "Son 30 günde hazırlanma süresi ortalamanın <strong>%{$diffPct} üzerinde</strong> ({$r->avg_prepared_min} dk). Genel ortalama: " . round($overallAvgPrepared, 1) . " dk. Mutfak kapasitesi veya iş akışı gözden geçirilebilir.",
                ];
            }
            if ($r->total_orders < 5) {
                $suggestions[] = [
                    'type'  => 'info',
                    'icon'  => 'fa-circle-info',
                    'title' => $r->restaurant_name . ' — Düşük Sipariş Hacmi',
                    'body'  => "Son 30 günde yalnızca <strong>{$r->total_orders} sipariş</strong> kaydedildi. Restoranın aktif olup olmadığını kontrol edin.",
                ];
            }
        }

        // Kurye önerileri
        foreach ($courierStats as $c) {
            $total = $c->active_min + $c->break_min + $c->service_min;
            if ($total < 30) continue;

            $breakPct = round(($c->break_min / $total) * 100);

            if ($breakPct > 40) {
                $suggestions[] = [
                    'type'  => 'warning',
                    'icon'  => 'fa-motorcycle',
                    'title' => $c->courier_name . ' — Yüksek Mola Oranı',
                    'body'  => "Son 7 günde toplam çalışma süresinin <strong>%{$breakPct}'ini molada</strong> geçirdi ({$c->break_min} dk mola / {$total} dk toplam). Aktif süre: {$c->active_min} dk.",
                ];
            } elseif ($c->active_min < 20 && $total > 120) {
                $suggestions[] = [
                    'type'  => 'danger',
                    'icon'  => 'fa-triangle-exclamation',
                    'title' => $c->courier_name . ' — Çok Düşük Aktif Süre',
                    'body'  => "Son 7 günde yalnızca <strong>{$c->active_min} dk aktif</strong> görüldü ({$total} dk kayıtlı süreden). Kurye durumu kontrol edilmeli.",
                ];
            }
        }

        if (empty($suggestions)) {
            $suggestions[] = [
                'type'  => 'success',
                'icon'  => 'fa-circle-check',
                'title' => 'Her Şey Yolunda!',
                'body'  => 'Son 30 günde dikkat gerektiren bir performans anomalisi tespit edilmedi.',
            ];
        }

        return view('admin.insights.index', compact(
            'restaurantStats', 'overallAvgPrepared', 'courierStats', 'suggestions'
        ));
    }
}
