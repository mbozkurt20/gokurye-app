<?php
namespace App\Http\Controllers;

use App\Enums\EntegraStatusEnum;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntegraWebhookController extends Controller
{
    public function addOrder(Request $request)
    {
        Log::info('Gelen Data', (array)json_encode($request->all()));

        $orderData = $request->json()->all();

        $address = $orderData['client']['deliveryAddress'];
        $restaurant = Restaurant::where('entegra_restaurant_id',$orderData['restaurantId'])->first();

        $admin = $restaurant->admin;
        if (!$admin->top_up_balance){
            NotificationHelper::add([
                'title' => 'Yetersiz Kontör Bakiyesi',
                'description' => 'Üzgünüz, Kontor bakiyeniz yetersiz olduğu için ürün eklemesi yapılamıyor!!',
                'url' => route('admin.balance'),
                'admin_id' => $adminId ?? $admin->id,
            ]);

            return false;
        }

        $create  = Customer::where('email',$orderData['client']['id'])->first();
        if (!$create) {
            $create = new Customer();
            $create->restaurant_id = $restaurant->id; // Assuming the authenticated user is the restaurant
            $create->name =  $orderData['client']['name'];
            $create->phone = $orderData['client']['clientPhoneNumber'];
            $create->mobile = $orderData['client']['contactPhoneNumber'];
            $create->email = $orderData['client']['id'] ?? null;
            $create->save();

            if ($create) {
                $addr = new CustomerAddress();
                $addr->customer_id = $create->id;
                $addr->restaurant_id = $restaurant->id;
                $addr->name = $orderData['client']['name'];
                $addr->sokak_cadde =  $orderData['client']['deliveryAddress']['street'] ?? " ";
                $addr->bina_no = $orderData['client']['deliveryAddress']['aptNo'] ?? " ";
                $addr->city_id = ' ';
                $addr->kat = $orderData['client']['deliveryAddress']['floor'];;
                $addr->latitude = $orderData['client']['location']['lat'];
                $addr->longitude =$orderData['client']['location']['lon'];
                $addr->daire_no = $orderData['client']['deliveryAddress']['doorNo'];
                $addr->mahalle = ' ';
                $addr->adres_tarifi = $orderData['client']['deliveryAddress']['address'] ?? '';
                $addr->save();
            }
        }

        $items = [];

        foreach ($orderData['products'] as $item) {
            $items[] = [
                'price' => $item['totalPrice'],              // toplam fiyat
                'unitSellingPrice' => $item['price'],   // birim fiyat
                'totalOptionPrice' => $item['totalOptionPrice'],   // birim fiyat
                'totalPriceWithOption' => $item['totalPriceWithOption'],   // birim fiyat
                'quantity' => (string) $item['count'],    // string isteniyorsa
                'name' => $item['name']['tr'],        // ürün adı
                'image' => " ",        // ürün adı
            ];
        }

        switch ($orderData['paymentMethodText']['tr']) {
            case 'PAY_WITH_CARD':
                $payMethod = 'Online Kredi Kartı ile Ödeme';
                break;
            case 'CARD':
                $payMethod = 'Kapıda Kredi Kartı ile Ödeme';
                break;
            case 'CASH':
                $payMethod = 'Kapıda Nakit ile Ödeme';
                break;
            default:
                $payMethod = $orderData['paymentMethodText']['tr'];
        }

        switch ($orderData['provider']['slug']) {
            case 'ys':
                $platform = 'yemeksepeti';
                break;
            case 'getir':
                $platform = 'getir';
                break;
            case 'migros':
                $platform = 'migros';
                break;
            case 'ty':
                $platform = 'trendyol';
                break;
            default:
                $platform = $orderData['provider']['slug'];
                break;
        }

        switch ($orderData['status']){
            case EntegraStatusEnum::PENDING:
                $status = OrderStatus::PENDING;
                break;
            case EntegraStatusEnum::PREPARING:
                $status = OrderStatus::PREPARED;
                break;
        }
        $orderData = [
            'platform' => $platform,
            'customer_id' => $create->id,
            'pid' => $orderData['pid'] ?? null,
            'status_request_count' => 0,
            'restaurant_id' => $restaurant->id,
            'courier_id' => -1,
            'status' => $status,
            'tracking_id' => $orderData['shortCode'],
            'full_name' => $orderData['client']['name'],
            'phone' =>  $orderData['client']['contactPhoneNumber'],
            'payment_method' =>$payMethod,
            'items' => json_encode($items),
            'address' => $orderData['client']['deliveryAddress']['address'],
            'promotions' => json_encode([]),
            'coupon' => json_encode([]),
            'sub_amount' => $orderData['totalPrice'],
            'entegra_current_status' => $orderData['status'],
            'discount' => $orderData['totalDiscount'],
            'amount' => $orderData['totalDiscountedPrice'],
            'notes' => $orderData['clientNote']??null,
            'platform_date' => date('d-m-Y, H:i', strtotime($orderData['created_at'])),
            'distance' => OrdersHelper::haversineDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $orderData['client']['location']['lat'],
                $orderData['client']['location']['lon']
            )
        ];

        $order = Order::create($orderData);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function cancelOrder(Request $request)
    {
        $orderData = $request->json()->all();

        $order = Order::where('tracking_id' , $orderData['shortCode'])->first();

        $order->update([
            'status' => OrderStatus::UNSUPPLIED
        ]);

        return response()->json(['success' => true, 'order' => $order]);
    }
}
