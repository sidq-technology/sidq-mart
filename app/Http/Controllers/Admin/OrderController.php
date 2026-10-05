<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $orders = Order::with(['items.product', 'user'])
            ->when($status, function ($q) use ($status) {
                $q->where('order_status', $status);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'shipped' => Order::where('order_status', 'shipped')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->input('order_status');

        $order->update([
            'order_status' => $newStatus,
            'admin_notes' => $request->input('admin_notes', $order->admin_notes),
        ]);

        if ($oldStatus !== $newStatus) {
            try {
                app(\App\Services\SmsService::class)->sendOrderNotification($order, $newStatus);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Order {$newStatus} SMS Trigger Error: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'অর্ডারের স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
    }

    public function invoice(Order $order)
    {
        $order->load(['items.product', 'user']);
        $siteName = Setting::get('site_name', 'SIDQ MART');
        $contactPhone = Setting::get('contact_phone', '');
        $contactAddress = Setting::get('contact_address', '');

        return view('admin.orders.invoice', compact('order', 'siteName', 'contactPhone', 'contactAddress'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'অর্ডারটি মুছে ফেলা হয়েছে।');
    }
}
