<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\MapHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Jobs\CheckCourierTimeoutJob;
use App\Models\Admin;
use App\Models\City;
use App\Models\District;
use App\Models\Printer;
use App\Models\Restaurant;
use App\Models\RestaurantCoupon;
use App\Services\PushNotificationService;
use App\Traits\RequestTrait;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use RequestTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($link)
    {
        if ($link == "tumu") {
            $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
            $orders = Order::where('status', 0)->where('restaurant_id', Auth::user()->id)->orderBy('id', 'desc')->whereDate('created_at', Carbon::today())->get();
            return view('restaurant.orders.index', compact('orders', 'couriers'));
        } else {
            $orders = Order::where('status', 0)->where('platform', $link)->where('restaurant_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();
            $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
            if ($orders) {
                return view('restaurant.orders.index', compact('orders', 'couriers'));
            } else {
                $orders = [];
                return view('restaurant.orders.index', compact('orders'));
            }
        }
    }

    public function sendCourier(Request $request, $orderId, $courierId)
    {
        $order = Order::find($orderId);

        //-1 kuryeyi siparişten çıkarır
        if ($courierId == -1) {
            $order->update([
                'courier_id' => null,
                'assigned_at' => null
            ]);

            CourierOrder::where('order_id', $order->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Siparişin kuryesi başarıyla kaldırıldı.'
            ]);
        } else {
            $courier = Courier::find($courierId);

            if (!$order || !$courier) {
                return response()->json(['success' => false, 'message' => 'Sipariş veya Kurye bulunamadı.'], 404);
            }

            $customer = Customer::find($order->customer_id);

            // Mesafe hesaplama
            $distance = MapHelper::getGoogleDistance(
                $courier->latitude, $courier->longitude,
                $customer->latitude, $customer->longitude
            );

            if ($distance === null) {
                $distance = OrdersHelper::haversineDistance(
                    $courier->latitude, $courier->longitude,
                    $customer->latitude, $customer->longitude
                );
            }

            // Gelen veri zaten KM ise /1000 yapma, Metre ise yap.
            // haversineDistance genelde Metre döner:
            $distanceInKm = $distance / 1000;
            $restaurant = Restaurant::with('admin')->find($order->restaurant_id);
            $restaurantDistance = $restaurant->admin->distance_limit ?? 50;

            if ($distanceInKm > $restaurantDistance) {
                return response()->json([
                    'success' => false,
                    'message' => "Mesafe çok uzak (" . round($distanceInKm, 2) . " km). {$restaurantDistance} km'den uzak yerlere atama yapılamaz."
                ], 400);
            }

            // İşlemleri gerçekleştir
            $order->courier_id = $courier->id;
            $order->assigned_at = now();
            $order->status = OrderStatus::PREPARED;
            $order->update();

            // Job ve diğer işlemler
            CheckCourierTimeoutJob::dispatch($order->id)->delay(now()->addMinutes(2));

            $courier->last_assigned_at = now();
            $courier->update();

            CourierOrder::firstOrCreate([
                'courier_id' => $courier->id,
                'order_id' => $order->id
            ]);

            $restaurant = Restaurant::find($order->restaurant_id);

            // Bildirimler
            if ($courier->fcm_token) {
                $ser = new PushNotificationService();
                $ser->sendNotification($courier->fcm_token, $restaurant->restaurant_name . ' Restorandan Yeni Sipariş Atandı', 'Sipariş Takip Kodu:' . $order->tracking_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kurye Başarıyla Atandı.'
            ]);
        }
    }

    public function new()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $categories = Categorie::where('status', 'active')
            ->where('restaurant_id', Auth::user()->id)
            ->orderBy('desk', 'asc')
            ->with(['products' => function($q) {
                $q->where('status', 'active')->where('restaurant_id', Auth::user()->id);
            }])
            ->get();
        $coupons = RestaurantCoupon::where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.orders.new', compact('customers', 'couriers', 'categories', 'coupons'));
    }

    public function addPOS($id)
    {
        $product = Product::find($id);
        $userId = Auth::user()->id;

        $pos = \Cart::session($userId)->getContent();

        if (count($pos) > 0) {
            \Cart::session($userId)->add(array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => array(),
                'associatedModel' => $product
            ));

            $items = '<div id="posItem_' . $product->id . '"
                class="item select-none"
              style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
                     background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

            <!-- Ürün Görseli -->
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>

            <!-- Ürün Bilgileri -->
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>

            <!-- Adet Butonları -->
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="1" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #259a38; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';

            if (\Cart::session($userId)->get($id)->quantity > 1) {
                $durum = "var";
            } else {
                $durum = "yok";
            }
        } else {
            \Cart::session($userId)->add(array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => array(),
                'associatedModel' => $product
            ));

            $durum = "yok";

            $items = '<div id="posItem_' . $product->id . '"
                class="item select-none"
              style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
                     background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

            <!-- Ürün Görseli -->
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>

            <!-- Ürün Bilgileri -->
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>

            <!-- Adet Butonları -->
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="1" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #259a38; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['items' => $items, 'posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'durum' => $durum, 'total' => $total]);
    }

    public function updatePlusPOS($id)
    {
        $userId = Auth::user()->id;

        \Cart::session($userId)->update($id, array(
            'quantity' => +1,
        ));

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'total' => $total]);
    }

    public function getPosItems()
    {
        $userId = Auth::user()->id;
        $cart = \Cart::session($userId)->getContent();

        $items = '';

        foreach ($cart as $item) {
            $product = $item->associatedModel;
            $quantity = $item->quantity;

            $items .= '<div id="posItem_' . $product->id . '" class="item select-none" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="' . $quantity . '" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #259a38; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json([
            'items' => $items,
            'posTotalItem' => $posTotalItem,
            'posTotal' => $posTotal,
            'total' => $total,
            ' durum' => 'var',
        ]);
    }

    public function message(Request $request)
    {
        $order = Order::where('restaurant_id', Auth::user()->id)->where('tracking_id', $request->tracking_id)->first();
        $order->message = $request->message;
        $s = $order->save();

        if ($s) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function storeQuick(Request $request)
    {
        try {
            DB::beginTransaction();

            $restaurant = Restaurant::find($request->restaurant_id);

            if (!OrdersHelper::isTopup(null, $restaurant->id)) {
                \App\Helpers\NotificationHelper::add([
                    'title'       => 'Yetersiz Kontör Bakiyesi',
                    'description' => 'Kontör bakiyeniz yetersiz olduğu için hızlı sipariş oluşturulamadı. Lütfen bakiye yükleyin.',
                    'url'         => route('admin.balance'),
                    'admin_id'    => $restaurant->admin_id,
                ]);
                DB::rollBack();
                return response()->json(['status' => 'BalanceError', 'message' => 'Yetersiz Kontör Bakiyesi']);
            }
            $restaurantId = $restaurant->id;
            $city = City::find(Admin::find($restaurant->admin_id)->city_id);

            $customer = Customer::where('phone', $request->phone)->where('restaurant_id', $restaurantId)->first();

            if (!$customer) {
                $customer = new Customer();
                $customer->restaurant_id = $restaurantId;
                $customer->name = $request->input('full_name');
                $customer->phone = $request->input('phone');
                $customer->mobile = $request->input('mobile');
                $customer->email = $request->input('email') ?? null;
                $customer->save();
            }

            $address = CustomerAddress::where('customer_id', $customer->id)
                ->where('restaurant_id', $restaurantId)
                ->where('mahalle', $request->mahalle)
                ->where('sokak_cadde', $request->sokak_cadde)
                ->where('kat', $request->kat)
                ->where('daire_no', $request->daire_no)
                ->first();

            if (!$address) {
                $address = new CustomerAddress();
                $address->customer_id = $customer->id;
                $address->restaurant_id = $restaurantId;
                $address->name = 'Hızlı Sipariş';
                $address->sokak_cadde = $request->sokak_cadde;
                $address->bina_no = $request->bina_no;
                $address->kat = $request->kat;
                $address->city_id = $city->id;
                $address->district_id = $request->ilce;
                $address->adres_tarifi = $request->adress_tarifi;
                $address->latitude = $request->latitude;
                $address->longitude = $request->longitude;
                $address->daire_no = $request->daire_no;
                $address->mahalle = $request->mahalle;
                $address->save();
            }

            $distance = MapHelper::getGoogleDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $address->latitude,
                $address->longitude
            );

            if ($distance === null) {
                $distance = OrdersHelper::haversineDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $address->latitude,
                    $address->longitude
                );
            }

            $order = \App\Models\Order::create([
                'platform' => 'telefonsiparis',
                'courier_id' => $request->courier_id ?? -1,
                'customer_id' => $customer->id,
                'restaurant_id' => $request->restaurant_id,
                'tracking_id' => "POS-" . rand(9, 99999),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->adress_tarifi,
                'payment_method' => $request->payment_method,
                'sub_amount' => $request->amount,
                'discount' => 0.00,
                'amount' => $request->amount,
                'status' => OrderStatus::PENDING,
                'items' => json_encode([]),
                'distance' => $distance
            ]);

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Sipariş Alınamadı!!']);
            }

            DB::commit();

            return response()->json(['message' => 'Sipariş Başarıyla Alındı.']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Hata loglama yapılabilir
            return response()->json(['message' => 'Sipariş İşleminde Hata Oluştu.', 'error' => $e->getMessage()], 500);
        }

    }

    public function message2(Request $request)
    {
        $order = Order::where('restaurant_id', Auth::user()->id)->where('tracking_id', $request->tracking_id)->first();
        $order->message2 = $request->message2;
        $s = $order->save();

        if ($s) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function updateMinusPOS($id, $qty)
    {
        $userId = Auth::user()->id;

        if ($qty <= 1) {
            \Cart::session($userId)->remove($id);
        } else {
            \Cart::session($userId)->update($id, array(
                'quantity' => -1,
            ));
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'total' => $total]);
    }

    public function removePOS()
    {
        $userId = Auth::user()->id;
        \Cart::session($userId)->clear();

        return response()->json(['message' => 'Temizlendi']);
    }

    public function customerpos($id)
    {
        // Müşteri bulunuyor mu kontrol et
        $custom = Customer::find($id);

        if (!$custom) {
            return response()->json(['error' => 'Customer not found'], 404); // Müşteri bulunamazsa 404 hatası döndür
        }

        // Adres bulunuyor mu kontrol et
        $adres = CustomerAddress::where('customer_id', $id)->first();

        // Müşteri adresi bulunamazsa alternatif bir mesaj göster
        if (!$adres) {
            $customer = '<p class="text-white mr-2 logo-text">' . $custom->name . '</p> ' . '<span class="ml-2 text-white"> - ' . $custom->phone . '</span>' . ' <br><span>Adres bulunamadı</span>';
        } else {
            // Müşteri ve adres bilgilerini birleştir
            $customer = '<p class="text-white mr-2 logo-text">' . $custom->name . '</p> ' . '<span class=" ml-2 text-white"> - ' . $custom->phone . '</span>' . ' <br><span>' . $adres->mahalle . ' Mah.' . $adres->sokak_cadde . '.No:' . $adres->bina_no . ' Kat:' . $adres->kat . ' Daire:' . $adres->daire_no . '</span>';
        }

        return response()->json(['customer' => $customer]);
    }

    public function addOrder(Request $request)
    {
        if (!OrdersHelper::isTopup(null, Auth::user()->id)) {
            $adminId = \App\Models\Restaurant::find(Auth::user()->id)?->admin_id;
            if ($adminId) {
                \App\Helpers\NotificationHelper::add([
                    'title'       => 'Yetersiz Kontör Bakiyesi',
                    'description' => 'Kontör bakiyeniz yetersiz olduğu için sipariş oluşturulamadı. Lütfen bakiye yükleyin.',
                    'url'         => route('admin.balance'),
                    'admin_id'    => $adminId,
                ]);
            }
            return response()->json(['status' => "BalanceError", 'message' => 'Yetersiz Kontör Bakiyesi']);
        }

        $customer = Customer::where('id', $request->customer_id)
            ->where('restaurant_id', Auth::user()->id)
            ->first();

        if (!$customer) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri bulunamadı']);
        }

        $customer_address = CustomerAddress::where('customer_id', $request->customer_id)->first();
        if (!$customer_address) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri Adresi Bulunamadı']);
        }

        if (!is_array($request->products) || count($request->products) === 0) {
            return response()->json(['status' => "ERR", 'message' => 'Ürünler listesi boş veya geçersiz.']);
        }

        $discount = 0;;
        $coupon = [];
        if ($request->coupon_id) {
            $coupon = RestaurantCoupon::find($request->coupon_id);

            $coupon = [
                'couponId' => $coupon->coupon_id,
                'description' => $coupon->name,
                'totalSellerAmount' => $coupon->total_seller_amount,
            ];

            $discount = (float)$coupon['totalSellerAmount'];
        }

        $order = new Order();
        $order->restaurant_id = Auth::user()->id;
        $order->customer_id = $request->customer_id;
        $order->full_name = $customer->name;
        $order->coupon = json_encode($coupon);
        $order->address = $customer_address->mahalle . " Mah. " . $customer_address->sokak_cadde . " Cad/Sk. Apt." . $customer_address->bina_no . ". Kat:" . $customer_address->kat . ". Daire No:" . $customer_address->daire_no;
        $order->phone = $customer->phone;
        $order->discount = $discount;
        $order->sub_amount = $request->amount;
        $order->amount = $request->amount - $discount;
        $order->platform = "telefonsiparis";
        $order->tracking_id = "POS-" . rand(9, 99999);
        $order->payment_method = $request->payment_method;
        $order->courier_id = $request->courier_id > 0 ? $request->courier_id : -1;

        $restaurant = Restaurant::find($order->restaurant_id);
        $order->distance = OrdersHelper::haversineDistance(
            $restaurant->latitude,
            $restaurant->longitude,
            $customer_address->latitude,
            $customer_address->longitude
        );

        $items = [];
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
            if (!$product) {
                return response()->json(['status' => "ERR", 'message' => 'Ürün bulunamadı: ' . $productData['product_id']]);
            }

            $items[] = [
                "price" => $product->price,
                "unitSellingPrice" => $product->price,
                "quantity" => $productData["quantity"],
                "name" => $product->name,
            ];
        }

        $order->items = json_encode($items);
        $order->status = OrderStatus::PENDING;

        try {
            $order->save();

            // Stok düşümü
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                if ($product && $product->stock_enabled && $product->stock !== null) {
                    $product->stock = max(0, $product->stock - (int)$productData['quantity']);
                    $product->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['status' => "ERR", 'message' => 'Sipariş kaydedilirken bir hata oluştu.']);
        }

        $printed = $this->generateInvoice($order);

        //sipariş eklenince ses bildirimi gerçekleştirir.
        OrdersHelper::createOrderNotification($order);

        return response()->json(['status' => "OK", 'printed' => $printed]);
    }


    private function generateInvoice($order)
    {
        $date = now()->format("d.m.Y H:i:s");

        return view('invoice-template', compact('order', 'date'))->render();  // Fatura görünümü burada oluşturulacak
    }

    public function updateOrderStatus(Request $request)
    {
        $trackingId = $request->input('tracking_id');
        $action = $request->input('action');
        $message = $request->input('message');

        $order = Order::where('tracking_id', $trackingId)->with(['restaurant', 'courier'])->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        switch ($action) {
            case OrderStatus::UNSUPPLIED:
                $courier = Order::where('tracking_id', $trackingId)->where('courier_id', '!=', -1)->with('courier')->first();
                if ($courier) {
                    $courier->status = CourierStatus::active;
                    $courier->update();

                    $order->courier_id = -1;
                    $order->update();

                    $courierOrder = CourierOrder::where('order_id', $order->id)->where('courier_id', $courier->id)->first();
                    if ($courierOrder) {
                        $courierOrder->delete();
                    }

                    if (OrdersHelper::getOrderSystem(3)) {
                        NotificationHelper::add([
                            'title' => 'Restaurant Paketi İptal Etti',
                            'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  Restaurant tarafından iptal edildi.',
                            'url' => route('admin.balance')
                        ]);
                    }
                }
                break;

            case OrderStatus::DELIVERED:
                // Sipariş teslim edildiğinde kuryenin durumu güncelleniyor
                $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                if ($courierOrder) {
                    $courier = Courier::find($courierOrder->courier_id);
                    if ($courier) {
                        // Kuryenin durumunu güncelle
                        $courier->status = CourierStatus::active;;
                        $courier->update();
                        Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                    }
                }
                break;
        }

        $order->status = $action;
        $order->message = $message;
        $saveStatus = $order->update();

        if ($saveStatus) {
            return response()->json(['status' => "OK", 'order' => $order]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function printed($orderId)
    {
        $order = Order::where('id', $orderId)->firstOrFail();

        $printers = Printer::where('payable_type', 'restaurant')->where('payable_id', $order->restaurant_id)->pluck('name')->toArray();

        if (count($printers) > 0) {
            OrdersHelper::nowPrint($order->id, $printers);
        }
    }

    public function deleteOrder($id)
    {
        $order = Order::where('id', $id)->first();

        $order->delete();

        if ($order) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function checkOrders()
    {
        $orderCount = Order::where('status', 'PENDING')->count();
        return response()->json(['orderCount' => $orderCount]);
    }
}
