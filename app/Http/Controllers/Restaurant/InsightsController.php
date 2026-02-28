<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function index()
    {
        $userId  = Auth::user()->id;
        $period30 = Carbon::now()->subDays(30);

        // 30 günlük genel ortalama
        $rawAvg     = DB::table('order_status_logs')
            ->where('restaurant_id', $userId)
            ->where('status', 'PENDING')
            ->whereNotNull('duration_seconds')
            ->where('changed_at', '>=', $period30)
            ->avg('duration_seconds');
        $overallAvg = $rawAvg ? round($rawAvg / 60, 1) : 0;

        // Saatlik dağılım
        $hourlyPattern = DB::table('order_status_logs')
            ->where('restaurant_id', $userId)
            ->where('status', 'PENDING')
            ->whereNotNull('duration_seconds')
            ->where('changed_at', '>=', $period30)
            ->select(
                DB::raw('HOUR(changed_at) as hour'),
                DB::raw('ROUND(AVG(duration_seconds / 60), 1) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Gün bazlı dağılım
        $dayPattern = DB::table('order_status_logs')
            ->where('restaurant_id', $userId)
            ->where('status', 'PENDING')
            ->whereNotNull('duration_seconds')
            ->where('changed_at', '>=', $period30)
            ->select(
                DB::raw('DAYOFWEEK(changed_at) as day'),
                DB::raw('ROUND(AVG(duration_seconds / 60), 1) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Platform bazlı dağılım
        $platformPattern = DB::table('order_status_logs')
            ->join('orders', 'orders.id', '=', 'order_status_logs.order_id')
            ->where('order_status_logs.restaurant_id', $userId)
            ->where('order_status_logs.status', 'PENDING')
            ->whereNotNull('order_status_logs.duration_seconds')
            ->where('order_status_logs.changed_at', '>=', $period30)
            ->select(
                'orders.platform',
                DB::raw('ROUND(AVG(order_status_logs.duration_seconds / 60), 1) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('orders.platform')
            ->orderByDesc('avg_minutes')
            ->get();

        // Trend: son 7 gün vs önceki 7 gün
        $rawLast7 = DB::table('order_status_logs')
            ->where('restaurant_id', $userId)->where('status', 'PENDING')
            ->whereNotNull('duration_seconds')
            ->where('changed_at', '>=', Carbon::now()->subDays(7))
            ->avg('duration_seconds');
        $last7Avg = $rawLast7 ? round($rawLast7 / 60, 1) : 0;

        $rawPrev7 = DB::table('order_status_logs')
            ->where('restaurant_id', $userId)->where('status', 'PENDING')
            ->whereNotNull('duration_seconds')
            ->whereBetween('changed_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])
            ->avg('duration_seconds');
        $prev7Avg = $rawPrev7 ? round($rawPrev7 / 60, 1) : 0;

        $trendChange = $prev7Avg > 0 ? round((($last7Avg - $prev7Avg) / $prev7Avg) * 100) : 0;

        // Öneriler oluştur
        $suggestions = [];
        $dayNames = [1 => 'Pazar', 2 => 'Pazartesi', 3 => 'Salı', 4 => 'Çarşamba', 5 => 'Perşembe', 6 => 'Cuma', 7 => 'Cumartesi'];

        // Trend önerisi
        if ($trendChange > 15) {
            $suggestions[] = [
                'type'  => 'warning',
                'icon'  => 'fa-arrow-trend-up',
                'title' => 'Hazırlanma Süresi Artış Eğiliminde',
                'body'  => "Son 7 günde hazırlanma süreniz bir önceki haftaya kıyasla <strong>%{$trendChange} arttı</strong> (önceki: {$prev7Avg} dk → son: {$last7Avg} dk). Yoğun dönemlerde mutfak kapasitesini artırmayı düşünebilirsiniz.",
            ];
        } elseif ($trendChange < -10) {
            $absChange = abs($trendChange);
            $suggestions[] = [
                'type'  => 'success',
                'icon'  => 'fa-arrow-trend-down',
                'title' => 'Hazırlanma Süresi İyileşiyor',
                'body'  => "Son 7 günde hazırlanma süreniz <strong>%{$absChange} azaldı</strong> ({$prev7Avg} dk → {$last7Avg} dk). Harika ilerleme!",
            ];
        }

        // Saatlik yavaş dönem önerileri
        if ($overallAvg > 0) {
            $slowHours = $hourlyPattern
                ->filter(fn($h) => $h->avg_minutes > $overallAvg * 1.3 && $h->order_count >= 3)
                ->sortByDesc('avg_minutes')
                ->take(3);

            foreach ($slowHours as $h) {
                $pct = round((($h->avg_minutes - $overallAvg) / $overallAvg) * 100);
                $suggestions[] = [
                    'type'  => 'warning',
                    'icon'  => 'fa-clock',
                    'title' => "Saat {$h->hour}:00–" . ($h->hour + 1) . ":00 — Yoğun Dönem",
                    'body'  => "Bu saatteki hazırlanma süreniz ortalamanın <strong>%{$pct} üzerinde</strong> ({$h->avg_minutes} dk). Bu saatlerde ek mutfak personeli ile önceden hazırlık yapabilirsiniz.",
                ];
            }
        }

        // Gün bazlı yavaş dönem önerileri
        if ($overallAvg > 0) {
            $slowDays = $dayPattern
                ->filter(fn($d) => $d->avg_minutes > $overallAvg * 1.25 && $d->order_count >= 5)
                ->sortByDesc('avg_minutes')
                ->take(2);

            foreach ($slowDays as $d) {
                $dayName = $dayNames[$d->day] ?? "Gün {$d->day}";
                $pct = round((($d->avg_minutes - $overallAvg) / $overallAvg) * 100);
                $suggestions[] = [
                    'type'  => 'info',
                    'icon'  => 'fa-calendar-days',
                    'title' => "{$dayName} — Yüksek Hazırlanma Süresi",
                    'body'  => "{$dayName} günleri ortalama hazırlanma süreniz <strong>{$d->avg_minutes} dk</strong> (genel ort. {$overallAvg} dk, %{$pct} üzerinde). Bu günlere özel personel planı oluşturabilirsiniz.",
                ];
            }
        }

        // Platform önerileri
        if ($platformPattern->count() > 1) {
            $platformAvg = $platformPattern->avg('avg_minutes') ?: 0;
            $slowPlatforms = $platformPattern->filter(
                fn($p) => $platformAvg > 0 && $p->avg_minutes > $platformAvg * 1.3 && $p->order_count >= 5
            );
            foreach ($slowPlatforms as $p) {
                $pct = round((($p->avg_minutes - $platformAvg) / $platformAvg) * 100);
                $suggestions[] = [
                    'type'  => 'info',
                    'icon'  => 'fa-layer-group',
                    'title' => strtoupper($p->platform) . " — Platform Gecikmesi",
                    'body'  => "Bu platformdan gelen siparişler diğerlerine kıyasla <strong>%{$pct} daha uzun</strong> hazırlanıyor ({$p->avg_minutes} dk). Platform menüsü veya sipariş yapısı incelenebilir.",
                ];
            }
        }

        if (empty($suggestions)) {
            $suggestions[] = [
                'type'  => 'success',
                'icon'  => 'fa-circle-check',
                'title' => 'Performansınız Mükemmel!',
                'body'  => 'Son 30 günde kayda değer bir gecikme veya anomali tespit edilmedi. Böyle devam edin!',
            ];
        }

        return view('restaurant.insights.index', compact(
            'overallAvg', 'hourlyPattern', 'dayPattern', 'platformPattern',
            'last7Avg', 'prev7Avg', 'trendChange', 'suggestions'
        ));
    }
}
