<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DemoController extends Controller
{
    public function index()
    {
        return view('demo.create');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:admins,email',
            'city_id'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
        ], [
            'name.required'      => 'İsim alanı zorunludur.',
            'email.required'     => 'E-posta alanı zorunludur.',
            'email.unique'       => 'Bu e-posta adresi zaten kullanılıyor.',
            'city_id.required'   => 'Şehir seçimi zorunludur.',
            'latitude.required'  => 'Haritadan konum seçiniz.',
            'longitude.required' => 'Haritadan konum seçiniz.',
        ]);

        $admin = Admin::create([
            'name'            => $request->input('name'),
            'email'           => $request->input('email'),
            'password'        => Hash::make('demo123'),
            'is_test'         => 1,
            'is_active'       => 1,
            'top_up_balance'  => 10,
            'latitude'        => $request->input('latitude'),
            'longitude'       => $request->input('longitude'),
            'city_id'         => $request->input('city_id'),
            'district_id'     => $request->input('district_id'),
            'code'            => rand(100000, 999999),
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.index');
    }
}
