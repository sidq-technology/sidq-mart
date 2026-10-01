<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FailedOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FailedOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = FailedOrder::query()->with('recoveredOrder');

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'unrecovered') {
                $query->where('is_recovered', false);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search Filter (Phone or Name or IP)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $failedOrders = $query->latest()->paginate(20)->withQueryString();

        // Metrics & KPI counters
        $stats = [
            'total_unrecovered' => FailedOrder::where('is_recovered', false)->count(),
            'today_unrecovered' => FailedOrder::where('is_recovered', false)->whereDate('created_at', today())->count(),
            'total_recovered' => FailedOrder::where('is_recovered', true)->count(),
            'lost_revenue' => FailedOrder::where('is_recovered', false)->sum('total_amount'),
        ];

        return view('admin.failed_orders.index', compact('failedOrders', 'stats'));
    }

    public function show($id)
    {
        $failedOrder = FailedOrder::with('recoveredOrder')->findOrFail($id);

        return view('admin.failed_orders.show', compact('failedOrder'));
    }

    public function updateStatus(Request $request, $id)
    {
        $failedOrder = FailedOrder::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:abandoned,attempted,contacted,recovered',
            'contact_notes' => 'nullable|string|max:1000',
        ]);

        $failedOrder->update([
            'status' => $validated['status'],
            'contact_notes' => $validated['contact_notes'] ?? $failedOrder->contact_notes,
            'is_recovered' => ($validated['status'] === 'recovered') ? true : $failedOrder->is_recovered,
        ]);

        return redirect()->back()->with('success', 'ফেইল্ড অর্ডারের স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
    }

    public function convertToOrder($id)
    {
        $failedOrder = FailedOrder::findOrFail($id);

        if ($failedOrder->is_recovered && $failedOrder->recovered_order_id) {
            return redirect()->back()
                ->with('info', 'এই রেকর্ডটি ইতোমধ্যেই একটি অর্ডারে রূপান্তরিত হয়েছে।');
        }

        if (empty($failedOrder->customer_phone) || empty($failedOrder->customer_name)) {
            return redirect()->back()->with('error', 'গ্রাহকের নাম বা ফোন নম্বর ছাড়া অর্ডারে রূপান্তর করা সম্ভব নয়।');
        }

        DB::beginTransaction();
        try {
            // Generate unique order number (SIDQ standard format)
            do {
                $orderNumber = 'SIDQ-' . date('Ymd') . '-' . rand(1000, 9999);
            } while (Order::where('order_number', $orderNumber)->exists());

            $subtotal = (float) ($failedOrder->subtotal ?? 0);
            $shippingCharge = (float) ($failedOrder->shipping_charge ?? 70);
            $discountAmount = 0.00;
            $grandTotal = (float) ($failedOrder->total_amount ?? ($subtotal + $shippingCharge));
            if ($grandTotal <= 0 && ($subtotal + $shippingCharge) > 0) {
                $grandTotal = $subtotal + $shippingCharge;
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => null,
                'customer_name' => $failedOrder->customer_name,
                'customer_phone' => $failedOrder->customer_phone,
                'shipping_address' => $failedOrder->shipping_address ?: 'ঠিকানা গ্রাহকের সাথে কথা বলে কনফার্ম করা হবে',
                'delivery_zone' => $failedOrder->delivery_zone ?: 'inside_dhaka',
                'shipping_charge' => $shippingCharge,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'payment_method' => $failedOrder->payment_method ?: 'cod',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'customer_note' => $failedOrder->customer_note ? ($failedOrder->customer_note . ' [Converted from Failed Order #' . $failedOrder->id . ']') : ('[Converted from Failed Order #' . $failedOrder->id . ']'),
                'ip_address' => $failedOrder->ip_address,
                'admin_notes' => 'Converted from abandoned/failed order #' . $failedOrder->id . ' by admin',
            ]);

            // Create Order Items if cart items exist
            if (!empty($failedOrder->cart_items) && is_array($failedOrder->cart_items)) {
                foreach ($failedOrder->cart_items as $item) {
                    $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                    $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                    $totalPrice = (float) ($item['total_price'] ?? ($unitPrice * $qty));

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'variant_id' => $item['variant_id'] ?? null,
                        'product_name' => $item['name'] ?? $item['product_name'] ?? 'Product',
                        'color' => $item['color'] ?? null,
                        'size' => $item['size'] ?? null,
                        'product_image' => $item['image'] ?? $item['product_image'] ?? null,
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'total_price' => $totalPrice,
                    ]);
                }
            }

            // Delete from failed orders list as it is now converted into an active order
            $failedOrder->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', "ফেইল্ড অর্ডারটি সফলভাবে মূল অর্ডারে (#{$order->order_number}) রূপান্তর করা হয়েছে!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'অর্ডারে রূপান্তর করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $failedOrder = FailedOrder::findOrFail($id);
        $failedOrder->delete();

        return redirect()->back()->with('success', 'ফেইল্ড অর্ডারের তথ্য সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
