<?php

namespace App\Helpers;

use App\Events\OrderNotification;
use App\Models\Admin;
use App\Models\AdminSystemFeature;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Printer;
use App\Models\Restaurant;
use App\Models\RestaurantSystemFeature;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Pusher\Pusher;

class OrdersHelper
{
    public static function addressReplace($address)
    {
        if ($address) {
            $keyword = "Notes:";
            $position = strpos($address, $keyword);

            if ($position !== false) {
                // "Notes:" kelimesinden önceki kısmı alıyoruz
                $address = substr($address, 0, $position);
            }

            return $address;
        } else {
            return null;
        }
    }

    public static function createOrderNotification($order): void
    {
        $id = Auth::guard('admin')->user()->id ?? 10;
        $restaurantIds = Restaurant::where('admin_id', $id)->pluck('id');

        if (Order::where('id', $order->id)->whereIn('restaurant_id', $restaurantIds)->exists()) {
            event(new OrderNotification($order));
        }
    }

    public static function generateVerifyCode(): string
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if (Order::where('verify_code', $code)->exists()) {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public static function isTopup($adminId, $restaurantId)
    {
        if ($restaurantId && !$adminId) {
            $adminId = Restaurant::find($restaurantId)->admin_id;
        }

        $admin = Admin::find($adminId);

        return $admin->top_up_balance > 0;
    }

    public static function updateTopup($adminId, $restaurantId)
    {
        if ($restaurantId && !$adminId) {
            $adminId = Restaurant::find($restaurantId)->admin_id;
        }

        $admin = Admin::find($adminId);

        if ($admin->top_up_balance > 0) {
            $admin->top_up_balance--;
            $admin->update();

            return true;
        } else {
            NotificationHelper::add([
                'title' => 'Yetersiz Kontör Bakiyesi',
                'description' => 'Üzgünüz, Kontor bakiyeniz yetersiz olduğu için ürün eklemesi yapılamıyor!!',
                'url' => route('admin.balance'),
                'admin_id' => $adminId ?? $admin->id,
            ]);

            return false;
        }
    }

    static function formatDistance($distanceKm)
    {
        if ($distanceKm >= 1) {
            // 1 km ve üzeri → kilometre cinsinden
            return number_format($distanceKm, 2) . " km";
        } elseif ($distanceKm >= 0.001) {
            // 1 metre ile 1 km arası → metre cinsinden
            return number_format($distanceKm * 1000, 2) . " m";
        } elseif ($distanceKm > 0) {
            // 1 mm ile 1 metre arası → santimetre cinsinden
            return number_format($distanceKm * 100000, 2) . " cm";
        } else {
            // Sıfır veya negatif mesafe
            return "0 m";
        }
    }

    static function haversineDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 6371)
    {
        // Dereceleri radyana çevir
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formülü
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    static function getOrderSystem($id)
    {
        return AdminSystemFeature::where('admin_id', Auth::guard('admin')->id())
            ->where('system_feature_id', $id)
            ->exists();
    }

    static function restaurantGetOrderSystem($id)
    {
        return RestaurantSystemFeature::where('restaurant_id', Auth::guard('admin')->id())
            ->where('system_feature_id', $id)
            ->exists();
    }

    static function getOrderData($orderId)
    {
        $order = Order::where('id', $orderId)->firstOrFail();
        $customerAddress = CustomerAddress::where('customer_id', $order->customer_id)
            ->where('restaurant_id', $order->restaurant_id)
            ->firstOrFail();

        $lat = $customerAddress->latitude;
        $lng = $customerAddress->longitude;

        $mapsUrl = "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}&zoom=18";

        $qrCode = QrCode::create($mapsUrl)
            ->setSize(50)       // 50 biraz küçük olabilir, büyütebilirsin
            ->setMargin(0);      // Beyaz kenarlıkları kaldırır

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $fileName = 'qr_' . $order->id . '.png';
        $filePath = public_path('qr/' . $fileName);

        if (!file_exists(public_path('qr'))) {
            mkdir(public_path('qr'), 0775, true);
        }

        $result->saveToFile($filePath);

        $qrUrl = asset('qr/' . $fileName);

        $data = [
            'id' => $order->id,
            'restaurant' => [
                'id' => $order->restaurant_id,
                'name' => Restaurant::find($order->restaurant_id)->restaurant_name,
                'logo' => asset('/theme/images/logo.png'),
            ],
            'platform' => $order->platform,
            'note' => $order->notes,
            'items' => json_decode($order->items, true),
            'discount' => $order->discount,
            'sub_amount' => $order->sub_amount,
            'amount' => $order->amount,
            'payment_method' => $order->payment_method,
            'customer' => [
                'name' => $order->full_name,
                'phone' => $order->phone,
                'address' => $order->address,
                'karekod' => $qrUrl
            ]
        ];

        return $data;
    }

    static function nowPrint($orderId, $printers)
    {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $orderData = self::getOrderData($orderId);
        $restaurant = Restaurant::where('id', $orderData['restaurant']['id'])->firstOrFail();

        $payload = [
            "restaurant_id" => $restaurant->id,
            "order" => $orderData,
            "printers" => $printers
        ];

        $channel = "GoKurye-" . $restaurant->id;

        $pusher->trigger($channel, "print-order", $payload);

        return true;
    }
}
