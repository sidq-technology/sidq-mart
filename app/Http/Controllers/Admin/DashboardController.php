<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $metrics = [
            'total_sales' => Order::where('order_status', '!=', 'cancelled')->sum('grand_total'),
            'today_sales' => Order::whereDate('created_at', $today)->where('order_status', '!=', 'cancelled')->sum('grand_total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'processing_orders' => Order::where('order_status', 'processing')->count(),
            'delivered_orders' => Order::where('order_status', 'delivered')->count(),
            'cancelled_orders' => Order::where('order_status', 'cancelled')->count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock_quantity', '<=', 5)->count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->take(5)->get();

        return view('admin.dashboard', compact('metrics', 'recentOrders', 'lowStockProducts'));
    }
}
