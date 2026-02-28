<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CourierApplyController extends Controller
{
    public function index()
    {
        return view('courier.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'password'      => 'required|string|min:6',
            'tc_id'         => 'required|digits:11',
            'age'           => 'required|integer|min:18|max:70',
            'blood_type'    => 'required|string',
            'admin_code'    => 'required|string',
            'profile_photo' => 'required|image|max:2048',
            'vehicle_type'  => 'required|in:motor,otomobil',
            'plate'         => 'required|string|max:20',
            'bank'          => 'required|string|max:100',
            'iban'          => 'required|string|max:32',
        ]);

        if (Admin::where('code', $request->input('admin_code'))->exists()) {
            return redirect()->back()->with('error', 'Bu koda ait bir firma bulunamadı.');
        }

        if (Courier::where('phone', $request->input('phone'))->exists()) {
            return redirect()->back()->with('error', 'Bu telefon numarasıyla zaten bir başvuru yapılmıştır.');
        }



        $profilePhoto = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo')->store('couriers/photos', 'public');
        }

        Courier::create([
            'admin_id'      => 0,
            'restaurant_id' => 0,
            'name'          => $request->input('name'),
            'phone'         => $request->input('phone'),
            'password'      => Hash::make($request->input('password') ?? 'kurye123'),
            'is_active'     => 0,
            'status'        => 'passive',
            'code'          => rand(100000, 999999),
            'tc_id'         => $request->input('tc_id'),
            'age'           => $request->input('age'),
            'blood_type'    => $request->input('blood_type'),
            'vehicle_type'  => $request->input('vehicle_type'),
            'plate'         => $request->input('plate'),
            'iban'          => $request->input('iban'),
            'bank'          => $request->input('bank'),
            'profile_photo' => $profilePhoto,
            'latitude'      => '39.9334',
            'longitude'     => '32.8597',
        ]);

        return redirect()->back()->with('success', 'Başvurunuz başarıyla alındı! Yönetici onayladıktan sonra hesabınız aktif edilecektir.');
    }
}
