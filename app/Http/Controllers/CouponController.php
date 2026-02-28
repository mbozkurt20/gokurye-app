<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Product;
use App\Models\RestaurantCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{

    public function index()
    {
        $coupons = RestaurantCoupon::where('restaurant_id',Auth::guard('restaurant')->id())->get();
       return view('restaurant.coupons.index',compact('coupons'));
    }


    public function create()
    {
        return view('restaurant.coupons.new');
    }


    public function store(Request $request)
    {
        $testMode =config('site.test_mode');

        if ($testMode) {
            if (RestaurantCoupon::count() > config('site.test_mode_limit')) {
                return redirect()->back()->with('error', 'Test Modu: Üzgünüz, En Fazla '.config('site.test_mode_limit').' Kayıt Ekleyebilirsiniz');
            }
        }

        $create = new RestaurantCoupon();

        $create->restaurant_id = Auth::user()->id;
        $create->coupon_id = rand(11111, 99999);
        $create->name = $request->name;
        $create->description = $request->description;
        $create->total_seller_amount = $request->total_seller_amount;
        $create->save();

        return redirect()->back()->with('success', 'Kupon Başarıyla Eklendi');
    }

    public function edit($id)
    {
        $coupon = RestaurantCoupon::find($id);
        return view('restaurant.coupons.edit',compact('coupon'));
    }

    public function update(Request $request)
    {
        $create = RestaurantCoupon::find($request->id);

        $create->name = $request->name;
        $create->description = $request->description;
        $create->total_seller_amount = $request->total_seller_amount;
        $create->update();

        return redirect()->back()->with('success', 'Kupon Başarıyla Güncellendi');
    }


    public function delete($id)
    {
        $del = RestaurantCoupon::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
