<?php

namespace App\Http\Controllers\Api\v2\Courier\Profile;

use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexController extends Controller
{
    public function index()
    {
        $courier = auth('courier')->user();

        return Json::success('Kurye bilgileri', new CourierResource($courier));
    }

    public function updateLocation(Request $request)
    {
        $courier = auth('courier')->user();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier->latitude = $request->latitude;
        $courier->longitude = $request->longitude;
        $courier->save();

        return Json::success('Konum güncellendi.', new CourierResource($courier));
    }

    public function update(Request $request)
    {
        $courier = auth('courier')->user();

        if ($request->input('price_type') == 'package' && empty($request->input('price'))) {
            return Json::error('Lütfen paket başı ücretinizi giriniz.', 422);
        }

        if ($request->input('price_type') === 'fixed') {
            if (empty($request->input('fixed_price')) || empty($request->input('km_price'))) {
                return Json::error('Lütfen sabit ücret ve km başı ücretinizi giriniz.', 422);
            }
        }

        if ($request->filled('phone') && Courier::where('phone', $request->input('phone'))
            ->where('id', '!=', $courier->id)
            ->exists()) {
            return Json::error('Bu telefon numarasına ait bir kayıt zaten mevcut.', 409);
        }

        $courier->update([
            'name' => $request->input('name') ?? $courier->name,
            'phone' => $request->input('phone') ?? $courier->phone,
            'fcm_token' => $request->input('fcm_token') ?? $courier->fcm_token,
            'birthday' => $request->input('birthday') ?? $courier->birthday,
            'latitude' => $request->input('latitude') ?? $courier->latitude,
            'longitude' => $request->input('longitude') ?? $courier->longitude,
            'price_type' => $request->input('price_type') ?? $courier->price_type,
            'price' => $request->filled('price') ? $request->input('price') : $courier->price,
            'fixed_price' => $request->input('fixed_price') ?? $courier->fixed_price,
            'km_price' => $request->input('km_price') ?? $courier->km_price,
            'online' => $request->input('online') ?? $courier->online,
        ]);

        return Json::success('Bilgileriniz başarıyla güncellendi.', new CourierResource($courier));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();

        if (!Hash::check($request->input('current_password'), $courier->password)) {
            return Json::error('Mevcut şifreniz hatalı.', 401);
        }

        $courier->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return Json::success('Şifreniz başarıyla güncellendi.');
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,passive,break',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();

        if ($courier->status == CourierStatus::service) {
            return Json::error('Teslim edilmeyen siparişiniz bulunuyor.', 409);
        }

        $courier->update([
            'status' => $request->input('status'),
        ]);

        return Json::success('Durumunuz başarıyla güncellendi.', new CourierResource($courier));
    }

    public function destroy()
    {
        try {
            $courier = auth('courier')->user();

            if ($courier->status == CourierStatus::service) {
                return Json::error('Şu an aktif bir paket taşıyorsunuz, önce siparişi tamamlayınız.', 409);
            }

            $courier->delete();

            JWTAuth::invalidate(JWTAuth::getToken());

            return Json::success('Hesabınız silindi ve oturumunuz sonlandırıldı.');

        } catch (JWTException $e) {
            return Json::error($e->getMessage());
        }
    }
}
