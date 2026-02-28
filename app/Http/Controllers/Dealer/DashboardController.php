<?php

namespace App\Http\Controllers\Dealer;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\District;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function home()
    {
        $startTime = Carbon::today()->startOfDay();
        $endTime   = Carbon::today()->endOfDay();

        $dealerId = Auth::guard('dealer')->id();
        $adminIds = Admin::where('created_by_id', $dealerId)->where('created_by_type', 'dealer')->pluck('id')->toArray();

        $couriers = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::active)->where('restaurant_id', 0)->get();

        $tumu = Order::whereDate('created_at', Carbon::today())->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->sum('amount');

        $formattedExpense = number_format($totalExpense, 2, '.', ',');

        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->avg('amount');

        $formattedAverageExpense = number_format($averageExpense ?? 0, 2, '.', ',');

        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->count();

        $totalCouriers   = Courier::whereIn('admin_id', $adminIds)->count();
        $idleCouriers    = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::active)->count();
        $breakCouriers   = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::break)->count();
        $serviceCouriers = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::service)->count();

        return view('dealer.home', compact(
            'totalCouriers', 'serviceCouriers', 'idleCouriers', 'breakCouriers',
            'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense',
            'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol',
            'couriers', 'migros', 'teslimEdilenSiparisler'
        ));
    }
    public function orders()
    {
        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $dealerId = Auth::guard('dealer')->id();
        $adminIds = Admin::where('created_by_id', $dealerId)->where('created_by_type')->pluck('id')->toArray();

        $couriers = Courier::whereIn('admin_id',$adminIds)->where('status', 'active')->where('restaurant_id', 0)->get();

        $tumu = Order::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->whereHas('restaurant', function ($query) use($adminIds) {
           return $query->whereIn('admin_id',$adminIds);
        })->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->whereHas('restaurant', function ($query) use($adminIds) {
               return $query->whereIn('admin_id',$adminIds);
            })->get();

        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function ($query) use($adminIds) {
           return $query->whereIn('admin_id',$adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')
            ->whereHas('restaurant', function ($query) use($adminIds) {
               return $query->whereIn('admin_id',$adminIds);
            })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')
            ->whereHas('restaurant', function ($query) use($adminIds) {
               return $query->whereIn('admin_id',$adminIds);
            }) ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')
            ->whereHas('restaurant', function ($query) use($adminIds) {
               return $query->whereIn('admin_id',$adminIds);
            })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($query) use($adminIds) {
           return $query->whereIn('admin_id',$adminIds);
        })->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($query) use($adminIds) {
           return $query->whereIn('admin_id',$adminIds);
        })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', OrderStatus::DELIVERED)->whereHas('restaurant', function ($query) use($adminIds) {
           return $query->whereIn('admin_id',$adminIds);
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::whereIn('admin_id',$adminIds)->count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::whereIn('admin_id',$adminIds)->where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::whereIn('admin_id',$adminIds)->where('status', CourierStatus::break)->count();

        return view('dealer.orders.index', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }

    public function filterByDate(Request $request)
    {
        // Başlangıç ve bitiş tarihlerini al
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $dealerId = Auth::guard('dealer')->id();
        $adminIds = Admin::where('created_by_id', $dealerId)->where('created_by_type', 'dealer')->pluck('id')->toArray();

        $couriers = Courier::whereIn('admin_id', $adminIds)->where('status', 'active')->where('restaurant_id', 0)->get();

        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();

        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->count();

        $totalCouriers   = Courier::whereIn('admin_id', $adminIds)->count();
        $idleCouriers    = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::active)->count();
        $breakCouriers   = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::break)->count();
        $serviceCouriers = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::service)->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');

        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense ?? 0, 2, '.', ',');

        return view('dealer.home', compact(
            'totalCouriers', 'idleCouriers', 'breakCouriers', 'serviceCouriers',
            'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense',
            'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol',
            'couriers', 'migros', 'startDate', 'endDate'
        ));
    }

    public function filterOrders(Request $request)
    {
        // Tarihe göre aralıkları belirleyelim
        // Tarih filtresini al
        $dateFilter = $request->input('date');
        switch ($dateFilter) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
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
            default:
                // Varsayılan olarak bugünün verilerini döndür
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
        }
        $dealerId = Auth::guard('dealer')->id();
        $adminIds = Admin::where('created_by_id', $dealerId)->where('created_by_type', 'dealer')->pluck('id')->toArray();

        $couriers = Courier::whereIn('admin_id', $adminIds)->where('status', 'active')->where('restaurant_id', 0)->get();

        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) {
            $q->whereIn('admin_id', $adminIds);
        })->orderBy('created_at', 'desc')->get();

        $yemeksepeti    = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $getiryemek     = Order::where('platform', 'getir')->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol       = Order::where('platform', 'trendyol')->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros         = Order::where('platform', 'migros')->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->whereBetween('created_at', [$startDate, $endDate])->count();

        $totalExpense            = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->sum('amount');
        $formattedExpense        = number_format($totalExpense, 2, '.', ',');
        $averageExpense          = Order::whereBetween('created_at', [$startDate, $endDate])->whereHas('restaurant', function ($q) use ($adminIds) { $q->whereIn('admin_id', $adminIds); })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense ?? 0, 2, '.', ',');

        $totalCouriers   = Courier::whereIn('admin_id', $adminIds)->count();
        $idleCouriers    = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::active)->count();
        $breakCouriers   = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::break)->count();
        $serviceCouriers = Courier::whereIn('admin_id', $adminIds)->where('status', CourierStatus::service)->count();

        return view('dealer.home', compact(
            'totalCouriers', 'idleCouriers', 'breakCouriers', 'serviceCouriers',
            'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense',
            'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol',
            'couriers', 'migros'
        ));
    }


    public function profile()
    {
        $auth = Auth::guard('dealer')->user();
        return view('dealer.profile', compact('auth'));
    }
    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('dealer')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->name = $request->input('name');
        $auth->email = $request->input('email');
        $auth->update();

        return redirect()->back()->with('success', 'Bilgileriniz Güncellenmiştir.');
    }

    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }
}
