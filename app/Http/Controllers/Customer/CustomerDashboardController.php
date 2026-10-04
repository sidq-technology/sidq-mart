<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerDashboardController extends Controller
{
    /**
     * Customer Profile & Orders Dashboard Overview.
     */
    public function index()
    {
        $user = Auth::user();

        // Match orders by user_id OR customer phone
        $ordersQuery = Order::query()->where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if (!empty($user->phone)) {
                $q->orWhere('customer_phone', $user->phone);
            }
        });

        $recentOrders = (clone $ordersQuery)->latest()->take(5)->get();

        $stats = [
            'total_orders'     => (clone $ordersQuery)->count(),
            'pending_orders'   => (clone $ordersQuery)->whereIn('order_status', ['pending', 'processing'])->count(),
            'delivered_orders' => (clone $ordersQuery)->where('order_status', 'delivered')->count(),
            'total_spent'      => (clone $ordersQuery)->where('order_status', 'delivered')->sum('grand_total'),
        ];

        return view('customer.dashboard', compact('user', 'recentOrders', 'stats'));
    }

    /**
     * All orders placed by this customer.
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::query()->where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if (!empty($user->phone)) {
                $q->orWhere('customer_phone', $user->phone);
            }
        });

        if ($status = $request->input('status')) {
            $query->where('order_status', $status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders', compact('orders', 'user'));
    }

    /**
     * Show single order details.
     */
    public function showOrder(Order $order)
    {
        $user = Auth::user();

        // Authorization check
        $isOwner = ($order->user_id === $user->id) ||
                   (!empty($user->phone) && $order->customer_phone === $user->phone);

        if (!$isOwner && !$user->hasAdminAccess()) {
            abort(403, 'এই অর্ডারের তথ্য দেখার অনুমতি আপনার নেই।');
        }

        $order->load('items.product');

        return view('customer.order-details', compact('order'));
    }

    /**
     * Update customer profile details.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->phone   = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'আপনার প্রোফাইল তথ্য সফলভাবে আপডেট হয়েছে।');
    }
}
