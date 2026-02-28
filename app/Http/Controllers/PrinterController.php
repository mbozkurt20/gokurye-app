<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\Product;
use App\Models\RestaurantCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterController extends Controller
{
    public function index()
    {
       $prints = Printer::where('payable_type','restaurant')->where('payable_id',Auth::guard('restaurant')->id())->get();
       return view('restaurant.prints.index',compact('prints'));
    }
    public function apps()
    {
        return view('restaurant.apps.index');
    }
    public function create()
    {
        return view('restaurant.prints.new');
    }
    public function store(Request $request)
    {
        $testMode =config('site.test_mode');

        if ($testMode) {
            if (Printer::count() > config('site.test_mode_limit')) {
                return redirect()->back()->with('error', 'Test Modu: Üzgünüz, En Fazla '.config('site.test_mode_limit').' Kayıt Ekleyebilirsiniz');
            }
        }

        $create = new Printer();
        $create->payable_id = Auth::user()->id;
        $create->payable_type = 'restaurant';
        $create->name = $request->name;
        $create->description = $request->description;
        $create->save();

        return redirect()->back()->with('success', 'Yazıcı Başarıyla Eklendi');
    }
    public function edit($id)
    {
        $print = Printer::find($id);
        return view('restaurant.prints.edit',compact('print'));
    }
    public function update(Request $request)
    {
        $create = Printer::find($request->id);
        $create->name = $request->name;
        $create->description = $request->description;
        $create->update();

        return redirect()->back()->with('success', 'Yazıcı Başarıyla Güncellendi');
    }

    public function delete($id)
    {
        $del = Printer::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
