<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Display Finance & Revenue Analytics Console
     */
    public function index(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($range) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } else {
                    $startDate = Carbon::now()->startOfMonth();
                }
                break;
            case 'all':
            default:
                $startDate = null;
                break;
        }

        // Base query for transactions
        $query = Order::query()->with('user');

        if ($startDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Summary Calculations for current filter
        $totalOrdersCount = (clone $query)->count();
        $grossRevenue = (clone $query)->where('order_status', '!=', 'cancelled')->sum('grand_total');
        $netDeliveredRevenue = (clone $query)->where('order_status', 'delivered')->sum('grand_total');
        $paidRevenue = (clone $query)->where('payment_status', 'paid')->sum('grand_total');
        $pendingReceivables = (clone $query)->where('payment_status', 'pending')->where('order_status', '!=', 'cancelled')->sum('grand_total');
        $totalShippingCollected = (clone $query)->where('order_status', '!=', 'cancelled')->sum('shipping_charge');
        $totalDiscountsGiven = (clone $query)->where('order_status', '!=', 'cancelled')->sum('discount_amount');

        $nonCancelledCount = (clone $query)->where('order_status', '!=', 'cancelled')->count();
        $averageOrderValue = $nonCancelledCount > 0 ? ($grossRevenue / $nonCancelledCount) : 0;

        // Payment Method breakdown
        $paymentMethodsBreakdown = (clone $query)
            ->where('order_status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Delivery Zone breakdown
        $zoneBreakdown = (clone $query)
            ->where('order_status', '!=', 'cancelled')
            ->select('delivery_zone', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'), DB::raw('sum(shipping_charge) as shipping_total'))
            ->groupBy('delivery_zone')
            ->get();

        // 30 Days trend data for Chart.js (Database agnostic)
        $daysTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $daysTrend[$d] = [
                'label' => Carbon::parse($d)->format('d M'),
                'revenue' => 0,
                'orders' => 0,
            ];
        }

        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        $recentOrders = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->where('order_status', '!=', 'cancelled')
            ->get(['created_at', 'grand_total']);

        foreach ($recentOrders as $order) {
            $dateKey = $order->created_at->format('Y-m-d');
            if (isset($daysTrend[$dateKey])) {
                $daysTrend[$dateKey]['revenue'] += (float) $order->grand_total;
                $daysTrend[$dateKey]['orders'] += 1;
            }
        }

        // Recent paginated transactions for ledger
        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.finance.index', compact(
            'transactions',
            'range',
            'startDate',
            'endDate',
            'totalOrdersCount',
            'grossRevenue',
            'netDeliveredRevenue',
            'paidRevenue',
            'pendingReceivables',
            'totalShippingCollected',
            'totalDiscountsGiven',
            'averageOrderValue',
            'paymentMethodsBreakdown',
            'zoneBreakdown',
            'daysTrend'
        ));
    }
}
