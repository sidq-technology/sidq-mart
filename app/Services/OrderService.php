<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'SIDQ-' . date('Ymd') . '-' . rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function createOrder(array $data, CartService $cart): Order
    {
        return DB::transaction(function () use ($data, $cart) {
            $items = $cart->getItems();
            $subtotal = $cart->getSubtotal();

            $deliveryZone = $data['delivery_zone'] ?? 'inside_dhaka';
            $insideFee = (float) Setting::get('delivery_inside_dhaka', 70);
            $outsideFee = (float) Setting::get('delivery_outside_dhaka', 130);
            $freeThreshold = (float) Setting::get('free_delivery_threshold', 3000);

            $shippingCharge = ($deliveryZone === 'outside_dhaka') ? $outsideFee : $insideFee;

            if ($freeThreshold > 0 && $subtotal >= $freeThreshold) {
                $shippingCharge = 0.00;
            }

            $discountAmount = (float) ($data['discount_amount'] ?? 0.00);
            $grandTotal = max(0, ($subtotal + $shippingCharge - $discountAmount));

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id() ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'delivery_zone' => $deliveryZone,
                'shipping_charge' => $shippingCharge,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'payment_status' => ($data['payment_method'] ?? 'cod') === 'cod' ? 'pending' : 'pending',
                'transaction_id' => $data['transaction_id'] ?? null,
                'order_status' => 'pending',
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                ]);
            }

            $cart->clear();

            return $order;
        });
    }
}
