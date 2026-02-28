<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::guard('restaurant')->check()) {
            return redirect()->route('restaurant.index');
        }

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.index');
        }

        return view('auth.login');
    }

    public function dealer()
    {
        $cities = City::all();
        return view('dealer-register', compact('cities'));
    }

    public function getDistricts($cityId)
    {
        return response()->json(District::where('city_id', $cityId)->get(['id', 'name']));
    }

    public function createDealerRequest(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:5',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'address' => 'nullable|string',
        ]);

        User::create([
            'is_active' => false,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'],
            'address' => $data['address'],
        ]);

        return redirect()->back()->with('success', 'Başvurunuz Başarıyla Alınmıştır');
    }
}
