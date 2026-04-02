<?php

namespace App\Http\Controllers\Api\v2\Courier\Auth;

use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Courier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'fcm_token' => 'nullable',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $rawPhone = preg_replace('/\D/', '', $request->phone);
        $courier = Courier::query()->where('phone', $rawPhone)->first();

        if (!$courier || !Hash::check($request->password, $courier->password)) {
            return Json::error('Telefon numarası veya şifre hatalı.', 401);
        }

        if (!$courier->is_active) {
            return Json::error('Hesabınız aktif edilmemiş, yöneticiniz ile iletişime geçiniz.', 403);
        }

        $courier->fcm_token = $request->input('fcm_token');
        $courier->save();

        $ttl = 60 * 24 * 30; // 30 gün
        $expiryDate = Carbon::now()->addMinutes($ttl);

        $token = JWTAuth::fromUser($courier, ['exp' => $expiryDate->timestamp]);

        return Json::success('Giriş başarılı', [
            'token' => $token,
            'expiry_date' => $expiryDate->toIso8601String(),
            'courier' => new CourierResource($courier),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string',
            'phone' => 'required|string|unique:couriers,phone',
            'password' => 'required|string|min:6',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'birthday' => 'required|date',
            'fcm_token' => 'nullable|string',
            'price_type' => 'nullable|in:package,fixed',
            'price' => 'nullable|numeric',
            'fixed_price' => 'nullable|numeric',
            'km_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        if (!Admin::where('code', $request->input('code'))->exists()) {
            return Json::error('Bu koda ait yönetici bulunamadı.', 404);
        }

        if ($request->input('price_type') == 'package' && empty($request->input('price'))) {
            return Json::error('Lütfen paket başı ücretinizi giriniz.', 422);
        }

        if ($request->input('price_type') === 'fixed') {
            if (empty($request->input('fixed_price')) || empty($request->input('km_price'))) {
                return Json::error('Lütfen sabit ücret ve km başı ücretinizi giriniz.', 422);
            }
        }

        $admin = Admin::where('code', $request->input('code'))->first();

        $courier = Courier::create([
            'admin_id' => $admin->id,
            'name' => $request->input('name'),
            'birthday' => $request->input('birthday'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price') ?? 0.00,
            'fixed_price' => $request->input('fixed_price'),
            'km_price' => $request->input('km_price'),
            'fcm_token' => $request->input('fcm_token'),
            'code' => $this->generateCode(),
            'status' => CourierStatus::active,
            'is_active' => true,
        ]);

        return Json::success('Kaydınız başarıyla alınmıştır.', new CourierResource($courier), 201);
    }

    private function generateCode(): int
    {
        do {
            $code = rand(100000, 999999);
        } while (Courier::where('code', $code)->exists());

        return $code;
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return Json::success('Oturumunuz sonlandırıldı.');
        } catch (JWTException $e) {
            return Json::error($e->getMessage());
        }
    }
}
