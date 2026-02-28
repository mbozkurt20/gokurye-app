<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $admin      = Auth::guard('admin')->user();
        $startDate  = $request->input('start_date', Carbon::today()->toDateString());
        $endDate    = $request->input('end_date', Carbon::today()->toDateString());

        $defaultRates = [
            'telefon'     => 0,
            'getir'       => 12,
            'trendyol'    => 12,
            'yemeksepeti' => 15,
            'migros'      => 10,
        ];

        $commissions = $admin->platform_commissions ?? $defaultRates;

        $platformKeywords = [
            'telefon'     => ['telefon', 'TELEFON', 'Telefon'],
            'getir'       => ['getir', 'Getir', 'GETIR'],
            'trendyol'    => ['trendyol', 'Trendyol', 'TRENDYOL'],
            'yemeksepeti' => ['yemeksepeti', 'Yemeksepeti', 'YEMEKSEPETI'],
            'migros'      => ['migros', 'Migros', 'MIGROS'],
        ];

        $platformLabels = [
            'telefon'     => 'Telefon Sipariş',
            'getir'       => 'Getir',
            'trendyol'    => 'Trendyol',
            'yemeksepeti' => 'Yemeksepeti',
            'migros'      => 'Migros',
        ];

        $restaurantIds = DB::table('restaurants')
            ->where('admin_id', $admin->id)
            ->pluck('id');

        $orderRows = DB::table('orders')
            ->whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'DELIVERED')
            ->select('platform', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as gross'))
            ->groupBy('platform')
            ->get();

        $results = [];
        foreach ($platformKeywords as $key => $keywords) {
            $gross = 0;
            $count = 0;
            foreach ($orderRows as $row) {
                foreach ($keywords as $kw) {
                    if (stripos($row->platform, $kw) !== false) {
                        $gross += (float) $row->gross;
                        $count += (int) $row->count;
                        break;
                    }
                }
            }
            $rate       = (float) ($commissions[$key] ?? 0);
            $commission = $gross * ($rate / 100);
            $results[]  = [
                'key'        => $key,
                'label'      => $platformLabels[$key],
                'count'      => $count,
                'gross'      => $gross,
                'rate'       => $rate,
                'commission' => $commission,
                'net'        => $gross - $commission,
            ];
        }

        $totals = [
            'count'      => array_sum(array_column($results, 'count')),
            'gross'      => array_sum(array_column($results, 'gross')),
            'commission' => array_sum(array_column($results, 'commission')),
            'net'        => array_sum(array_column($results, 'net')),
        ];

        // Daily breakdown for chart (last 30 days)
        $dailyRows = DB::table('orders')
            ->whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'DELIVERED')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as gross'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.commissions.index', compact(
            'results', 'totals', 'commissions', 'startDate', 'endDate', 'dailyRows'
        ));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $rates = $request->input('rates', []);
        $admin->platform_commissions = $rates;
        $admin->save();

        return redirect()->back()->with('success', 'Komisyon oranları güncellendi.');
    }
}
