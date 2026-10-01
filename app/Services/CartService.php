<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'shopping_cart';

    /**
     * Get normalized cart array: [itemKey => ['product_id' => ..., 'variant_id' => ..., 'quantity' => ...]]
     */
    public function getCart(): array
    {
        $raw = Session::get($this->sessionKey, []);
        $cart = [];

        foreach ($raw as $key => $val) {
            if (is_array($val)) {
                $cart[$key] = [
                    'product_id' => (int) ($val['product_id'] ?? $key),
                    'variant_id' => !empty($val['variant_id']) ? (int) $val['variant_id'] : null,
                    'quantity' => max(1, (int) ($val['quantity'] ?? 1)),
                ];
            } else {
                // Backward compatible with old integer quantity format
                $cart[$key] = [
                    'product_id' => (int) $key,
                    'variant_id' => null,
                    'quantity' => max(1, (int) $val),
                ];
            }
        }

        return $cart;
    }

    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        if (empty($cart)) {
            return $items;
        }

        $productIds = array_unique(array_column($cart, 'product_id'));
        $variantIds = array_filter(array_unique(array_column($cart, 'variant_id')));

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = !empty($variantIds) ? ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id') : collect();

        foreach ($cart as $itemKey => $cartItem) {
            $productId = $cartItem['product_id'];
            $variantId = $cartItem['variant_id'];
            $quantity = $cartItem['quantity'];

            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];
            $variant = $variantId && isset($variants[$variantId]) ? $variants[$variantId] : null;

            $price = $variant ? $variant->effective_price : (float) $product->current_price;
            $image = ($variant && $variant->image_url) ? $variant->image_url : $product->primary_image_url;

            $variantText = null;
            $color = null;
            $size = null;

            if ($variant) {
                $variantText = $variant->variant_name;
                $color = $variant->color;
                $size = $variant->size;
            }

            $items[] = [
                'item_key' => (string) $itemKey,
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $image,
                'color' => $color,
                'size' => $size,
                'variant_text' => $variantText,
                'unit_price' => $price,
                'quantity' => $quantity,
                'total_price' => $price * $quantity,
            ];
        }

        return $items;
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $cart = $this->getCart();
        $quantity = max(1, $quantity);

        $itemKey = $variantId ? "{$productId}_v{$variantId}" : (string) $productId;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'count' => $this->getCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function update(string|int $itemKey, int $quantity): array
    {
        $cart = $this->getCart();
        $itemKey = (string) $itemKey;

        if ($quantity <= 0) {
            unset($cart[$itemKey]);
        } elseif (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $quantity;
        }

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'count' => $this->getCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function remove(string|int $itemKey): array
    {
        $cart = $this->getCart();
        $itemKey = (string) $itemKey;

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
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
        $cart = $this->getCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
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
