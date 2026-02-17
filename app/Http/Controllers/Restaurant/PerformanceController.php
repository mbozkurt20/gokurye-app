<?php

namespace App\Http\Controllers\Restaurant;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function home(Request $request)
    {
        $userId = Auth::user()->id;

        // Filtreleme mantığı: Varsayılan olarak son 30 gün
        $startDateStr = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDateStr = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $startTime = Carbon::parse($startDateStr)->startOfDay();
        $endTime = Carbon::parse($endDateStr)->endOfDay();

        // Ortak Veriler (Kurye listeleri, platform bazlı sayılar vb.)
        $commonData = $this->getCommonData($startTime, $endTime);

        // Hız Verilerini Al (Log tablosu üzerinden)
        $dailyPreparedSpeed = $this->getPreparedSpeed($userId, $startDateStr, $endDateStr);
        $dailyHandoverSpeed = $this->getHandoverSpeed($userId, $startDateStr, $endDateStr);
        $dailyDeliverySpeed = $this->getDeliverySpeed($userId, $startDateStr, $endDateStr);

        // Grafik için Tarih Dizisi Oluşturma
        $allDates = [];
        $tempDate = Carbon::parse($startDateStr);
        while ($tempDate <= Carbon::parse($endDateStr)) {
            $allDates[] = $tempDate->format('Y-m-d');
            $tempDate->addDay();
        }

        // ChartJS Formatına Dönüştürme
        $chartData = [
            'labels' => $allDates,
            'datasets' => [
                [
                    'label' => 'Hazırlanma (dk)',
                    'data' => $this->mapDataToDates($dailyPreparedSpeed, $allDates),
                    'borderColor' => '#4927b3',
                    'backgroundColor' => 'rgba(73, 39, 179, 0.1)',
                ],
                [
                    'label' => 'Teslim Alma (dk)',
                    'data' => $this->mapDataToDates($dailyHandoverSpeed, $allDates),
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(236, 105, 30, 0.1)',
                ],
                [
                    'label' => 'Teslimat (dk)',
                    'data' => $this->mapDataToDates($dailyDeliverySpeed, $allDates),
                    'borderColor' => '#30d760',
                    'backgroundColor' => 'rgba(48, 215, 96, 0.1)',
                ]
            ]
        ];

        // Kartlar için İstatistikler
        $stats = [
            'prepared' => $this->calculateStats($dailyPreparedSpeed),
            'handover' => $this->calculateStats($dailyHandoverSpeed),
            'delivery' => $this->calculateStats($dailyDeliverySpeed)
        ];

        // Aktif ve Günlük Siparişler
        $tumu = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')->get();

        return view('restaurant.home', array_merge($commonData, compact(
            'tumu', 'ActiveSiparisler', 'chartData', 'stats',
            'startDateStr', 'endDateStr', 'userId'
        )));
    }

    // --- Ölçüm Metotları ---

    private function getPreparedSpeed($userId, $start, $end) {
        return DB::table('order_status_logs as s')
            ->join('order_status_logs as e', 's.order_id', '=', 'e.order_id')
            ->join('orders as o', 'o.id', '=', 's.order_id')
            ->select(
                DB::raw('DATE(s.changed_at) as date'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(SECOND, s.changed_at, e.changed_at)) / 60, 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('s.status', OrderStatus::PREPARING)
            ->where('e.status', OrderStatus::PREPARED)
            ->where('o.restaurant_id', $userId)
            ->whereBetween('s.changed_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('date')->get();
    }

    private function getHandoverSpeed($userId, $start, $end) {
        return DB::table('order_status_logs as s')
            ->join('order_status_logs as e', 's.order_id', '=', 'e.order_id')
            ->join('orders as o', 'o.id', '=', 's.order_id')
            ->select(
                DB::raw('DATE(s.changed_at) as date'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(SECOND, s.changed_at, e.changed_at)) / 60, 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('s.status', OrderStatus::ASSIGNED)
            ->where('e.status', OrderStatus::HANDOVER)
            ->where('o.restaurant_id', $userId)
            ->whereBetween('s.changed_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('date')->get();
    }

    private function getDeliverySpeed($userId, $start, $end) {
        return DB::table('order_status_logs as s')
            ->join('order_status_logs as e', 's.order_id', '=', 'e.order_id')
            ->join('orders as o', 'o.id', '=', 's.order_id')
            ->select(
                DB::raw('DATE(s.changed_at) as date'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(SECOND, s.changed_at, e.changed_at)) / 60, 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('s.status', OrderStatus::HANDOVER)
            ->where('e.status', OrderStatus::DELIVERED)
            ->where('o.restaurant_id', $userId)
            ->whereBetween('s.changed_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('date')->get();
    }

    // --- Yardımcı Metotlar ---

    private function calculateStats($data) {
        if ($data->isEmpty()) return ['avg' => 0, 'min' => 0, 'max' => 0, 'total_orders' => 0];
        return [
            'avg' => round($data->avg('avg_minutes'), 2),
            'min' => round($data->min('avg_minutes'), 2),
            'max' => round($data->max('avg_minutes'), 2),
            'total_orders' => $data->sum('order_count')
        ];
    }

    private function mapDataToDates($data, $allDates) {
        $mapped = [];
        $dataByDate = $data->keyBy('date');
        foreach ($allDates as $date) {
            $mapped[] = $dataByDate->has($date) ? (float)$dataByDate[$date]->avg_minutes : 0;
        }
        return $mapped;
    }

    private function getCommonData($startDate, $endDate) {
        $userId = Auth::user()->id;
        // ... (Paylaştığınız getCommonData içeriği buraya gelecek)
        // Önemli: view'a gönderilen tüm platform sayıları ve kurye verileri burada toplanıyor.
        return [ /* Veriler */ ];
    }

    public function filterByDate(Request $request)
    {
        $startTime = Carbon::parse($request->start_date)->startOfDay();
        $endTime = Carbon::parse($request->end_date)->endOfDay();
        $userId = Auth::user()->id;

        $commonData = $this->getCommonData($startTime, $endTime);

        $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        // Verileri al
        $dailyPreparedSpeed = $this->getPreparedSpeed($userId, $startDate, $endDate);
        $dailyHandoverSpeed = $this->getHandoverSpeed($userId, $startDate, $endDate);
        $dailyDeliverySpeed = $this->getDeliverySpeed($userId, $startDate, $endDate);

        // Tüm tarihleri içeren bir dizi oluştur
        $allDates = [];
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);

        while ($currentDate <= $endDateObj) {
            $allDates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Chart verilerini hazırla
        $chartData = [
            'labels' => $allDates,
            'datasets' => [
                [
                    'label' => 'Hazırlanma Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyPreparedSpeed, $allDates),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Teslim Alma Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyHandoverSpeed, $allDates),
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                ],
                [
                    'label' => 'Teslimat Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyDeliverySpeed, $allDates),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ]
            ]
        ];

        // İstatistikleri hesapla
        $stats = [
            'prepared' => $this->calculateStats($dailyPreparedSpeed),
            'handover' => $this->calculateStats($dailyHandoverSpeed),
            'delivery' => $this->calculateStats($dailyDeliverySpeed)
        ];

        $tumu = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant.home', array_merge($commonData, compact(
            'tumu',
            'ActiveSiparisler',
            'chartData',
            'stats',
            'startDate',
            'endDate',
            'userId',
            'dailyPreparedSpeed',
            'dailyHandoverSpeed',
            'dailyDeliverySpeed'
        )));
    }

    public function filterOrders(Request $request)
    {
        $dateFilter = $request->input('date');
        switch ($dateFilter) {
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'today':
            default:
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
        }

        $userId = Auth::user()->id;
        $commonData = $this->getCommonData($startDate, $endDate);

        $tumu = Order::where('restaurant_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $orders = Order::where('restaurant_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();


        // Verileri al
        $dailyPreparedSpeed = $this->getPreparedSpeed($userId, $startDate, $endDate);
        $dailyHandoverSpeed = $this->getHandoverSpeed($userId, $startDate, $endDate);
        $dailyDeliverySpeed = $this->getDeliverySpeed($userId, $startDate, $endDate);

        // Tüm tarihleri içeren bir dizi oluştur
        $allDates = [];
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);

        while ($currentDate <= $endDateObj) {
            $allDates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Chart verilerini hazırla
        $chartData = [
            'labels' => $allDates,
            'datasets' => [
                [
                    'label' => 'Hazırlanma Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyPreparedSpeed, $allDates),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Teslim Alma Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyHandoverSpeed, $allDates),
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                ],
                [
                    'label' => 'Teslimat Hızı (dakika)',
                    'data' => $this->mapDataToDates($dailyDeliverySpeed, $allDates),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ]
            ]
        ];

        // İstatistikleri hesapla
        $stats = [
            'prepared' => $this->calculateStats($dailyPreparedSpeed),
            'handover' => $this->calculateStats($dailyHandoverSpeed),
            'delivery' => $this->calculateStats($dailyDeliverySpeed)
        ];

        return view('restaurant.home', array_merge($commonData, compact('tumu', 'orders',
            'tumu',
            'ActiveSiparisler',
            'chartData',
            'stats',
            'startDate',
            'endDate',
            'userId',
            'dailyPreparedSpeed',
            'dailyHandoverSpeed',
            'dailyDeliverySpeed'
        )));
    }
}
