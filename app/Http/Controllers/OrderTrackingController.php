<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display order tracking page and handle search query.
     */
    public function index(Request $request)
    {
        $searchQuery = trim($request->input('search', ''));
        $orders = collect();
        $hasSearched = false;

        if (!empty($searchQuery)) {
            $hasSearched = true;

            // Normalize search query
            $rawQuery = $searchQuery;
            $digitsOnly = preg_replace('/[^0-9]/', '', $rawQuery);

            $ordersQuery = Order::with(['items'])->orderBy('created_at', 'desc');

            $ordersQuery->where(function ($q) use ($rawQuery, $digitsOnly) {
                // Search by Order Reference Number
                $q->where('order_number', 'LIKE', '%' . $rawQuery . '%');

                // Search by Mobile Phone Number
                if (strlen($digitsOnly) >= 6) {
                    // Extract last 10 digits for accurate BD phone match
                    $phonePattern = strlen($digitsOnly) > 10 ? substr($digitsOnly, -10) : $digitsOnly;
                    $q->orWhere('customer_phone', 'LIKE', '%' . $phonePattern . '%')
                      ->orWhere('customer_phone', 'LIKE', '%' . $digitsOnly . '%');
                } else {
                    $q->orWhere('customer_phone', 'LIKE', '%' . $rawQuery . '%');
                }
            });

            // Limit to most recent 10 orders for that phone/query
            $orders = $ordersQuery->take(10)->get();
        }

        return view('frontend.order-tracking', [
            'orders' => $orders,
            'searchQuery' => $searchQuery,
            'hasSearched' => $hasSearched,
        ]);
    }
}
