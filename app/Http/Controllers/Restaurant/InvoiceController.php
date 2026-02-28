<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /** Tekil sipariş faturası */
    public function order($id)
    {
        $restaurant = Auth::user();

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        abort_if(!$order, 404);

        $items = is_string($order->items) ? json_decode($order->items, true) : (array)$order->items;
        $courier = $order->courier_id ? DB::table('couriers')->where('id', $order->courier_id)->first() : null;

        return view('restaurant.invoices.order', compact('order', 'items', 'restaurant', 'courier'));
    }

    /** Günlük toplu fatura */
    public function daily(Request $request)
    {
        $restaurant = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $orders = DB::table('orders')
            ->where('restaurant_id', $restaurant->id)
            ->where('status', 'DELIVERED')
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get()
            ->map(function ($order) {
                $order->parsedItems = is_string($order->items)
                    ? json_decode($order->items, true)
                    : (array)$order->items;
                return $order;
            });

        $totalAmount  = $orders->sum('amount');
        $totalOrders  = $orders->count();

        return view('restaurant.invoices.daily', compact('orders', 'restaurant', 'date', 'totalAmount', 'totalOrders'));
    }

    /** Fatura listesi */
    public function index(Request $request)
    {
        $restaurant = Auth::user();
        $month = $request->input('month', Carbon::today()->format('Y-m'));

        [$year, $mon] = explode('-', $month);

        $dailySummary = DB::table('orders')
            ->where('restaurant_id', $restaurant->id)
            ->where('status', 'DELIVERED')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $monthTotal = $dailySummary->sum('total');
        $monthCount = $dailySummary->sum('count');

        return view('restaurant.invoices.index', compact(
            'dailySummary', 'month', 'monthTotal', 'monthCount', 'restaurant'
        ));
    }
}
