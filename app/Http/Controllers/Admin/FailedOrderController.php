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
            return redirect()->route('admin.orders.show', $failedOrder->recovered_order_id)
                ->with('info', 'এই রেকর্ডটি ইতোমধ্যেই একটি অর্ডারে রূপান্তরিত হয়েছে।');
        }

        if (empty($failedOrder->customer_phone) || empty($failedOrder->customer_name)) {
            return redirect()->back()->with('error', 'গ্রাহকের নাম বা ফোন নম্বর ছাড়া অর্ডারে রূপান্তর করা সম্ভব নয়।');
        }

        DB::beginTransaction();
        try {
            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $failedOrder->customer_name,
                'customer_phone' => $failedOrder->customer_phone,
                'customer_email' => null,
                'shipping_address' => $failedOrder->shipping_address ?: 'ঠিকানা গ্রাহকের সাথে কথা বলে কনফার্ম করা হবে',
                'delivery_zone' => $failedOrder->delivery_zone ?: 'inside_dhaka',
                'payment_method' => $failedOrder->payment_method ?: 'cod',
                'subtotal' => $failedOrder->subtotal,
                'shipping_charge' => $failedOrder->shipping_charge,
                'discount_amount' => 0.00,
                'total_amount' => $failedOrder->total_amount,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'customer_note' => $failedOrder->customer_note . ' [Converted from Failed Order #' . $failedOrder->id . ']',
                'ip_address' => $failedOrder->ip_address,
            ]);

            // Create Order Items if cart items exist
            if (!empty($failedOrder->cart_items) && is_array($failedOrder->cart_items)) {
                foreach ($failedOrder->cart_items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['name'] ?? 'Product',
                        'product_price' => $item['unit_price'] ?? 0,
                        'quantity' => $item['quantity'] ?? 1,
                        'total_price' => $item['total_price'] ?? 0,
                    ]);
                }
            }

            $failedOrder->update([
                'is_recovered' => true,
                'status' => 'recovered',
                'recovered_order_id' => $order->id,
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
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
