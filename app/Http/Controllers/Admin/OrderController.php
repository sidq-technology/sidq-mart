<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $enabledCouriers = app(\App\Services\Courier\CourierManager::class)->getEnabledCouriers();

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search', 'enabledCouriers'));
    }

    /**
     * Show form for creating a new order manually by Admin
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->orderBy('id');
            }, 'category'])
            ->latest()
            ->get();

        $insideDhakaFee = (float) Setting::get('delivery_inside_dhaka', 70);
        $outsideDhakaFee = (float) Setting::get('delivery_outside_dhaka', 130);

        return view('admin.orders.create', compact('products', 'insideDhakaFee', 'outsideDhakaFee'));
    }

    /**
     * API search products for manual order creation
     */
    public function searchProducts(Request $request)
    {
        $query = trim($request->input('q', ''));
        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                });
            })
            ->with(['variants' => function ($q) {
                $q->orderBy('id');
            }])
            ->latest()
            ->take(30)
            ->get();

        return response()->json($products);
    }

    /**
     * Check for new orders since last known order ID (for real-time sound/toast alert)
     */
    public function checkNewOrders(Request $request)
    {
        $lastId = (int) $request->input('last_id', 0);

        if ($lastId <= 0) {
            $latestOrder = Order::latest('id')->first(['id']);
            return response()->json([
                'has_new' => false,
                'latest_id' => $latestOrder ? (int) $latestOrder->id : 0,
            ]);
        }

        // Search for newest order with ID > $lastId
        $newOrder = Order::where('id', '>', $lastId)
            ->latest('id')
            ->first();

        if ($newOrder) {
            return response()->json([
                'has_new' => true,
                'latest_id' => (int) $newOrder->id,
                'order' => [
                    'id' => $newOrder->id,
                    'order_number' => $newOrder->order_number,
                    'customer_name' => $newOrder->customer_name ?: 'সম্মানিত গ্রাহক',
                    'customer_phone' => $newOrder->customer_phone ?: '',
                    'grand_total' => (float) $newOrder->grand_total,
                    'order_status' => $newOrder->order_status,
                    'created_at' => $newOrder->created_at ? $newOrder->created_at->format('h:i A') : 'এইমাত্র',
                    'show_url' => route('admin.orders.show', $newOrder->id),
                ]
            ]);
        }

        return response()->json([
            'has_new' => false,
            'latest_id' => $lastId,
        ]);
    }

    /**
     * Store a newly created order manually by Admin
     */
    public function store(Request $request)
    {
        // 1. Phone number cleanup and normalization
        if ($request->filled('customer_phone')) {
            $rawPhone = (string) $request->input('customer_phone');
            $bengaliDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
            $englishDigits = ['0','1','2','3','4','5','6','7','8','9'];
            $cleanPhone = str_replace($bengaliDigits, $englishDigits, $rawPhone);
            $cleanPhone = preg_replace('/[^\d]/', '', $cleanPhone);
            if (str_starts_with($cleanPhone, '8801')) {
                $cleanPhone = substr($cleanPhone, 2);
            } elseif (str_starts_with($cleanPhone, '1') && strlen($cleanPhone) === 10) {
                $cleanPhone = '0' . $cleanPhone;
            }
            $request->merge(['customer_phone' => $cleanPhone]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:100',
            'customer_phone' => ['required', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'shipping_address' => 'required|string|min:3|max:500',
            'delivery_zone' => 'required|in:inside_dhaka,outside_dhaka',
            'shipping_charge' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cod,bkash,nagad',
            'payment_status' => 'required|in:pending,paid',
            'transaction_id' => 'nullable|string|max:100',
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'customer_note' => 'nullable|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'items.required' => 'অর্ডারে অন্তত একটি পণ্য যুক্ত করা আবশ্যক।',
            'items.min' => 'অর্ডারে অন্তত একটি পণ্য যুক্ত করা আবশ্যক।',
            'customer_phone.regex' => 'অনুগ্রহ করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)।',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $orderService = app(\App\Services\OrderService::class);
                $orderNumber = $orderService->generateOrderNumber();

                $subtotal = 0;
                $orderItemsData = [];

                foreach ($validated['items'] as $itemInput) {
                    $product = Product::findOrFail($itemInput['product_id']);
                    $variant = !empty($itemInput['variant_id']) ? ProductVariant::find($itemInput['variant_id']) : null;

                    $quantity = (int) $itemInput['quantity'];
                    $unitPrice = (float) $itemInput['unit_price'];
                    $rowTotal = $unitPrice * $quantity;
                    $subtotal += $rowTotal;

                    $orderItemsData[] = [
                        'product' => $product,
                        'variant' => $variant,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'product_name' => $product->name,
                        'color' => $variant ? $variant->color : null,
                        'size' => $variant ? $variant->size : null,
                        'product_image' => $product->primary_image_url,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total_price' => $rowTotal,
                    ];
                }

                $shippingCharge = (float) $validated['shipping_charge'];
                $discountAmount = (float) ($validated['discount_amount'] ?? 0);
                $grandTotal = max(0, ($subtotal + $shippingCharge - $discountAmount));

                $adminNoteText = 'ম্যানুয়ালি অ্যাডমিন দ্বারা তৈরি করা অর্ডার।';
                if (!empty($validated['admin_notes'])) {
                    $adminNoteText .= ' | ' . $validated['admin_notes'];
                }

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => auth()->id() ?? null,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'shipping_address' => $validated['shipping_address'],
                    'delivery_zone' => $validated['delivery_zone'],
                    'shipping_charge' => $shippingCharge,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['payment_status'],
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'order_status' => $validated['order_status'],
                    'customer_note' => $validated['customer_note'] ?? null,
                    'admin_notes' => $adminNoteText,
                    'ip_address' => request()->ip() ?: '127.0.0.1',
                ]);

                foreach ($orderItemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'variant_id' => $itemData['variant_id'],
                        'product_name' => $itemData['product_name'],
                        'color' => $itemData['color'],
                        'size' => $itemData['size'],
                        'product_image' => $itemData['product_image'],
                        'unit_price' => $itemData['unit_price'],
                        'quantity' => $itemData['quantity'],
                        'total_price' => $itemData['total_price'],
                    ]);

                    // Atomic stock decrement
                    if ($itemData['variant']) {
                        $itemData['variant']->decrement('stock_quantity', $itemData['quantity']);
                    }
                    if ($itemData['product']) {
                        $itemData['product']->decrement('stock_quantity', $itemData['quantity']);
                    }
                }

                return $order;
            });

            // Send SMS notification if configured
            try {
                app(\App\Services\SmsService::class)->sendOrderNotification($order, 'order_placed');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Admin Order Create SMS Error: ' . $e->getMessage());
            }

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "নতুন অর্ডারটি সফলভাবে তৈরি হয়েছে! (অর্ডার নং: {$order->order_number})");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'অর্ডার তৈরি করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);
        $enabledCouriers = app(\App\Services\Courier\CourierManager::class)->getEnabledCouriers();

        return view('admin.orders.show', compact('order', 'enabledCouriers'));
    }

    /**
     * Show form for editing an existing order
     */
    public function edit(Order $order)
    {
        $order->load(['items.product.variants', 'user']);

        $products = Product::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->orderBy('id');
            }, 'category'])
            ->latest()
            ->get();

        $insideDhakaFee = (float) Setting::get('delivery_inside_dhaka', 70);
        $outsideDhakaFee = (float) Setting::get('delivery_outside_dhaka', 130);

        $cartItems = $order->items->map(function ($it) {
            return [
                'uid' => (string) $it->id,
                'productId' => (string) $it->product_id,
                'variantId' => $it->variant_id ? (string) $it->variant_id : null,
                'productName' => $it->product_name,
                'productImage' => $it->product_image ?: ($it->product ? $it->product->primary_image_url : '/images/placeholder-product.png'),
                'variantColor' => $it->color ?: '',
                'variantSize' => $it->size ?: '',
                'unitPrice' => (float) $it->unit_price,
                'quantity' => (int) $it->quantity,
            ];
        })->values();

        return view('admin.orders.edit', compact('order', 'products', 'insideDhakaFee', 'outsideDhakaFee', 'cartItems'));
    }

    /**
     * Update an existing order
     */
    public function update(Request $request, Order $order)
    {
        // 1. Phone number cleanup and normalization
        if ($request->filled('customer_phone')) {
            $rawPhone = (string) $request->input('customer_phone');
            $bengaliDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
            $englishDigits = ['0','1','2','3','4','5','6','7','8','9'];
            $cleanPhone = str_replace($bengaliDigits, $englishDigits, $rawPhone);
            $cleanPhone = preg_replace('/[^\d]/', '', $cleanPhone);
            if (str_starts_with($cleanPhone, '8801')) {
                $cleanPhone = substr($cleanPhone, 2);
            } elseif (str_starts_with($cleanPhone, '1') && strlen($cleanPhone) === 10) {
                $cleanPhone = '0' . $cleanPhone;
            }
            $request->merge(['customer_phone' => $cleanPhone]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:100',
            'customer_phone' => ['required', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'shipping_address' => 'required|string|min:3|max:500',
            'delivery_zone' => 'required|in:inside_dhaka,outside_dhaka',
            'shipping_charge' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cod,bkash,nagad',
            'payment_status' => 'required|in:pending,paid',
            'transaction_id' => 'nullable|string|max:100',
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'customer_note' => 'nullable|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'items.required' => 'অর্ডারে অন্তত একটি পণ্য থাকা আবশ্যক।',
            'items.min' => 'অর্ডারে অন্তত একটি পণ্য থাকা আবশ্যক।',
            'customer_phone.regex' => 'অনুগ্রহ করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)।',
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                // 1. Restore previous stock for this order's items
                $oldItems = $order->items;
                foreach ($oldItems as $oldItem) {
                    if ($oldItem->variant_id) {
                        $variant = ProductVariant::find($oldItem->variant_id);
                        if ($variant) {
                            $variant->increment('stock_quantity', $oldItem->quantity);
                        }
                    }
                    if ($oldItem->product_id) {
                        $product = Product::find($oldItem->product_id);
                        if ($product) {
                            $product->increment('stock_quantity', $oldItem->quantity);
                        }
                    }
                }

                // 2. Delete old items
                $order->items()->delete();

                // 3. Prepare new items and calculate subtotal
                $subtotal = 0;
                $newItemsData = [];

                foreach ($validated['items'] as $itemInput) {
                    $product = Product::findOrFail($itemInput['product_id']);
                    $variant = !empty($itemInput['variant_id']) ? ProductVariant::find($itemInput['variant_id']) : null;

                    $quantity = (int) $itemInput['quantity'];
                    $unitPrice = (float) $itemInput['unit_price'];
                    $rowTotal = $unitPrice * $quantity;
                    $subtotal += $rowTotal;

                    $newItemsData[] = [
                        'product' => $product,
                        'variant' => $variant,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'product_name' => $product->name,
                        'color' => $variant ? $variant->color : null,
                        'size' => $variant ? $variant->size : null,
                        'product_image' => $product->primary_image_url,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total_price' => $rowTotal,
                    ];
                }

                $shippingCharge = (float) $validated['shipping_charge'];
                $discountAmount = (float) ($validated['discount_amount'] ?? 0);
                $grandTotal = max(0, ($subtotal + $shippingCharge - $discountAmount));

                // 4. Update Order record
                $order->update([
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'shipping_address' => $validated['shipping_address'],
                    'delivery_zone' => $validated['delivery_zone'],
                    'shipping_charge' => $shippingCharge,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['payment_status'],
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'order_status' => $validated['order_status'],
                    'customer_note' => $validated['customer_note'] ?? null,
                    'admin_notes' => $validated['admin_notes'] ?? null,
                ]);

                // 5. Create new items and decrement stock
                foreach ($newItemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'variant_id' => $itemData['variant_id'],
                        'product_name' => $itemData['product_name'],
                        'color' => $itemData['color'],
                        'size' => $itemData['size'],
                        'product_image' => $itemData['product_image'],
                        'unit_price' => $itemData['unit_price'],
                        'quantity' => $itemData['quantity'],
                        'total_price' => $itemData['total_price'],
                    ]);

                    if ($itemData['variant']) {
                        $itemData['variant']->decrement('stock_quantity', $itemData['quantity']);
                    }
                    if ($itemData['product']) {
                        $itemData['product']->decrement('stock_quantity', $itemData['quantity']);
                    }
                }
            });

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "অর্ডার (#{$order->order_number}) সফলভাবে আপডেট করা হয়েছে!");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'অর্ডার আপডেট করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'অর্ডারের স্ট্যাটাস ও নোট সফলভাবে সংরক্ষিত হয়েছে।',
                'order_status' => $newStatus,
                'status_label' => ucfirst($newStatus),
                'status_badge_class' => $order->status_badge_class,
                'admin_notes' => $order->admin_notes,
            ]);
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

    public function sendToSheet(Order $order, \App\Services\GoogleSheetService $sheetService)
    {
        $result = $sheetService->syncOrder($order);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'action' => $result['action'] ?? 'synced',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    public function sendToCourier(Request $request, Order $order, \App\Services\Courier\CourierManager $courierManager)
    {
        $courierName = $request->input('courier');
        if (empty($courierName)) {
            return response()->json([
                'success' => false,
                'message' => 'অনুগ্রহ করে একটি কুরিয়ার সার্ভিস নির্বাচন করুন।'
            ], 422);
        }

        try {
            $result = $courierManager->sendOrder($order, $courierName);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'courier_name' => $result['courier'],
                    'tracking_code' => $result['tracking_code'],
                    'consignment_id' => $result['consignment_id'],
                    'tracking_url' => $result['tracking_url'] ?? null,
                    'order_status' => $result['order_status'],
                    'status_badge_class' => $result['status_badge_class'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'কুরিয়ার বুকিং ত্রুটি: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'অর্ডারটি মুছে ফেলা হয়েছে।');
    }

    /**
     * IP Geolocation & Network Lookup via AJAX
     */
    public function lookupIp(Request $request, \App\Services\IpLocationService $ipService)
    {
        $ip = trim($request->input('ip', ''));

        // If demo testing is requested (helpful for localhost dev)
        if ($request->boolean('demo')) {
            $ip = '144.48.111.188'; // Real Bangladeshi IP
        }

        if (empty($ip)) {
            return response()->json([
                'success' => false,
                'message' => 'আইপি অ্যাড্রেস পাওয়া যায়নি।'
            ], 422);
        }

        $result = $ipService->lookup($ip);
        return response()->json($result);
    }
}
