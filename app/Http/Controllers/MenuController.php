<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Restaurant;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('restaurant.menus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
       $restaurant = Auth::guard('restaurant')->user();
       $restaurant->menu_template = $request->template;
       $restaurant->save();

        return redirect()->back()->with('success', 'Menü Başarıyla Güncellendi');
    }


    public function qrCode()
    {
        $restaurant = Auth::guard('restaurant')->user();
        $menuUrl    = url('/restaurant/' . $restaurant->id . '/menu');

        $qrCode = QrCode::create($menuUrl)->setSize(300)->setMargin(10);
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);
        $qrSvg  = $result->getString();

        return view('restaurant.menus.qr', compact('menuUrl', 'qrSvg'));
    }

    public function show($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            abort(404);
        }

        $template = $restaurant->menu_template ?: 'first';

        $data = [
            'name'       => $restaurant->name,
            'address'    => $restaurant->address,
            'phone'      => $restaurant->phone,
            'email'      => $restaurant->email,
            'categories' => Categorie::with('products')->where('restaurant_id', $restaurant->id)->where('status', 'active')->orderBy('desk', 'asc')->get(),
        ];

        return view('restaurant.menus.templates.' . $template, compact('restaurant', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
