<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Dealer extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('dealer')->attempt($credentials)) {
            if (!Auth::guard('dealer')->user()->is_active){
                Auth::guard('dealer')->logout();

                return redirect()->back()->with(['test' => 'Hesabınız Henüz Aktif Değil.']);
            }

            return redirect()->route('dealer.dashboards');
        }

        return redirect()->route('dealer.login')->withErrors(['error' => 'Giriş bilgileri hatalı.']);
    }

    public function logout()
    {
        Auth::guard('dealer')->logout(); // Superadmin oturumunu kapat
        return redirect()->route('dealer.login'); // Login sayfasına yönlendir
    }
}
