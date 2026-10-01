<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        $items = $cart->getItems();
        $subtotal = $cart->getSubtotal();
        $count = $cart->getCount();

        return view('frontend.cart', compact('items', 'subtotal', 'count'));
    }

    public function add(Request $request, CartService $cart)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);
        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;

        $result = $cart->add($productId, $quantity, $variantId);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'পণ্যটি কার্টে যোগ করা হয়েছে!',
                'count' => $result['count'],
                'subtotal' => $result['subtotal'],
                'items' => $cart->getItems(),
            ]);
        }

        return redirect()->back()->with('success', 'পণ্যটি কার্টে যোগ করা হয়েছে!');
    }

    public function update(Request $request, CartService $cart)
    {
        $itemKey = $request->input('item_key') ?? $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $result = $cart->update($itemKey, $quantity);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $result['count'],
                'subtotal' => $result['subtotal'],
                'items' => $cart->getItems(),
            ]);
        }

        return redirect()->back();
    }

    public function remove(Request $request, CartService $cart)
    {
        $itemKey = $request->input('item_key') ?? $request->input('product_id');
        $result = $cart->remove($itemKey);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'পণ্যটি কার্ট থেকে সরানো হয়েছে!',
                'count' => $result['count'],
                'subtotal' => $result['subtotal'],
                'items' => $cart->getItems(),
            ]);
        }

        return redirect()->back()->with('info', 'পণ্যটি কার্ট থেকে সরানো হয়েছে!');
    }

    public function drawer(CartService $cart)
    {
        return response()->json([
            'count' => $cart->getCount(),
            'subtotal' => $cart->getSubtotal(),
            'items' => $cart->getItems(),
        ]);
    }
}
