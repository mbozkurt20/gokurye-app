<?php

namespace App\Http\Controllers\Api\v2\Courier\Profile;

use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use App\Models\Order;
use App\Models\ProgressPaymentRecord;
use Carbon\Carbon;
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

    public function earnings(Request $request)
    {
        $courier = auth('courier')->user();

        $startDate = $request->query('startDate')
            ? Carbon::parse($request->query('startDate'))->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->query('endDate')
            ? Carbon::parse($request->query('endDate'))->endOfDay()
            : Carbon::now()->endOfDay();

        $deliveredOrders = Order::where('courier_id', $courier->id)
            ->where('status', OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $orderCount = $deliveredOrders->count();
        $totalKm = $deliveredOrders->sum(fn($o) => (float) $o->distance);

        $paidAmount = ProgressPaymentRecord::where('payable_type', 'courier')
            ->where('payable_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $total = 0;
        $info = '';

        if ($courier->price_type === 'package') {
            $unitPrice = (float) $courier->price;
            $total = $orderCount * $unitPrice;
            $info = "{$orderCount} paket x " . number_format($unitPrice, 2) . " TL";
        } else {
            $kmPrice = (float) $courier->km_price;
            $fixedPrice = (float) $courier->fixed_price;
            $externalKm = (float) $courier->km_distance_later;

            $distanceTotal = $deliveredOrders->sum(function ($o) use ($kmPrice, $externalKm) {
                $orderKm = (float) $o->distance;
                $payableKm = max(0, $orderKm - $externalKm);
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $total = $distanceTotal + $fixedTotal;
            $info = "Sabit " . number_format($fixedPrice, 2) . " TL + km ücreti";
        }

        return Json::success('Hakediş bilgileri', [
            'order_count'            => $orderCount,
            'total_km'               => round($totalKm, 2),
            'total_progress_payment' => number_format($total, 2, '.', ''),
            'paid_amount'            => number_format($paidAmount, 2, '.', ''),
            'remaining'              => number_format(max(0, $total - $paidAmount), 2, '.', ''),
            'calculation_info'       => $info,
            'price_type'             => $courier->price_type,
        ]);
    }

    public function getNotes()
    {
        $courier = auth('courier')->user();
        return Json::success('Notlar', $courier->notes ?? []);
    }

    public function saveNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();
        $notes = $courier->notes ?? [];
        $notes[] = [
            'text'       => $request->input('text'),
            'created_at' => now()->toIso8601String(),
        ];
        $courier->notes = $notes;
        $courier->save();

        return Json::success('Not kaydedildi.', $courier->notes);
    }

    public function deleteNote($index)
    {
        $courier = auth('courier')->user();
        $notes = $courier->notes ?? [];

        if (!isset($notes[$index])) {
            return Json::error('Not bulunamadı.', 404);
        }

        array_splice($notes, $index, 1);
        $courier->notes = $notes;
        $courier->save();

        return Json::success('Not silindi.', $courier->notes);
    }

    public function getExpenses()
    {
        $courier = auth('courier')->user();
        return Json::success('Giderler', $courier->expenses ?? []);
    }

    public function addExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors()->first(), 422);
        }

        $courier = auth('courier')->user();
        $expenses = $courier->expenses ?? [];
        $expenses[] = [
            'description' => $request->input('description'),
            'amount'      => (float) $request->input('amount'),
            'created_at'  => now()->toIso8601String(),
        ];
        $courier->expenses = $expenses;
        $courier->save();

        return Json::success('Gider kaydedildi.', $courier->expenses);
    }

    public function deleteExpense($index)
    {
        $courier = auth('courier')->user();
        $expenses = $courier->expenses ?? [];

        if (!isset($expenses[$index])) {
            return Json::error('Gider bulunamadı.', 404);
        }

        array_splice($expenses, $index, 1);
        $courier->expenses = $expenses;
        $courier->save();

        return Json::success('Gider silindi.', $courier->expenses);
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
