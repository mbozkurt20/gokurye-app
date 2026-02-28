<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = Auth::user()->id;
        $days = (int)$request->input('days', 30);
        $since = Carbon::now()->subDays($days)->toDateTimeString();
        $longAgo = Carbon::now()->subDays(90)->toDateTimeString();

        // All customers of this restaurant
        $customers = DB::table('customers')
            ->where('restaurant_id', $restaurantId)
            ->select('id', 'name', 'phone')
            ->get()
            ->keyBy('id');

        // Order counts per customer (all time)
        $allTimeOrders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'), DB::raw('MAX(created_at) as last_order'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // New customers (first order within period)
        $newCustomerIds = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->whereNotNull('customer_id')
            ->where('created_at', '>=', $since)
            ->select('customer_id', DB::raw('MIN(created_at) as first_order'))
            ->groupBy('customer_id')
            ->havingRaw('MIN(created_at) >= ?', [$since])
            ->pluck('customer_id')
            ->toArray();

        // Returning customers (ordered in period AND have previous orders)
        $periodicCustomers = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'DELIVERED')
            ->whereNotNull('customer_id')
            ->where('created_at', '>=', $since)
            ->pluck('customer_id')
            ->unique()
            ->toArray();
        $returningCustomerIds = array_diff($periodicCustomers, $newCustomerIds);

        // Lost customers (ordered before but not in last 30 days)
        $allCustomerIds = $allTimeOrders->keys()->toArray();
        $lostCustomerIds = array_diff($allCustomerIds, $periodicCustomers);

        // Top 10 customers by order count
        $top10 = $allTimeOrders
            ->sortByDesc('count')
            ->take(10)
            ->map(function ($row) use ($customers) {
                $c = $customers->get($row->customer_id);
                return [
                    'name'       => $c->name ?? 'Silinmiş Müşteri',
                    'phone'      => $c->phone ?? '',
                    'count'      => $row->count,
                    'total'      => $row->total,
                    'last_order' => $row->last_order,
                ];
            })
            ->values();

        $stats = [
            'total'     => $customers->count(),
            'new'       => count($newCustomerIds),
            'returning' => count($returningCustomerIds),
            'lost'      => count($lostCustomerIds),
        ];

        // Daily new customer registrations
        $dailyNew = DB::table('customers')
            ->where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('restaurant.crm.index', compact(
            'stats', 'top10', 'dailyNew', 'days'
        ));
    }
}
