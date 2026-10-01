<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        if (!in_array($period, ['today', '7_days', '30_days', 'all'])) {
            $period = 'all';
        }

        $today = Carbon::today();

        // Base query for period-filtered orders
        $orderQuery = Order::query();

        if ($period === 'today') {
            $orderQuery->whereDate('created_at', $today);
        } elseif ($period === '7_days') {
            $orderQuery->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        } elseif ($period === '30_days') {
            $orderQuery->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay());
        }
        // 'all' has no date constraint

        // Filtered metrics
        $periodSales = (clone $orderQuery)->where('order_status', '!=', 'cancelled')->sum('grand_total');
        $periodOrders = (clone $orderQuery)->count();
        $periodPendingOrders = (clone $orderQuery)->where('order_status', 'pending')->count();
        $periodDeliveredSales = (clone $orderQuery)->where('order_status', 'delivered')->sum('grand_total');

        // All-time & Today fixed metrics
        $todaySales = Order::whereDate('created_at', $today)->where('order_status', '!=', 'cancelled')->sum('grand_total');
        $totalSales = Order::where('order_status', '!=', 'cancelled')->sum('grand_total');

        // Dynamic card values and labels based on filter
        if ($period === 'today') {
            $card1Title = 'আজকের বিক্রয় (Sales)';
            $card1Subtitle = 'আজকের দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের ডেলিভার্ড (Delivered)';
            $card2Subtitle = 'আজকের সম্পন্ন ডেলিভারি';
            $card2Value = $periodDeliveredSales;

            $card3Title = 'আজকের অর্ডার (Orders)';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } elseif ($period === '7_days') {
            $card1Title = '৭ দিনের বিক্রয় (7 Days)';
            $card1Subtitle = 'বিগত ৭ দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের বিক্রয় (Today)';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = '৭ দিনের অর্ডার (Orders)';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } elseif ($period === '30_days') {
            $card1Title = '৩০ দিনের বিক্রয় (30 Days)';
            $card1Subtitle = 'বিগত ৩০ দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের বিক্রয় (Today)';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = '৩০ দিনের অর্ডার (Orders)';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } else {
            // 'all'
            $card1Title = 'মোট বিক্রয় (Total Sales)';
            $card1Subtitle = 'সর্বমোট আয়';
            $card1Value = $totalSales;

            $card2Title = 'আজকের বিক্রয় (Today)';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = 'মোট অর্ডার (Orders)';
            $card3Orders = Order::count();
            $card3Pending = Order::where('order_status', 'pending')->count();
        }

        $metrics = [
            'period' => $period,
            'card1_title' => $card1Title,
            'card1_value' => $card1Value,
            'card1_subtitle' => $card1Subtitle,

            'card2_title' => $card2Title,
            'card2_value' => $card2Value,
            'card2_subtitle' => $card2Subtitle,

            'card3_title' => $card3Title,
            'card3_orders' => $card3Orders,
            'card3_pending' => $card3Pending,

            'total_sales' => $totalSales,
            'today_sales' => $todaySales,
            'period_sales' => $periodSales,
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'period_orders' => $periodOrders,
            'period_pending' => $periodPendingOrders,

            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock_quantity', '<=', 5)->count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        // Recent orders (always "As Now" real-time) with eager loading
        $recentOrders = Order::with('items.product')->latest()->take(8)->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->take(5)->get();

        return view('admin.dashboard', compact('period', 'metrics', 'recentOrders', 'lowStockProducts'));
    }
}
