<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $card1Title = 'আজকের বিক্রয়';
            $card1Subtitle = 'আজকের দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের ডেলিভার্ড';
            $card2Subtitle = 'আজকের সম্পন্ন ডেলিভারি';
            $card2Value = $periodDeliveredSales;

            $card3Title = 'আজকের অর্ডার';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } elseif ($period === '7_days') {
            $card1Title = '৭ দিনের বিক্রয়';
            $card1Subtitle = 'বিগত ৭ দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের বিক্রয়';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = '৭ দিনের অর্ডার';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } elseif ($period === '30_days') {
            $card1Title = '৩০ দিনের বিক্রয়';
            $card1Subtitle = 'বিগত ৩০ দিনের মোট আয়';
            $card1Value = $periodSales;

            $card2Title = 'আজকের বিক্রয়';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = '৩০ দিনের অর্ডার';
            $card3Orders = $periodOrders;
            $card3Pending = $periodPendingOrders;
        } else {
            // 'all'
            $card1Title = 'মোট বিক্রয়';
            $card1Subtitle = 'সর্বমোট আয়';
            $card1Value = $totalSales;

            $card2Title = 'আজকের বিক্রয়';
            $card2Subtitle = 'আজকের দিনের মোট আয়';
            $card2Value = $todaySales;

            $card3Title = 'মোট অর্ডার';
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

        // Trend Data for Chart.js
        $trendDaysCount = ($period === '7_days') ? 7 : (($period === 'today') ? 1 : 30);
        $daysTrend = [];

        if ($period === 'today') {
            for ($h = 0; $h <= 23; $h++) {
                $hKey = str_pad($h, 2, '0', STR_PAD_LEFT);
                $label = date('g A', strtotime("$hKey:00"));
                $daysTrend[$hKey] = [
                    'label' => $label,
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }
            $todayOrders = Order::whereDate('created_at', $today)
                ->where('order_status', '!=', 'cancelled')
                ->get(['created_at', 'grand_total']);
            foreach ($todayOrders as $order) {
                $hKey = $order->created_at->format('H');
                if (isset($daysTrend[$hKey])) {
                    $daysTrend[$hKey]['revenue'] += (float) $order->grand_total;
                    $daysTrend[$hKey]['orders'] += 1;
                }
            }
        } else {
            for ($i = $trendDaysCount - 1; $i >= 0; $i--) {
                $d = Carbon::now()->subDays($i)->format('Y-m-d');
                $daysTrend[$d] = [
                    'label' => Carbon::parse($d)->format('d M'),
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }
            $trendStartDate = Carbon::now()->subDays($trendDaysCount - 1)->startOfDay();
            $trendOrders = Order::where('created_at', '>=', $trendStartDate)
                ->where('order_status', '!=', 'cancelled')
                ->get(['created_at', 'grand_total']);
            foreach ($trendOrders as $order) {
                $dateKey = $order->created_at->format('Y-m-d');
                if (isset($daysTrend[$dateKey])) {
                    $daysTrend[$dateKey]['revenue'] += (float) $order->grand_total;
                    $daysTrend[$dateKey]['orders'] += 1;
                }
            }
        }

        // Payment Method breakdown
        $paymentMethodsBreakdown = (clone $orderQuery)
            ->where('order_status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Delivery Zone breakdown
        $zoneBreakdown = (clone $orderQuery)
            ->where('order_status', '!=', 'cancelled')
            ->select('delivery_zone', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'), DB::raw('sum(shipping_charge) as shipping_total'))
            ->groupBy('delivery_zone')
            ->get();

        // Recent orders (always "As Now" real-time) with eager loading
        $recentOrders = Order::with('items.product')->latest()->take(8)->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->take(5)->get();

        return view('admin.dashboard', compact(
            'period',
            'metrics',
            'recentOrders',
            'lowStockProducts',
            'daysTrend',
            'paymentMethodsBreakdown',
            'zoneBreakdown'
        ));
    }
}
