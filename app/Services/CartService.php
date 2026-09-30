<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'shopping_cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        if (empty($cart)) {
            return $items;
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $productId => $quantity) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $price = (float) $product->current_price;
                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->primary_image_url,
                    'unit_price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $price * $quantity,
                ];
            }
        }

        return $items;
    }

    public function add(int $productId, int $quantity = 1): array
    {
        $cart = $this->getCart();
        $quantity = max(1, $quantity);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'count' => $this->getCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function update(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'count' => $this->getCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function remove(int $productId): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put($this->sessionKey, $cart);
        }

        return [
            'success' => true,
            'count' => $this->getCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
        Session::forget('applied_coupon');
    }

    public function getCount(): int
    {
        return array_sum($this->getCart());
    }

    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        $items = $this->getItems();

        foreach ($items as $item) {
            $subtotal += $item['total_price'];
        }

        return round($subtotal, 2);
    }
}
