<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourierHelper;
use App\Helpers\CourierStatus;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Helpers\Pusher;
use App\Http\Controllers\Controller;
use App\Jobs\CheckCourierTimeoutJob;
use App\Models\Courier;
use App\Models\Admin;
use App\Models\Order;
use App\Models\CourierOrder;
use App\Models\ProgressPaymentRecord;
use App\Models\Restaurant;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->where('is_active', 1)
            ->get();

        $applications = Courier::where('is_active', 0)
            ->where('admin_id', 0)
            ->get();

        return view('admin.couriers.index', compact('couriers', 'applications'));
    }

    public function approveApplication($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->update([
            'is_active' => 1,
            'admin_id'  => auth()->id(),
            'status'    => \App\Helpers\CourierStatus::active,
        ]);

        return redirect()->route('admin.couriers.index')->with('message', 'Başvuru onaylandı. Kurye aktif edildi.');
    }

    public function rejectApplication($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->delete();

        return response()->json(['status' => 'OK']);
    }

    public function getCourier()
    {
        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', Auth::guard('admin')->id())
            ->whereIn('status', [CourierStatus::active, CourierStatus::service])
            ->get();

        return response()->json($couriers);
    }

    public function new()
    {
        return view('admin.couriers.new');
    }

    public function edit($id)
    {
        $courier = Courier::find($id);
        return view('admin.couriers.edit', compact('courier'));
    }

    public function create(Request $request)
    {
        $isTestAccount = auth()->guard('admin')->check() && auth()->guard('admin')->user()->is_test;
        $testMode = config('site.test_mode') || $isTestAccount;
        $testLimit = $isTestAccount ? 2 : config('site.test_mode_limit');

        if ($testMode) {
            if (Courier::where('admin_id', auth()->guard('admin')->id())->count() >= $testLimit) {
                return redirect()->back()->with('test', 'Test Hesabı: En Fazla ' . $testLimit . ' Kurye Ekleyebilirsiniz');
            }
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'password'      => 'required|string|min:6',
            'tc_id'         => 'required|string|max:11',
            'age'           => 'required|integer|min:18|max:70',
            'blood_type'    => 'required|string',
            'profile_photo' => 'required|image|max:2048',
            'vehicle_type'  => 'required|in:motor,otomobil',
            'plate'         => 'required|string|max:20',
            'bank'          => 'required|string|max:100',
            'iban'          => 'required|string|max:32',
        ]);

        if (Courier::where('phone', $request->input('phone'))->exists()) {
            return redirect()->back()->with('test', 'Bu numaraya ait kurye bulunmaktadır !!');
        }

        $profilePhoto = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo')->store('couriers/photos', 'public');
        }

        Courier::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'km_distance_later' => $request->input('km_distance_later'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => CourierStatus::active,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'code' => $this->generateCode(),
            'is_active' => 1,
            'admin_id' => Auth::guard('admin')->user()->id,
            'iban' => $request->input('iban'),
            'bank' => $request->input('bank'),
            'profile_photo' => $profilePhoto,
            'tc_id' => $request->input('tc_id'),
            'age' => $request->input('age'),
            'vehicle_type' => $request->input('vehicle_type'),
            'plate' => $request->input('plate'),
            'blood_type' => $request->input('blood_type'),
        ]);

        return redirect()->back()->with('message', 'Kurye Başarıyla Kaydedildi.');
    }

    public function generateCode()
    {
        $code = rand(100000, 999999);

        if (Courier::where('code', $code)->exists()) {
            $code = rand(100000, 999999);
        }

        return $code;
    }

    public function update(Request $request)
    {
        $requestData = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required'
        ]);

        if ($requestData->fails()) {
            return redirect()->back()->with('message', 'Tüm alanları doldurunuz.');
        }

        if (Courier::where('id', '!=', $request->input('id'))->where('phone', $request->input('phone'))->exists()) {
            return redirect()->back()->with('test', 'Bu numaraya ait kurye bulunmaktadır !!');
        }

        if (!empty($request->input('password'))) {
            Courier::whereId($request->get('id'))->update([
                'password' => $request->input('password')
            ]);
        }

        $courier = Courier::whereId($request->input('id'))->first();

        if ($courier->price_type != $request->get('price_type') && CourierHelper::hasReceivable($courier->id)) {
            return redirect()->back()->with('test', 'Kurye ödeme türünü değiştirmek için kurye hakedişini ödemelisiniz!!');
        }

        $updateData = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'km_distance_later' => $request->input('km_distance_later'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => $request->input('status'),
            'iban' => $request->input('iban'),
            'bank' => $request->input('bank'),
            'tc_id' => $request->input('tc_id'),
            'age' => $request->input('age'),
            'vehicle_type' => $request->input('vehicle_type'),
            'plate' => $request->input('plate'),
            'blood_type' => $request->input('blood_type'),
        ];

        if ($request->hasFile('profile_photo')) {
            $updateData['profile_photo'] = $request->file('profile_photo')->store('couriers/photos', 'public');
        }

        $courier->update($updateData);

        $courierss = Courier::where('status', 1)
            ->where('status', CourierStatus::active)
            ->get();


        $admin = Admin::where('id', auth()->id())->select(['latitude', 'longitude'])->first();

        $courierss = $courierss->map(function ($courier) use ($admin) {
            $distanceKm = $this->haversineDistance(
                $admin->latitude,
                $admin->longitude,
                $courier->latitude,
                $courier->longitude
            );

            if ($distanceKm < 1) {
                $courier->distance = round($distanceKm * 1000) . ' metre';
            } else {
                $courier->distance = round($distanceKm, 2) . ' km';
            }

            return $courier;
        });

        Pusher::trigger('courier-channel', 'courier-' . $admin->id, $courierss);

        return redirect()->back()->with('message', 'Kurye güncelleme işlemi başarıyla gerçekleşti.');
    }

    public function delete($id)
    {
        $del = Courier::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }

    public function report(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        // Tarih aralığı (varsayılan: bugün)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $startDateObj = Carbon::parse($startDate)->startOfDay();
        $endDateObj = Carbon::parse($endDate)->endOfDay();

        // Siparişleri Getir
        $orders = Order::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDateObj, $endDateObj])
            ->orderBy('created_at', 'desc')
            ->get();

        // Sadece Teslim Edilenler Üzerinden Hesaplama Yap
        $deliveredOrders = $orders->where('status', OrderStatus::DELIVERED);

        // Ödeme Yöntemi Gruplama
        $cashMethods = ['Kapıda Nakit İle Ödeme', 'Nakit', 'Kapıda Nakit ile Ödeme'];
        $cardMethods = ['Kapıda Kredi Kartı ile Ödeme', 'Kredi Kartı', 'Online Ödeme'];
        $ticketMethods = [
            'Kapıda Ticket ile Ödeme','Ticket','Ticket Online',
            'Kapıda Sodexo ile Ödeme','Sodexo','Sodexo Online',
            'Kapıda Multinet ile Ödeme','Multinet','Multinet Online',
            'Kapıda Pluxee ile Ödeme','Pluxee','Pluxee Online'
        ];

        $cashOrders = $deliveredOrders->whereIn('payment_method', $cashMethods);
        $cardOrders = $deliveredOrders->whereIn('payment_method', $cardMethods);
        $ticketOrders = $deliveredOrders->whereIn('payment_method', $ticketMethods);

        // Kazanç Hesaplama Parametreleri
        $totalEarnings = 0;
        $info = "";
        $orderCount = $deliveredOrders->count();

        if ($courier->price_type == 'package') {
            $unitPrice = (float) $courier->price;
            $totalEarnings = $orderCount * $unitPrice;
            $info = "Teslim edilen {$orderCount} paket için paket başı " . number_format($unitPrice, 2) . " ₺ üzerinden hesaplanmıştır.";
        } else {
            $kmPrice = (float) $courier->km_price;
            $fixedPrice = (float) $courier->fixed_price;
            $externalKm = (float) $courier->km_distance_later;

            $distanceEarnings = $deliveredOrders->sum(function($o) use ($kmPrice, $externalKm) {
                $orderKm = (float) $o->distance; // Veri KM cinsinden string ise
                $payableKm = max(0, $orderKm - $externalKm);
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $totalEarnings = $distanceEarnings + $fixedTotal;

            $info = "Paket başı sabit " . number_format($fixedPrice, 2) . " ₺ + ilk {$externalKm} km sonrası için km başı " . number_format($kmPrice, 2) . " ₺ eklenmiştir.";
        }

        $summary = [
            'order_count' => $orderCount,
            'cash_count' => $cashOrders->count(),
            'card_count' => $cardOrders->count(),
            'ticket_count' => $ticketOrders->count(),
            'info' => $info
        ];

        return view('admin.couriers.report', compact(
            'courier', 'orders', 'startDate', 'endDate', 'summary', 'totalEarnings'
        ));
    }

    public function maps()
    {
        $data = [
            'active' => Courier::where('status', CourierStatus::active)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'passive' => Courier::where('status', CourierStatus::passive)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'service' => Courier::where('status', CourierStatus::service)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'break' => Courier::where('status', CourierStatus::break)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count()
        ];

        $couriers = Courier::whereIn('status', [CourierStatus::active, CourierStatus::service])
            ->where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->get();

        $admin = Admin::where('id', \auth()->id())->select(['latitude', 'longitude'])->first();
        $courierss = $couriers->map(function ($courier) use ($admin) {
            $distanceKm = OrdersHelper::haversineDistance(
                $admin->latitude,
                $admin->longitude,
                $courier->latitude,
                $courier->longitude
            );

            if ($distanceKm < 1) {
                $courier->distance = round($distanceKm * 1000) . ' metre';
            } else {
                $courier->distance = round($distanceKm, 2) . ' km';
            }

            return $courier;
        });

        return view('admin.couriers.new-maps', compact('courierss', 'data'));
    }

    public function auto_order($id)
    {
        $auto = Admin::where('id', auth()->id())->first();
        $auto->auto_orders = $id;
        $auto->save();
    }

    /**
     * @throws \Exception
     */
    public function sendCourier($orderId, $courierId)
    {
        $order = Order::find($orderId);
        $courier = Courier::find($courierId);

        $order->courier_id = $courier->id;
        $order->assigned_at = Carbon::now();
        $order->save();

        CheckCourierTimeoutJob::dispatch($order->id)
            ->delay(now()->addMinutes(2));

        // Kuryeyi servide de yap ve son atama zamanını güncelle
        $courier->last_assigned_at = now();
        $courier->save();

        $orderCourier = CourierOrder::where('courier_id', $courier->id)->where('order_id', $order->id)->first();

        if (!$orderCourier) {
            // Yeni siparişi kuryeye atama
            $newOrderCourier = new CourierOrder();
            $newOrderCourier->courier_id = $courier->id;
            $newOrderCourier->order_id = $order->id;
            $newOrderCourier->save();
        }

        $restaurant = Restaurant::find($order->restaurant_id);

        //mobil bildiri
        if ($courier->fcm_token) {
            $ser = new PushNotificationService();
            $ser->sendNotification($courier->fcm_token, $restaurant->restaurant_name . ' Restorandan Yeni Sipariş Atandı', 'Sipariş Takip Kodu:' . $order->tracking_id);
        }

        if (OrdersHelper::getOrderSystem(3)) {
            NotificationHelper::add([
                'title' => 'Paket Kuryeye Atandı',
                'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . ' isimli kuryeye atandı.',
                'url' => route('admin.balance')
            ]);
        }

        echo 'OK';
    }

    public function performance(Request $request)
    {
        $courierId = $request->input('courier_id');
        $period = $request->input('period', 'daily'); // daily, weekly, monthly
        $date = Carbon::parse($request->input('date', now()));

        // Tarih aralığına göre filtre
        $startDate = match ($period) {
            'weekly' => $date->copy()->startOfWeek(),
            'monthly' => $date->copy()->startOfMonth(),
            default => $date->copy()->startOfDay(),
        };
        $endDate = match ($period) {
            'weekly' => $date->copy()->endOfWeek(),
            'monthly' => $date->copy()->endOfMonth(),
            default => $date->copy()->endOfDay(),
        };

        $query = DB::table('courier_status_movements')
            ->select('courier_id', 'status', DB::raw('SUM(duration_seconds) as total_duration'))
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id', 'status');

        if ($courierId) {
            $query->where('courier_id', $courierId);
        }

        $statusSummary = $query->get();

        // Günlük en çok aktif olan courier
        $topActiveCourier = DB::table('courier_status_movements')
            ->select('courier_id', DB::raw('SUM(duration_seconds) as active_duration'))
            ->where('status', 'active')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id')
            ->orderByDesc('active_duration')
            ->first();

        // Sağdaki liste: tüm courier'lar için statülere göre sıralama
        $topStatusList = DB::table('courier_status_movements')
            ->select('courier_id', 'status', DB::raw('SUM(duration_seconds) as total_duration'))
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id', 'status')
            ->orderByDesc('total_duration')
            ->get();

        $couriers = Courier::where('admin_id', auth()->id())->get();

        return view('admin.couriers.performance', compact(
            'statusSummary',
            'topActiveCourier',
            'topStatusList',
            'period',
            'startDate',
            'endDate',
            'courierId',
            'couriers'
        ));
    }
}
