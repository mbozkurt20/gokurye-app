<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\Courier;
use App\Models\District;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function home()
    {
        $today = Carbon::today();

        // System-wide KPIs
        $totalAdmins      = Admin::count();
        $activeAdmins     = Admin::where('is_active', true)->count();
        $totalRestaurants = Restaurant::count();
        $totalCouriers    = Courier::count();
        $idleCouriers     = Courier::where('status', CourierStatus::active)->count();
        $serviceCouriers  = Courier::where('status', CourierStatus::service)->count();
        $breakCouriers    = Courier::where('status', CourierStatus::break)->count();
        $totalDealers     = User::count();

        // Today's system-wide orders
        $tumu           = Order::whereDate('created_at', $today)->orderBy('created_at', 'desc')->get();
        $totalExpense   = Order::whereDate('created_at', $today)->sum('amount');
        $averageExpense = count($tumu) > 0 ? $totalExpense / count($tumu) : 0;
        $formattedExpense        = number_format($totalExpense, 2, '.', ',');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler  = Order::where('status', 'DELIVERED')->whereDate('created_at', $today)->count();

        // Platform breakdown (today, system-wide)
        $yemeksepeti    = Order::where('platform', 'yemeksepeti')->whereDate('created_at', $today)->get();
        $getiryemek     = Order::where('platform', 'getir')->whereDate('created_at', $today)->get();
        $trendyol       = Order::where('platform', 'trendyol')->whereDate('created_at', $today)->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereDate('created_at', $today)->get();
        $migros         = Order::where('platform', 'migros')->whereDate('created_at', $today)->count();

        // Admins with per-admin stats
        $admins = Admin::orderBy('is_active', 'desc')->orderBy('name')->get();
        foreach ($admins as $admin) {
            $restaurantIds             = Restaurant::where('admin_id', $admin->id)->pluck('id');
            $admin->restaurants_count  = $restaurantIds->count();
            $admin->couriers_count     = Courier::where('admin_id', $admin->id)->count();
            $admin->active_couriers    = Courier::where('admin_id', $admin->id)->where('status', CourierStatus::active)->count();
            $admin->today_orders       = Order::whereDate('created_at', $today)->whereIn('restaurant_id', $restaurantIds)->count();
            $admin->today_revenue      = Order::whereDate('created_at', $today)->whereIn('restaurant_id', $restaurantIds)->sum('amount');
        }

        // Dealers with admin counts
        $dealers = User::withCount(['admins'])->orderBy('is_active', 'desc')->get();

        // Unassigned active couriers (legacy)
        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();

        return view('superadmin.home', compact(
            'totalAdmins', 'activeAdmins', 'totalRestaurants', 'totalCouriers',
            'idleCouriers', 'serviceCouriers', 'breakCouriers', 'totalDealers',
            'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'telefonsiparis', 'migros',
            'totalExpense', 'averageExpense', 'formattedExpense', 'formattedAverageExpense',
            'teslimEdilenSiparisler', 'admins', 'dealers', 'couriers'
        ));
    }
    public function getCourier()
    {
        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->where('status',CourierStatus::active)
            ->get();

        return response()->json($couriers);
    }
    public function dealer()
    {
        $dealers = User::withCount(['admins'])
            ->orderBy('is_active', 'asc')
            ->get();
        return view('superadmin.dealer.index', compact('dealers'));
    }
    public function ajax(Request $request)
    {
        $tumu = Order::whereDate('created_at', Carbon::today())
           ->orderBy('created_at', 'desc')->with(['restaurant','courier'])->get();

        // Tüm siparişleri statülerine göre tek seferde grupla (Performans için kritik)
        $grouped = $tumu->groupBy('status');

        return response()->json([
            'pending'          => $grouped->get(OrderStatus::PENDING, collect())->values(),
            'prepared'         => $grouped->where('courier_id','!=', -1)->get(OrderStatus::PREPARED, collect())->values(),
            'assigned'         => $grouped->get(OrderStatus::ASSIGNED, collect())->values(),
            'handover'         => $grouped->get(OrderStatus::HANDOVER, collect())->values(),
            'delivered'        => $grouped->get(OrderStatus::DELIVERED, collect())->values(),
            'unsupplied'       => $grouped->get(OrderStatus::UNSUPPLIED, collect())->values(),
        ]);
    }
    public function profile()
    {
        $auth = Auth::guard('superadmin')->user();
        return view('superadmin.profile', compact('auth'));
    }
    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('superadmin')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->name = $request->input('name');
        $auth->email = $request->input('email');
        $auth->update();

        return redirect()->back()->with('success', 'Bilgileriniz Güncellenmiştir.');
    }
    public function deleteDealer($id)
    {
        $user = User::find($id);
        $dleete = $user->delete();
        if ($dleete) {
            echo 'OK';
        } else {
            echo 'ERR';
        }
    }

    public function createDealer()
    {
        $cities = City::all();
        return view('superadmin.dealer.create', compact('cities'));
    }

    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }

    public function createDealerRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:5',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        User::create([
            'is_active'       => true,
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'password'        => Hash::make($request->password),
            'latitude'        => $request->lat,
            'longitude'       => $request->lng,
            'city_id'         => $request->city_id,
            'district_id'     => $request->district_id,
            'address'         => $request->address,
            'commission_rate' => $request->input('commission_rate', 20),
        ]);

        return redirect()->back()->with('success', 'Yeni Bayi Başarıyla Eklendi!');
    }
    public function statusDealer($id)
    {
        $daler = User::find($id);
        $delete = $daler->update([
            'is_active' => !$daler->is_active
        ]);

        if ($delete){
            echo 'OK';
        }else{
            echo 'ERR';
        }
    }
    public function editDealer($id)
    {
        $dealer = User::find($id);
        $cities = City::all();
        return view('superadmin.dealer.edit', compact('dealer', 'cities'));
    }

    public function updateDealer($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:5',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // Admin kaydını bul ve güncelle
        $dealer = User::find($id);

        // Eğer admin kaydı yoksa hata döndürülebilir
        if (!$dealer->exists()) {
            return redirect()->back()->with(['test' => 'Bayi Bulunamadı.']);
        }

        $updateData = [
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'latitude'        => $request->lat,
            'longitude'       => $request->lng,
            'city_id'         => $request->city_id,
            'district_id'     => $request->district_id,
            'address'         => $request->address,
            'commission_rate' => $request->input('commission_rate', 20),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Veritabanında güncelleme işlemi
        $dealer->update($updateData);

        return redirect()->route('superadmin.dealer')->with('message', 'Bayi bilgileri başarıyla güncellendi!');
    }

    public function orders()
    {
        $now = Carbon::now();

        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();

        $tumu = Order::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $getiryemek = Order::where('platform', 'getir')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        return view('superadmin.orders.index', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }

    public function filterByDate(Request $request)
    {
        // Başlangıç ve bitiş tarihlerini al
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->count();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'startDate', 'endDate'));
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
        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        // Seçilen tarih aralığındaki siparişleri filtrele
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'orders', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros'));
    }
}
