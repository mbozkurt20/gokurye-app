<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ProgressPaymentRecord;
use Illuminate\Http\Request;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use App\Models\CourierOrder;

class ProgressPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function restaurant(){
        $records = ProgressPaymentRecord::where('payable_type','restaurant')->get();
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.progressPayment.restaurant', compact('restaurants','records'));
    }
    public function courier(){
        $records = ProgressPaymentRecord::where('payable_type','courier')->get();

        $courierss = Courier::where('admin_id', auth()->id())->where('restaurant_id', 0)->get();
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.progressPayment.courier', compact('courierss','restaurants','records'));
    }

    public function newRecords(){
        return view('admin.progressPayment.records.new');
    }

    public function storeRecords(Request $request){
        $data = $request->validate([
            'payable_type' => 'required|in:restaurant,courier',
            'payable_id' => 'required|integer',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
            'amount' => 'required|string'
        ]);

        ProgressPaymentRecord::create([
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Ödeme kaydedildi.');
    }

    public function deleteRecords($recordId){
        $record = ProgressPaymentRecord::find($recordId);
        $del = $record->delete();

        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }

    public function restaurantFilter(Request $request){
        $restaurant = Restaurant::find($request->restaurant);

        $startDate = Carbon::createFromFormat('Y-m-d', $request->start)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end)->endOfDay();

        $orderCount = Order::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $paidAmount = ProgressPaymentRecord::where('payable_type', 'restaurant')
            ->where('payable_id', $restaurant->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $records = ProgressPaymentRecord::where('payable_type', 'restaurant')
            ->where('payable_id', $restaurant->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalProgressPayment = floatval($restaurant->package_price) * $orderCount;

        // Tabloda göstermek için kayıtları HTML'e çevireceğiz
        $recordsHtml = view('admin.progressPayment._records', compact('records'))->render();

        return response()->json([
            'paidAmount' => number_format($paidAmount, 2, '.', ''), // 2 basamak
            'restaurant_name' => $restaurant->restaurant_name,
            'order_count' => $orderCount,
            'total_progress_payment' => number_format($totalProgressPayment, 2, '.', ''), // 2 basamak
            'records_html' => $recordsHtml
        ]);
    }

    public function courierFilter(Request $request) {
        $courier = Courier::find($request->courier);

        $startDate = Carbon::createFromFormat('Y-m-d', $request->start)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end)->endOfDay();

        // Temel Sorgular
        $deliveredOrders = Order::where('courier_id', $courier->id)
            ->where('status', OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $orderCount = $deliveredOrders->count();

        $paidAmount = ProgressPaymentRecord::where('payable_type', 'courier')
            ->where('payable_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $records = ProgressPaymentRecord::where('payable_type', 'courier')
            ->where('payable_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        $recordsHtml = view('admin.progressPayment._courier_records', compact('records'))->render();

        // Hakediş Hesaplama
        $total = 0;
        $info = "";
        $fixedTotal = 0;

        if ($courier->price_type == 'package') {
            $unitPrice = (float) $courier->price;
            $total = $orderCount * $unitPrice;

            $info = "Hakediş, teslim edilen {$orderCount} paket üzerinden, paket başı " . number_format($unitPrice, 2) . " TL olarak hesaplanmıştır.";
        } else {
            $kmPrice = (float) $courier->km_price;
            $fixedPrice = (float) $courier->fixed_price;
            $externalKm = (float) $courier->km_distance_later; // Hariç tutulacak mesafe (km)

            $distanceTotal = $deliveredOrders->sum(function($o) use ($kmPrice, $externalKm) {
                // String gelen "4.708..." değerini sayıya çeviriyoruz.
                // Eğer veritabanında metre ise /1000 yapın, doğrudan km ise bölmeyin.
                $orderKm = (float) $o->distance;
                $payableKm = max(0, $orderKm - $externalKm); // Muafiyeti düş, eksiye inme
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $total = $distanceTotal + $fixedTotal;

            $info = "Paket başı sabit " . number_format($fixedPrice, 2) . " TL'ye ek olarak; sipariş mesafelerinden ilk {$externalKm} km düşülerek, kalan mesafe için km başına " . number_format($kmPrice, 2) . " TL eklenmiştir.";
        }

        return response()->json([
            'paidAmount' => number_format($paidAmount, 2, '.', ''),
            'courier' => $courier,
            'order_count' => $orderCount,
            'fixed_amount' => number_format($fixedTotal, 2, '.', ''),
            'total_progress_payment' => number_format($total, 2, '.', ''),
            'calculation_info' => $info, // Frontend'de göstermek için ekledik
            'records_html' => $recordsHtml,
        ]);
    }
}
