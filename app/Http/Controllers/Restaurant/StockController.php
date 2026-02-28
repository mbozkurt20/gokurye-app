<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->id;
        $products = Product::where('restaurant_id', $restaurantId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('restaurant.stock.index', compact('products'));
    }

    public function update(Request $request)
    {
        $restaurantId = Auth::user()->id;
        $updates = $request->input('stock', []);

        foreach ($updates as $productId => $data) {
            $product = Product::where('id', $productId)
                ->where('restaurant_id', $restaurantId)
                ->first();
            if (!$product) continue;

            $product->stock_enabled = isset($data['enabled']) ? 1 : 0;
            $product->stock = $product->stock_enabled ? (int)($data['qty'] ?? 0) : null;
            $product->save();
        }

        return redirect()->back()->with('success', 'Stok bilgileri güncellendi.');
    }

    public function adjust(Request $request, $id)
    {
        $restaurantId = Auth::user()->id;
        $product = Product::where('id', $id)->where('restaurant_id', $restaurantId)->firstOrFail();

        $action = $request->input('action'); // add / subtract / set
        $qty    = (int)$request->input('qty', 0);

        if ($action === 'add') {
            $product->stock = ($product->stock ?? 0) + $qty;
        } elseif ($action === 'subtract') {
            $product->stock = max(0, ($product->stock ?? 0) - $qty);
        } else {
            $product->stock = $qty;
        }
        $product->stock_enabled = true;
        $product->save();

        return response()->json(['stock' => $product->stock]);
    }
}
