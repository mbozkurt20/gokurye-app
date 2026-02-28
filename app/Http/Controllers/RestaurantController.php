<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Models\AdminSystemFeature;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\SystemFeature;
use App\Models\Customer;
use App\Models\Categorie;
use App\Models\CourierOrder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    use AuthenticatesUsers;

    protected function sendFailedLoginResponse(Request $request)
    {
        // Önceki davranış yerine doğrudan yönlendirme yapıyoruz
        return redirect()->route('login')
            ->withInput($request->only($this->username(), 'remember'))
            ->with('test', 'Giriş bilgileriniz hatalı. Lütfen tekrar deneyin.');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('restaurant.index');
    }
    public function ajax(Request $request)
    {
        $tumu = Order::whereDate('created_at', Carbon::today())
            ->where('restaurant_id', Auth::guard('restaurant')->id())
            ->with(['restaurant', 'courier', 'logs'])
            ->orderBy('created_at', 'asc')
            ->get();

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

    public function home()
    {
        $startTime = Carbon::today()->startOfDay();
        $endTime = Carbon::today()->endOfDay();
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

        // Paket Gelince Bildir — admin'in bu özelliği açık mı?
        $paketFeature = SystemFeature::where('name', 'Paket Gelince Bildir')->first();
        $adminId = Auth::user()->admin_id;
        $pakettGelinceBildir = $paketFeature && $adminId
            ? AdminSystemFeature::where('admin_id', $adminId)->where('system_feature_id', $paketFeature->id)->exists()
            : false;

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
            'dailyDeliverySpeed',
            'pakettGelinceBildir'
        )));
    }



    private function getCommonData($startDate, $endDate)
    {
        $userId = Auth::user()->id;

        $ResActiveCouriers = Courier::where('status', CourierStatus::active)
            ->where('restaurant_id', $userId)
            ->get();

        $AcitegenelCouriers = Courier::where('status', CourierStatus::active)
            ->where('restaurant_id', 0)
            ->get();

        $ActiveCouriers = $ResActiveCouriers->merge($AcitegenelCouriers);

        return [
            'couriers' => Courier::where('status', 'active')->where('restaurant_id', $userId)->get(),
            'genelCouriers' => Courier::where('status', 'active')->where('restaurant_id', 0)->get(),
            'ResActiveCouriers' => $ResActiveCouriers,
            'AcitegenelCouriers' => $AcitegenelCouriers,
            'ActiveCouriers' => $ActiveCouriers,
            'restaurant' => Restaurant::where('id', $userId)->get(),
            'yemeksepeti' => Order::where('platform', 'yemeksepeti')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'getiryemek' => Order::where('platform', 'getir')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'trendyol' => Order::where('platform', 'trendyol')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'telefonsiparis' => Order::where('platform', 'telefonsiparis')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'migros' => Order::where('platform', 'migros')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'customers' => Customer::where('status', 'active')->where('restaurant_id', $userId)->get(),
            'categories' => Categorie::where('status', 'active')->where('restaurant_id', $userId)->orderBy('desk', 'asc')->get(),
            'formattedExpense' => number_format(Order::where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'), 2, '.', ','),
            'formattedAverageExpense' => number_format(Order::where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->avg('amount'), 2, '.', ','),
            'totalCouriers' => Courier::count(),
            'idleCouriers' => Courier::where('status', CourierStatus::active)->count(),
            'breakCouriers' => Courier::where('status', CourierStatus::break)->count(),
        ];
    }

    private function getPreparedSpeed($userId, $startDate, $endDate)
    {
        return DB::table('order_status_logs as p')
            ->join('orders as o', 'o.id', '=', 'p.order_id')
            ->select(
                DB::raw('DATE(p.changed_at) as date'),
                DB::raw('ROUND(AVG(p.duration_seconds / 60), 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('p.status', OrderStatus::PENDING)
            ->whereNotNull('p.duration_seconds')
            ->where('o.restaurant_id', $userId)
            ->whereBetween('p.changed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * ASSIGNED to HANDOVER hızını hesapla
     */
    private function getHandoverSpeed($userId, $startDate, $endDate)
    {
        return DB::table('order_status_logs as a')
            ->join('order_status_logs as h', function($join) {
                $join->on('a.order_id', '=', 'h.order_id')
                    ->where('h.status', OrderStatus::HANDOVER);
            })
            ->join('orders as o', 'o.id', '=', 'a.order_id')
            ->select(
                DB::raw('DATE(a.changed_at) as date'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(MINUTE, a.changed_at, h.changed_at)), 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('a.status', OrderStatus::ASSIGNED)
            ->where('o.restaurant_id', $userId)
            ->whereBetween('a.changed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * HANDOVER to DELIVERED hızını hesapla
     */
    private function getDeliverySpeed($userId, $startDate, $endDate)
    {
        return DB::table('order_status_logs as h')
            ->join('order_status_logs as d', function($join) {
                $join->on('h.order_id', '=', 'd.order_id')
                    ->where('d.status', OrderStatus::DELIVERED);
            })
            ->join('orders as o', 'o.id', '=', 'h.order_id')
            ->select(
                DB::raw('DATE(h.changed_at) as date'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(MINUTE, h.changed_at, d.changed_at)), 2) as avg_minutes'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('h.status', OrderStatus::HANDOVER)
            ->where('o.restaurant_id', $userId)
            ->whereBetween('h.changed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Verileri tarih bazında eşleştir
     */
    private function mapDataToDates($data, $allDates)
    {
        $mappedData = [];
        $dataByDate = $data->keyBy('date');

        foreach ($allDates as $date) {
            $mappedData[] = $dataByDate->has($date) ? (float)$dataByDate[$date]->avg_minutes : 0;
        }

        return $mappedData;
    }

    /**
     * İstatistikleri hesapla
     */
    private function calculateStats($data)
    {
        if ($data->isEmpty()) {
            return ['avg' => 0, 'min' => 0, 'max' => 0, 'total_orders' => 0];
        }

        return [
            'avg' => round($data->avg('avg_minutes'), 2),
            'min' => round($data->min('avg_minutes'), 2),
            'max' => round($data->max('avg_minutes'), 2),
            'total_orders' => $data->sum('order_count')
        ];
    }

    public function getByPhone($phone)
    {
        // Müşteriyi ve adreslerini bul
        $customer = \App\Models\Customer::where('phone', 'LIKE', "%$phone%")
            ->with('addresses') // Adresler tablosuyla ilişki varsa
            ->first();
dd($customer);

        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->full_name,
                ],
                'addresses' => $customer->addresses // Müşterinin tüm kayıtlı adresleri
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Müşteri bulunamadı']);
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

        // Paket Gelince Bildir — admin'in bu özelliği açık mı?
        $paketFeature = SystemFeature::where('name', 'Paket Gelince Bildir')->first();
        $adminId = Auth::user()->admin_id;
        $pakettGelinceBildir = $paketFeature && $adminId
            ? AdminSystemFeature::where('admin_id', $adminId)->where('system_feature_id', $paketFeature->id)->exists()
            : false;

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
            'dailyDeliverySpeed',
            'pakettGelinceBildir'
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

    //auth process
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
        return redirect()->route('restaurant.login');
    }

    protected function guard()
    {
        return Auth::guard('restaurant');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'restaurant_name' => 'required',
            'email' => 'required|unique:restaurants',
            'phone' => 'required',
            'password' => 'required',
        ]);

        $create = new Restaurant();
        $create->restaurant_code = "RES-" . rand(9, 99999);
        $create->restaurant_name = $data['restaurant_name'];
        $create->name = $data['name'];
        $create->email = $data['email'];
        $create->phone = $data['phone'];
        $create->password = Hash::make($data['password']);
        $create->status = 'deactive';
        $create->save();

        return redirect()->route('restaurant.payment');
    }
}
