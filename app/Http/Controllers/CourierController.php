<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Models\Admin\AdminCourier;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Expenses;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\TenantModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.couriers.index', compact('couriers'));
    }

    public function getCourier()
    {
        $adminId = Auth::guard('restaurant')->user()->admin_id;

        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', $adminId)
            ->whereIn('status', [CourierStatus::active,CourierStatus::service])
            ->get();

        return response()->json($couriers);
    }

    public function maps()
    {
        $couriers = Courier::on('tenant')
            ->where('status','active')
            ->get();

        $restaurant = Restaurant::where('id', auth()->id())->select(['latitude','longitude'])->first();

        $couriers = $couriers->map(function($courier) use ($restaurant) {
            $distanceKm = $this->haversineDistance(
                $restaurant->latitude,
                $restaurant->longitude,
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

        return view('currents.courier.maps', compact('couriers','restaurant'));

    }

    public function new()
    {
        return view('restaurant.couriers.new');
    }
    public function edit($id)
    {
        $courier = Courier::find($id);

        return view('restaurant.couriers.edit', compact('courier'));
    }
    public function create(Request $request)
    {
        $testMode =config('site.test_mode');

        if ($testMode) {
            if (Courier::count() > config('site.test_mode_limit')) {
                return redirect()->back()->with('error', 'Test Modu: Üzgünüz, En Fazla '.config('site.test_mode_limit').' Kayıt Ekleyebilirsiniz');
            }
        }

        Courier::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => CourierStatus::passive,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'code' => $this->generateCode(),
            'is_active' => 1,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()->with('success', 'Kurye Başarıyla Kaydedildi.');
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
            return redirect()->back()->with('success', 'Tüm alanları doldurunuz.');
        }

        if (!empty($request->input('password'))) {
            Courier::whereId($request->get('id'))->update([
                'password' => $request->input('password')
            ]);
        }

         Courier::whereId($request->input('id'))->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => $request->input('status'),
        ]);

        return redirect()->back()->with('success', 'Kurye güncelleme işlemi başarıyla gerçekleşti.');
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
    public function report($id)
    {
        $courier = Courier::where('id', $id)->first();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('courier_id', $id)->whereDate('created_at', Carbon::today())->get();
        return view('restaurant.couriers.report', compact('courier', 'orders'));
    }
}
