<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = Auth::user()->id;
        $days = (int)$request->input('days', 30);
        $since = Carbon::now()->subDays($days)->toDateTimeString();

        $orders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->where('created_at', '>=', $since)
            ->select('items', 'created_at')
            ->get();

        // Aggregate product sales
        $productSales = [];
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : (array)$order->items;
            if (!is_array($items)) continue;
            foreach ($items as $item) {
                $name  = $item['name'] ?? ($item['product_name'] ?? 'Bilinmiyor');
                $qty   = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
                $price = (float)($item['price'] ?? 0);
                if (!isset($productSales[$name])) {
                    $productSales[$name] = ['name' => $name, 'qty' => 0, 'revenue' => 0];
                }
                $productSales[$name]['qty']     += $qty;
                $productSales[$name]['revenue'] += $qty * $price;
            }
        }
        usort($productSales, fn($a, $b) => $b['qty'] - $a['qty']);
        $maxQty = !empty($productSales) ? $productSales[0]['qty'] : 1;

        // Top 10 + Bottom 10
        $topProducts    = array_slice($productSales, 0, 10);
        $bottomProducts = array_slice(array_reverse($productSales), 0, 10);

        // Daily trend (orders per day)
        $dailyTrend = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Category performance (join products → categories)
        $categoryStats = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.restaurant_id', $restaurantId)
            ->select('categories.name as category_name', DB::raw('COUNT(products.id) as product_count'))
            ->groupBy('categories.name')
            ->get();

        return view('restaurant.menu-performance.index', compact(
            'topProducts', 'bottomProducts', 'dailyTrend', 'categoryStats', 'maxQty', 'days'
        ));
    }
}
