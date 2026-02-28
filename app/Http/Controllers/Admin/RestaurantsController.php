<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Expenses;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $restaurants = Restaurant::where('status', 'active')->where('admin_id', auth()->id())->get();

        return view('admin.restaurants.index', compact('restaurants'));
    }
    public function new()
    {
        return view('admin.restaurants.new');
    }
    public function edit($id)
    {
        $restaurant = Restaurant::find($id);

        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function create(Request $request)
    {
        $testMode =config('site.test_mode');

        if ($testMode) {
            if (Restaurant::count() > config('site.test_mode_limit')) {
                return redirect()->back()->with('error', 'Test Modu: Üzgünüz, En Fazla '.config('site.test_mode_limit').' Kayıt Ekleyebilirsiniz');
            }
        }

        if (Restaurant::where('email',$request->email)->exists()) {
            return redirect()->back()->with('error', 'Bu email adresine ait bir restaurant zaten mevcut!!');
        }

        if (Restaurant::where('phone',$request->phone)->exists()) {
            return redirect()->back()->with('error', 'Bu telefon numarasına ait bir restaurant zaten mevcut!!');
        }

        if (Restaurant::where('restaurant_name',$request->restaurant_name)->exists()) {
            return redirect()->back()->with('error', 'Bu isimde bir restaurant zaten mevcut!!');
        }

        $create = new Restaurant();
        $create->admin_id = auth()->id();
        $create->restaurant_code = "RES-" . rand(9, 99999);
        $create->restaurant_name = $request->restaurant_name;
        $create->name = $request->name;
        $create->email = $request->email;
        $create->phone = $request->phone;
        $create->password = Hash::make($request->password);
        $create->tax_name = $request->tax_name;
        $create->tax_number = $request->tax_number;
        $create->package_price = $request->package_price;
        $create->address = $request->address;
        $create->latitude = $request->latitude;
        $create->longitude = $request->longitude;
        $create->save();

        return redirect()->back()->with('success', 'Restaurant Kaydı Tamamlandı.');
    }

    public function update(Request $request)
    {
        $create = Restaurant::find($request->id);

        if (Restaurant::where('email',$request->email)->where('id','!=',$create->id)->exists()) {
            return redirect()->back()->with('error', 'Bu email adresine ait bir restaurant zaten mevcut!!');
        }

        if (Restaurant::where('phone',$request->phone)->where('id','!=',$create->id)->exists()) {
            return redirect()->back()->with('error', 'Bu telefon numarasına ait bir restaurant zaten mevcut!!');
        }

        if (Restaurant::where('restaurant_name',$request->restaurant_name)->where('id','!=',$create->id)->exists()) {
            return redirect()->back()->with('error', 'Bu isimde bir restaurant zaten mevcut!!');
        }

        $create->restaurant_name = $request->restaurant_name;
        $create->name = $request->name;
        $create->email = $request->email;
        $create->phone = $request->phone;
        if ($request->password) {
            $create->password = Hash::make($request->password);
        }
        $create->tax_name = $request->tax_name;
        $create->tax_number = $request->tax_number;
        $create->address = $request->address;
        $create->status = $request->status;
        $create->package_price = $request->package_price;
        $create->latitude = $request->latitude;
        $create->longitude = $request->longitude;
        $create->update();

        return redirect()->back()->with('success', 'İşyeri bilgileri güncellendi.');
    }

    public function delete($id)
    {
        $del = Restaurant::find($id);
        $del->status = 'deactive';
        $sav = $del->save();

        if ($sav) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
