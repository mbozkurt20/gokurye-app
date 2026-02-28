<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\TopupMovement;
use App\Services\Paytr\PayTrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayTrPaymentController extends Controller
{
    public function payTrPayment(Request $r)
    {
        $topup = $r->top_up;
        $amount = auth()->user()->top_up_price * $topup;

        $basket = [
            ['total',$amount, 1],
        ];

        $address = Auth::user()->address ?? 'Muğla';
        $phone = auth()->user()->phone;
        $name = auth()->user()->name;
        $email = auth()->user()->email;

        $totalAmountTL = $amount; // TL cinsinden tutar (DB'ye yazılacak)
        $amount = $amount * 100; // PayTR kuruş cinsinden istiyor

        $paytr = new PaytrService();
        $result = $paytr->getToken($name, $address, $phone, $email, $amount, $basket);

        if ($result['status'] === 'success') {
            $orderId = $this->generateGuid();
            $admin = Auth::guard('admin')->user();

            $topUp = TopupMovement::create([
                'admin_id'          => $admin->id,
                'top_up_price'      => $admin->top_up_price,
                'top_up'            => $topup,
                'type'              => 'yükleme',
                'is_approved'       => 0,
                'total_amount'      => $totalAmountTL,
                'created_by_user_id'=> Auth::guard('admin')->id(),
                'created_type'      => 'admin',
                'order_id'          => $orderId,
                'payment_details'   => null,
            ]);

            session()->put('orderID',$orderId);

            return view('admin.payment.paytr.form', ['token' => $result['token']]);
        } else {
            return back()->withErrors($result['reason']);
        }
    }

    public function paytrCallback(Request $request)
    {
        $merchantOid  = $_POST['merchant_oid'] ?? null;
        $status       = $_POST['status'] ?? null;
        $totalAmount  = $_POST['total_amount'] ?? null;
        $hashPost     = $_POST['hash'] ?? null;

        $hash = base64_encode(hash_hmac(
            'sha256',
            $merchantOid .
            $status .
            $totalAmount .
            config('payment.paytr.merchant_salt'),
            config('payment.paytr.merchant_key'),
            true
        ));

        if ($hash !== $hashPost) {
            Log::error('PayTR bad hash', $_POST);
            return response('PAYTR notification failed: bad hash', 400);
        }

        return response('OK');
    }

    public function payTrSuccess(Request $request)
    {
        $orderId = session('orderID');

        if (!$orderId) {
            return redirect()->route('admin.balance')->with('error', 'Oturum süresi dolmuş. Lütfen tekrar deneyin.');
        }

        $topUp = TopupMovement::where('order_id', $orderId)->first();

        if (!$topUp) {
            return redirect()->route('admin.balance')->with('error', 'İşlem kaydı bulunamadı.');
        }

        // Daha önce onaylanmamışsa bakiyeyi güncelle (çift işlemi önler)
        if (!$topUp->is_approved) {
            $admin = Admin::find($topUp->admin_id);
            if ($admin) {
                $admin->increment('top_up_balance', $topUp->top_up);
            }

            $topUp->update([
                'is_approved'     => 1,
                'is_paid'         => 1,
                'payment_details' => json_encode([
                    'amount'      => $topUp->total_amount,
                    'top_up'      => $topUp->top_up,
                    'top_up_price'=> $topUp->top_up_price,
                    'paid_at'     => now()->toDateTimeString(),
                ]),
            ]);

            session()->forget('orderID');
        }

        return redirect()->route('admin.balance')->with('success', $topUp->top_up . ' kontör başarıyla bakiyenize eklendi.');
    }

    public function payTrFail($merchantOid)
    {
        $orderService = app(PaymentService::class)->payment(false);
        return $this->handleOrderServiceResponse($orderService);
    }

    private function generateGuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
