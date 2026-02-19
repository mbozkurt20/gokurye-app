<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Models\AdminSystemFeature;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\District;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\SystemFeature;
use App\Models\TopupMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.index');
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResource([], 204)
            : redirect('/');
    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        // Önceki davranış yerine doğrudan yönlendirme yapıyoruz
        return redirect()->route('login')
            ->withInput($request->only($this->username(), 'remember'))
            ->with('test', 'Giriş bilgileriniz hatalı. Lütfen tekrar deneyin.');
    }
    protected function guard()
    {
        return Auth::guard('admin');
    }
    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }
    public function ajax(Request $request)
    {
        $tumu = Order::whereDate('created_at', Carbon::today())
            ->whereHas('restaurant', function($query){
                return $query->where('admin_id', auth()->id());
            })->orderBy('created_at', 'asc')->with(['restaurant','courier'])->get();

        // Siparişleri duruma göre ayır
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
    public function topupTalep(REquest $request){
        $topup = TopupMovement::create([
            'admin_id' => Auth::guard('admin')->id(),
            'top_up_price' => 3,
            'top_up' => $request->input('top_up'),
            'type' => 'talep',
            'total_amount' => 3*$request->input('top_up'),
            'created_by_user_id' => Auth::guard('admin')->id(),
            'created_type' => 'admin',
        ]);
    }
    public function profile()
    {
        return view('admin.profile');
    }

    public function notifications()
    {
        Notification::query()->where('admin_id', Auth::guard('admin')->id())->delete();

        return response()->json(['status' => "OK"]);
    }

    public function notificationDelete($id)
    {
        Notification::where('id',$id)->delete();
        return response()->json(['status' => "OK"]);
    }

    public function profileUpdate(Request $request)
    {
        $auth = Admin::find(Auth::guard('admin')->id());

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->latitude = $request->input('latitude');
        $auth->longitude = $request->input('longitude');
        $auth->name = $request->input('name');
        $auth->phone = $request->input('phone');
        $auth->city_id = $request->input('city_id');
        $auth->district_id = $request->input('district_id');
        $auth->distance_limit = $request->input('distance_limit', 50);
        $auth->max_package_limit = $request->input('max_package_limit', 4);
        $auth->update();

        return redirect()->back()->with('message', 'Bilgileriniz Güncellenmiştir.');
    }
    public function balance()
    {
        $movements = TopupMovement::where('admin_id', Auth::guard('admin')->id())->get();
        return view('admin.balance.index', compact('movements'));
    }
    public function home()
    {
        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $couriers = Courier::where('status', 'active')->where('admin_id', auth()->id())->where('restaurant_id', 0)->get();
        $tumu = Order::whereDate('created_at', Carbon::today())->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $gpsyemek = Order::where('platform', 'gpsyemek')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::where('admin_id', auth()->id())->count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->where('admin_id', auth()->id())->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->where('admin_id', auth()->id())->count();
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        return view('admin.home', compact('totalCouriers','serviceCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'gpsyemek','trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }
    public function features()
    {
        $admin = Admin::find(Auth::user()->id);
        $adminFeatures = AdminSystemFeature::where('admin_id', $admin->id)->get();
        $features = SystemFeature::all();
        return view('admin.features', compact('admin','features','adminFeatures'));
    }

    public function featuresUpdate($id)
    {
        $admin = Auth::guard('admin')->id();

        $systemFeature = AdminSystemFeature::where('admin_id', $admin)->where('system_feature_id',$id)->first();
        if ($systemFeature) {
            $systemFeature->delete();
        }else{
            AdminSystemFeature::create([
                'admin_id' => $admin,
                'system_feature_id' => $id,
            ]);
        }

        echo 'OK';
    }

    public function auto_order($status)
    {
        $auto = Admin::where('id', Auth::guard('admin')->user()->id)->first();
        $auto->auto_orders = $status;
        $auto->update();

        if ($auto->auto_orders ){
            echo "Active";
        }else{
            echo "Passive";
        }
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
        $gpsyemek = Order::where('platform', 'gpsyemek')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->count();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        return view('admin.home', compact('totalCouriers', 'idleCouriers', 'gpsyemek','breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros','serviceCouriers'));
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
        $gpsyemek = Order::where('platform', 'gpsyemek')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
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
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('admin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'orders', 'gpsyemek','formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros','serviceCouriers'));
    }

    public function statistics(Request $request)
    {
        $adminId = auth()->id();

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->addDay()->toDateString());
        $groupBy   = $request->input('group_by', 'day'); // day | week

        // 🔹 Siparişler
        $orders = Order::whereHas('restaurant',function ($q) use($adminId) {
            $q->where('admin_id',$adminId);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

        // 🔹 Genel toplamlar
        $totalOrders      = $orders->count();
        $totalSubAmount   = $orders->sum('sub_amount');
        $totalDiscount    = $orders->sum('discount');
        $totalAmount      = $orders->sum('amount');

        // 🔹 Diğer modelleri topla (tek sorgudan)
        $couriers    = Courier::where('admin_id',$adminId)->whereBetween('created_at', [$startDate, $endDate])->get();
        $restaurants = Restaurant::where('admin_id',$adminId)->whereBetween('created_at', [$startDate, $endDate])->get();
        $customers   = Customer::whereHas('restaurant',function ($q) use($adminId) {
            $q->where('admin_id',$adminId);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

        $totalCouriers    = $couriers->count();
        $totalRestaurants = $restaurants->count();
        $totalCustomers   = $customers->count();

        // 🔹 Gruplama fonksiyonu
        $groupFn = function($item) use ($groupBy) {
            if ($groupBy === 'week') {
                return \Carbon\Carbon::parse($item->created_at)->startOfWeek()->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        };

        // 🔹 Dataset fonksiyonu
        $makeDataset = function($collection, $field = null, $sum = false) use ($groupFn) {
            if ($sum) {
                return $collection->groupBy($groupFn)->map(fn($items) => $items->sum($field));
            }
            return $collection->groupBy($groupFn)->map(fn($items) => $items->count());
        };

        // 🔹 Günlük/Haftalık kırılım
        $ordersByDate      = $makeDataset($orders);
        $couriersByDate    = $makeDataset($couriers);
        $restaurantsByDate = $makeDataset($restaurants);
        $customersByDate   = $makeDataset($customers);

        $subAmountByDate = $makeDataset($orders, 'sub_amount', true);
        $discountByDate  = $makeDataset($orders, 'discount', true);
        $amountByDate    = $makeDataset($orders, 'amount', true);

        // 🔹 Boş günleri doldur (eksik gün varsa sıfır yap)
        $period = $groupBy === 'week'
            ? \Carbon\CarbonPeriod::create($startDate, '1 week', $endDate)
            : \Carbon\CarbonPeriod::create($startDate, $endDate);

        $labels = collect($period)->map(fn($d) =>
        $groupBy === 'week' ? $d->startOfWeek()->format('Y-m-d') : $d->format('Y-m-d')
        );

        $fillMissing = function($dataset) use ($labels) {
            return $labels->mapWithKeys(fn($d) => [$d => $dataset[$d] ?? 0]);
        };

        $metrics = [
            ['title' => 'Toplam Sipariş',    'value' => $totalOrders,      'data' => $fillMissing($ordersByDate)],
            ['title' => 'Toplam Kurye',      'value' => $totalCouriers,    'data' => $fillMissing($couriersByDate)],
            ['title' => 'Toplam Restoran',   'value' => $totalRestaurants, 'data' => $fillMissing($restaurantsByDate)],
            ['title' => 'Toplam Müşteri',    'value' => $totalCustomers,   'data' => $fillMissing($customersByDate)],
            ['title' => 'Toplam Ara Toplam', 'value' => number_format($totalSubAmount, 2), 'data' => $fillMissing($subAmountByDate)],
            ['title' => 'Toplam İndirim',    'value' => number_format($totalDiscount, 2),  'data' => $fillMissing($discountByDate)],
            ['title' => 'Toplam Net Tutar',  'value' => number_format($totalAmount, 2),    'data' => $fillMissing($amountByDate)],
        ];

        return view('admin.statistics', compact(
            'startDate', 'endDate', 'groupBy', 'metrics'
        ));
    }
}
