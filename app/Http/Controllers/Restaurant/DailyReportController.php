<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = Auth::user()->id;
        $date = $request->input('date', Carbon::today()->toDateString());

        $orders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->whereDate('created_at', $date)
            ->get();

        $totalCount  = $orders->count();
        $totalAmount = $orders->sum('amount');

        // Platform breakdown
        $byPlatform = $orders->groupBy('platform')->map(function ($rows, $platform) {
            return [
                'platform' => $platform,
                'count'    => $rows->count(),
                'amount'   => $rows->sum('amount'),
            ];
        })->values();

        // Payment method breakdown
        $byPayment = $orders->groupBy('payment_method')->map(function ($rows, $method) {
            return [
                'method' => $method,
                'count'  => $rows->count(),
                'amount' => $rows->sum('amount'),
            ];
        })->values();

        // Top products from items JSON
        $productSales = [];
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : (array)$order->items;
            if (!is_array($items)) continue;
            foreach ($items as $item) {
                $name = $item['name'] ?? ($item['product_name'] ?? 'Bilinmiyor');
                $qty  = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
                if (!isset($productSales[$name])) {
                    $productSales[$name] = ['name' => $name, 'qty' => 0];
                }
                $productSales[$name]['qty'] += $qty;
            }
        }
        usort($productSales, fn($a, $b) => $b['qty'] - $a['qty']);
        $topProducts = array_slice($productSales, 0, 10);

        // Hourly distribution
        $hourly = [];
        for ($h = 0; $h < 24; $h++) {
            $hourly[$h] = 0;
        }
        foreach ($orders as $order) {
            $h = (int)Carbon::parse($order->created_at)->format('H');
            $hourly[$h]++;
        }

        return view('restaurant.daily-report.index', compact(
            'date', 'totalCount', 'totalAmount', 'byPlatform', 'byPayment', 'topProducts', 'hourly'
        ));
    }
}
