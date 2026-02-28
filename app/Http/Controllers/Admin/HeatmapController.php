<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HeatmapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $days  = (int) $request->input('days', 30);

        $restaurantIds = DB::table('restaurants')
            ->where('admin_id', $admin->id)
            ->pluck('id');

        $points = DB::table('orders')
            ->join('customer_addresses', function ($join) {
                $join->on('customer_addresses.customer_id', '=', 'orders.customer_id')
                     ->on('customer_addresses.restaurant_id', '=', 'orders.restaurant_id');
            })
            ->whereIn('orders.restaurant_id', $restaurantIds)
            ->where('orders.status', 'DELIVERED')
            ->whereNotNull('customer_addresses.latitude')
            ->whereNotNull('customer_addresses.longitude')
            ->where('customer_addresses.latitude', '!=', '')
            ->where('customer_addresses.longitude', '!=', '')
            ->where('orders.created_at', '>=', Carbon::now()->subDays($days))
            ->select(
                'customer_addresses.latitude',
                'customer_addresses.longitude',
                'customer_addresses.mahalle'
            )
            ->limit(1000)
            ->get();

        // Mahalle frequency for top zones
        $zoneFreq = $points
            ->filter(fn($p) => !empty($p->mahalle))
            ->groupBy('mahalle')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(10);

        return view('admin.heatmap.index', compact('points', 'days', 'zoneFreq'));
    }
}
