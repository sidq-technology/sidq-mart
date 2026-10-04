<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\FailedOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function buyNow(Request $request, Product $product, CartService $cart)
    {
        $quantity = (int) $request->input('quantity', 1);
        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;
        $cart->add($product->id, $quantity, $variantId);

        return redirect()->route('checkout');
    }

    public function index(CartService $cart)
    {
        // IP Blacklist Protection Check
        if (Setting::get('ip_blocking_enabled', '0') === '1') {
            $clientIp = request()->ip();
            if (!empty($clientIp)) {
                $rawBlockedIps = Setting::get('blocked_ips', '');
                $blockedIps = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawBlockedIps))));
                if (in_array($clientIp, $blockedIps, true)) {
                    $blockMsg = Setting::get('ip_block_message', 'আপনার আইপি অ্যাড্রেস থেকে সাময়িকভাবে অর্ডার বা এক্সেস স্থগিত রাখা হয়েছে। সহযোগিতার জন্য আমাদের সাপোর্ট নম্বরে যোগাযোগ করুন।');
                    return redirect()->route('home')->with('error', $blockMsg);
                }
            }
        }

        $items = $cart->getItems();

        if (empty($items)) {
            return redirect()->route('home')->with('info', 'আপনার কার্ট বর্তমানে খালি আছে। অনুগ্রহ করে কোনো পণ্য নির্বাচন করুন।');
        }

        $subtotal = $cart->getSubtotal();
        $insideDhakaFee = (float) Setting::get('delivery_inside_dhaka', 70);
        $outsideDhakaFee = (float) Setting::get('delivery_outside_dhaka', 130);
        $freeThreshold = (float) Setting::get('free_delivery_threshold', 3000);

        // Coupon discount if applied
        $appliedCoupon = Session::get('applied_coupon');
        $discountAmount = 0.00;

        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon['code'])->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                Session::forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        // Dynamic Payment Information Settings
        $paymentSettings = [
            'cod_enabled' => Setting::get('cod_enabled', '1') === '1',
            'cod_instructions' => Setting::get('cod_instructions', 'ক্যাশ অন ডেলিভারি (পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন)।'),
            
            'bkash_enabled' => Setting::get('bkash_enabled', '1') === '1',
            'bkash_number' => Setting::get('bkash_number', '01711223344'),
            'bkash_type' => Setting::get('bkash_type', 'Personal (Send Money)'),
            'bkash_instructions' => Setting::get('bkash_instructions', 'দয়া করে указанный বিকাশ নম্বরে Send Money করুন এবং TrxID নিচে লিখুন।'),
            
            'nagad_enabled' => Setting::get('nagad_enabled', '1') === '1',
            'nagad_number' => Setting::get('nagad_number', '01711223344'),
            'nagad_type' => Setting::get('nagad_type', 'Personal (Send Money)'),
            'nagad_instructions' => Setting::get('nagad_instructions', 'দয়া করে указанный নগদ নম্বরে Send Money করুন এবং TrxID নিচে লিখুন।'),
        ];

        // Safety fallback: Always ensure at least Cash On Delivery is available
        if (!$paymentSettings['cod_enabled'] && !$paymentSettings['bkash_enabled'] && !$paymentSettings['nagad_enabled']) {
            $paymentSettings['cod_enabled'] = true;
        }

        // Retrieve saved guest details if guest user
        $savedGuest = [];
        if (!auth()->check() && request()->hasCookie('sidq_saved_guest_profile')) {
            $savedGuest = json_decode(request()->cookie('sidq_saved_guest_profile'), true) ?: [];
        }

        return view('frontend.checkout', compact(
            'items',
            'subtotal',
            'insideDhakaFee',
            'outsideDhakaFee',
            'freeThreshold',
            'appliedCoupon',
            'discountAmount',
            'paymentSettings',
            'savedGuest'
        ));
    }

    public function store(Request $request, CartService $cart, OrderService $orderService)
    {
        $items = $cart->getItems();

        if (empty($items)) {
            return redirect()->route('home')->with('error', 'আপনার কার্ট খালি!');
        }

        // Normalize customer phone (remove dashes, spaces, convert Bengali numerals, handle +88)
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

        // Ensure default payment method if not supplied
        if (!$request->filled('payment_method')) {
            $request->merge(['payment_method' => 'cod']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:100',
            'customer_phone' => ['required', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'shipping_address' => 'required|string|min:3|max:500',
            'delivery_zone' => 'required|in:inside_dhaka,outside_dhaka',
            'payment_method' => 'required|in:cod,bkash,nagad',
            'transaction_id' => 'nullable|string|max:100',
            'customer_note' => 'nullable|string|max:500',
        ], [
            'customer_name.required' => 'আপনার নাম প্রদান করুন।',
            'customer_phone.required' => 'অনুগ্রহ করে সঠিক মোবাইল নম্বর দিন',
            'customer_phone.regex' => 'অনুগ্রহ করে সঠিক মোবাইল নম্বর দিন',
            'shipping_address.required' => 'ডেলিভারির সম্পূর্ণ ঠিকানা প্রদান করুন।',
            'shipping_address.min' => 'ডেলিভারি ঠিকানা অন্তত ৩ অক্ষরের হতে হবে।',
            'delivery_zone.required' => 'ডেলিভারি এরিয়া (ঢাকার ভিতরে বা বাহিরে) নির্বাচন করুন।',
            'payment_method.required' => 'মূল্য পরিশোধের মাধ্যম নির্বাচন করুন।',
        ]);

        // 1. IP Blacklist Protection Check
        if (Setting::get('ip_blocking_enabled', '0') === '1') {
            $clientIp = $request->ip();
            if (!empty($clientIp)) {
                $rawBlockedIps = Setting::get('blocked_ips', '');
                $blockedIps = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawBlockedIps))));
                if (in_array($clientIp, $blockedIps, true)) {
                    $blockMsg = Setting::get('ip_block_message', 'আপনার আইপি অ্যাড্রেস থেকে সাময়িকভাবে অর্ডার বা এক্সেস স্থগিত রাখা হয়েছে। সহযোগিতার জন্য আমাদের সাপোর্ট নম্বরে যোগাযোগ করুন।');
                    $this->logFailedAttempt($request, $cart, 'IP ব্লকলিস্টের কারণে অর্ডার ব্যর্থ (Blocked IP)');
                    return redirect()->back()->withInput()->with('error', $blockMsg);
                }
            }
        }

        // 2. Order Fraud Protection (Frequency / Rate Limiting)
        if (Setting::get('order_protection_enabled', '1') === '1') {
            $maxOrders = (int) Setting::get('order_protection_max_orders', 2);
            $timeWindowMinutes = (int) Setting::get('order_protection_time_window', 30);
            $trackBy = Setting::get('order_protection_track_by', 'phone_and_ip');
            $blockMessage = Setting::get('order_protection_block_message', 'আপনি সম্প্রতি একটি অর্ডার প্লেস করেছেন। ফেক বা অতিরিক্ত অর্ডার রোধে সাময়িকভাবে পুনরায় অর্ডার গ্রহণ স্থগিত রয়েছে। জরুরি প্রয়োজনে আমাদের হটলাইনে যোগাযোগ করুন।');

            $since = now()->subMinutes($timeWindowMinutes);
            $clientIp = $request->ip();
            $customerPhone = $validated['customer_phone'];

            $orderQuery = Order::where('created_at', '>=', $since);

            if ($trackBy === 'phone') {
                $orderQuery->where('customer_phone', $customerPhone);
            } elseif ($trackBy === 'ip') {
                $orderQuery->where('ip_address', $clientIp);
            } else {
                $orderQuery->where(function ($q) use ($customerPhone, $clientIp) {
                    $q->where('customer_phone', $customerPhone);
                    if (!empty($clientIp)) {
                        $q->orWhere('ip_address', $clientIp);
                    }
                });
            }

            if ($orderQuery->count() >= $maxOrders) {
                $this->logFailedAttempt($request, $cart, 'অতিরিক্ত অর্ডার লিমিট অতিক্রম (Rate Limited)');
                return redirect()->back()->withInput()->with('error', $blockMessage);
            }
        }

        // If authenticated customer, persist their phone/address to profile if empty
        if (auth()->check()) {
            $user = auth()->user();
            $updated = false;
            if (empty($user->phone) && !empty($validated['customer_phone'])) {
                $user->phone = $validated['customer_phone'];
                $updated = true;
            }
            if (empty($user->address) && !empty($validated['shipping_address'])) {
                $user->address = $validated['shipping_address'];
                $updated = true;
            }
            if ($updated) {
                $user->save();
            }
        }

        // Calculate coupon discount if applied
        $appliedCoupon = Session::get('applied_coupon');
        $discountAmount = 0.00;
        $subtotal = $cart->getSubtotal();

        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon['code'])->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        $validated['discount_amount'] = $discountAmount;

        $order = $orderService->createOrder($validated, $cart);

        // Remove any matching draft failed order as order has been placed successfully
        try {
            $sessionId = Session::getId();
            FailedOrder::where(function ($q) use ($sessionId, $validated) {
                    $q->where('session_id', $sessionId);
                    if (!empty($validated['customer_phone'])) {
                        $q->orWhere('customer_phone', $validated['customer_phone']);
                    }
                })
                ->delete();
        } catch (\Throwable $e) {
            \Log::error('Failed to clean up draft order: ' . $e->getMessage());
        }

        $guestProfileCookie = cookie()->forever('sidq_saved_guest_profile', json_encode([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'delivery_zone' => $validated['delivery_zone'],
        ]));

        return redirect()->route('order.success', $order->order_number)
            ->with('success', 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে! ধন্যবাদ।')
            ->withCookie($guestProfileCookie);
    }

    public function captureDraft(Request $request, CartService $cart)
    {
        $phone = trim((string) $request->input('customer_phone'));
        $name = trim((string) $request->input('customer_name'));
        $address = trim((string) $request->input('shipping_address'));

        // Save as long as phone has at least 3 digits, or name has at least 2 chars, or address has at least 3 chars
        if (strlen($phone) < 3 && strlen($name) < 2 && strlen($address) < 3) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $sessionId = Session::getId();
            $items = $cart->getItems();
            $subtotal = $cart->getSubtotal();
            $deliveryZone = $request->input('delivery_zone', 'inside_dhaka');
            $insideDhakaFee = (float) Setting::get('delivery_inside_dhaka', 70);
            $outsideDhakaFee = (float) Setting::get('delivery_outside_dhaka', 130);
            $shippingFee = ($deliveryZone === 'outside_dhaka') ? $outsideDhakaFee : $insideDhakaFee;

            $failedOrder = FailedOrder::where('is_recovered', false)
                ->where(function ($q) use ($sessionId, $phone) {
                    $q->where('session_id', $sessionId);
                    if (!empty($phone)) {
                        $q->orWhere('customer_phone', $phone);
                    }
                })
                ->latest()
                ->first();

            if (!$failedOrder) {
                $failedOrder = new FailedOrder();
                $failedOrder->session_id = $sessionId;
                $failedOrder->status = 'abandoned';
                $failedOrder->failure_reason = 'ফর্ম পূরণ করে অর্ডার বাটনে চাপ দেয়নি (Incomplete Checkout)';
            }

            if (!empty($name)) {
                $failedOrder->customer_name = $name;
            }
            if (!empty($phone)) {
                $failedOrder->customer_phone = $phone;
            }
            if ($request->filled('shipping_address')) {
                $failedOrder->shipping_address = $address;
            }
            if ($request->filled('delivery_zone')) {
                $failedOrder->delivery_zone = $deliveryZone;
            }
            if ($request->filled('payment_method')) {
                $failedOrder->payment_method = $request->input('payment_method');
            }
            if ($request->filled('customer_note')) {
                $failedOrder->customer_note = $request->input('customer_note');
            }

            $failedOrder->cart_items = $items;
            $failedOrder->subtotal = $subtotal;
            $failedOrder->shipping_charge = $shippingFee;
            $failedOrder->total_amount = $subtotal + $shippingFee;
            $failedOrder->ip_address = $request->ip();
            $failedOrder->user_agent = $request->userAgent();
            $failedOrder->save();

            return response()->json(['status' => 'success', 'id' => $failedOrder->id]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    protected function logFailedAttempt(Request $request, CartService $cart, string $reason)
    {
        try {
            $sessionId = Session::getId();
            $phone = (string) $request->input('customer_phone');
            $name = (string) $request->input('customer_name');
            $items = $cart->getItems();
            $subtotal = $cart->getSubtotal();
            $deliveryZone = $request->input('delivery_zone', 'inside_dhaka');
            $insideDhakaFee = (float) Setting::get('delivery_inside_dhaka', 70);
            $outsideDhakaFee = (float) Setting::get('delivery_outside_dhaka', 130);
            $shippingFee = ($deliveryZone === 'outside_dhaka') ? $outsideDhakaFee : $insideDhakaFee;

            $failedOrder = FailedOrder::where('is_recovered', false)
                ->where(function ($q) use ($sessionId, $phone) {
                    $q->where('session_id', $sessionId);
                    if (!empty($phone)) {
                        $q->orWhere('customer_phone', $phone);
                    }
                })
                ->latest()
                ->first();

            if (!$failedOrder) {
                $failedOrder = new FailedOrder();
                $failedOrder->session_id = $sessionId;
            }

            $failedOrder->customer_name = $name ?: $failedOrder->customer_name;
            $failedOrder->customer_phone = $phone ?: $failedOrder->customer_phone;
            $failedOrder->shipping_address = $request->input('shipping_address') ?: $failedOrder->shipping_address;
            $failedOrder->delivery_zone = $deliveryZone;
            $failedOrder->payment_method = $request->input('payment_method', 'cod');
            $failedOrder->customer_note = $request->input('customer_note');
            $failedOrder->cart_items = $items;
            $failedOrder->subtotal = $subtotal;
            $failedOrder->shipping_charge = $shippingFee;
            $failedOrder->total_amount = $subtotal + $shippingFee;
            $failedOrder->ip_address = $request->ip();
            $failedOrder->user_agent = $request->userAgent();
            $failedOrder->status = 'attempted';
            $failedOrder->failure_reason = $reason;
            $failedOrder->save();
        } catch (\Throwable $e) {
            \Log::error('Failed to log failed attempt: ' . $e->getMessage());
        }
    }

    public function success(string $orderNumber)
    {
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();

        $upsellEnabled = Setting::get('upsell_enabled', '0') === '1';
        $upsellBadgeText = Setting::get('upsell_badge_text', 'স্পেশাল অফার — ০৳ অতিরিক্ত ডেলিভারি চার্জ');
        $upsellHeading = Setting::get('upsell_heading', '🎉 আপনার জন্য স্পেশাল অফার! একই ডেলিভারিতে যুক্ত করুন');
        $upsellSubtitle = Setting::get('upsell_subtitle', 'অতিরিক্ত কোনো ডেলিভারি চার্জ ছাড়াই ১ ক্লিকে আপনার পার্সেলে যোগ করুন');
        $upsellProducts = collect();

        if ($upsellEnabled && $order->order_status === 'pending') {
            $orderedProductIds = $order->items->pluck('product_id')->filter()->unique()->toArray();
            $rawUpsellIds = json_decode(Setting::get('upsell_product_ids', '[]'), true) ?: [];

            if (!empty($rawUpsellIds)) {
                $upsellProducts = Product::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->whereIn('id', $rawUpsellIds)
                    ->whereNotIn('id', $orderedProductIds)
                    ->take(3)
                    ->get();
            }

            // Fallback: If no products selected or all selected products already in order
            if ($upsellProducts->isEmpty()) {
                $categoryIds = $order->items->map(function ($item) {
                    return $item->product?->category_id;
                })->filter()->unique()->toArray();

                if (!empty($categoryIds)) {
                    $upsellProducts = Product::where('is_active', true)
                        ->where('stock_quantity', '>', 0)
                        ->whereIn('category_id', $categoryIds)
                        ->whereNotIn('id', $orderedProductIds)
                        ->latest()
                        ->take(3)
                        ->get();
                }
            }

            // Fallback 2: If still empty, fetch latest active products
            if ($upsellProducts->isEmpty()) {
                $upsellProducts = Product::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->whereNotIn('id', $orderedProductIds)
                    ->latest()
                    ->take(3)
                    ->get();
            }
        }

        // Fetch up to 4 similar / recommended products for Thank You page
        $orderedIds = $order->items->pluck('product_id')->filter()->unique()->toArray();
        $upsellIds = $upsellProducts->pluck('id')->toArray();
        $excludeIds = array_values(array_unique(array_merge($orderedIds, $upsellIds)));

        $categoryIds = $order->items->map(function ($item) {
            return $item->product?->category_id;
        })->filter()->unique()->toArray();

        $similarProducts = collect();
        if (!empty($categoryIds)) {
            $similarProducts = Product::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('id', $excludeIds)
                ->latest()
                ->take(4)
                ->get();
        }

        if ($similarProducts->count() < 4) {
            $needed = 4 - $similarProducts->count();
            $moreExcludeIds = array_values(array_unique(array_merge($excludeIds, $similarProducts->pluck('id')->toArray())));
            $moreProducts = Product::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereNotIn('id', $moreExcludeIds)
                ->latest()
                ->take($needed)
                ->get();
            $similarProducts = $similarProducts->concat($moreProducts);
        }

        return view('frontend.order-success', compact(
            'order',
            'upsellEnabled',
            'upsellBadgeText',
            'upsellHeading',
            'upsellSubtitle',
            'upsellProducts',
            'similarProducts'
        ));
    }

    public function addUpsellItem(Request $request, string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->order_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'এই অর্ডারের স্ট্যাটাস পরিবর্তন হয়ে যাওয়ায় নতুন পণ্য যোগ করা সম্ভব নয়।'
            ], 422);
        }

        $productId = (int) $request->input('product_id');
        $product = Product::where('id', $productId)->where('is_active', true)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'পণ্যটি খুঁজে পাওয়া যায়নি।'
            ], 404);
        }

        if ($product->stock_quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'দুঃখিত, পণ্যটি বর্তমানে স্টক আউট।'
            ], 422);
        }

        $existingItem = $order->items()->where('product_id', $product->id)->first();
        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'এই পণ্যটি ইতিমধ্যে আপনার অর্ডারে রয়েছে।'
            ], 422);
        }

        $unitPrice = (float) $product->final_price;
        $quantity = 1;
        $totalPrice = $unitPrice * $quantity;

        $orderItem = $order->items()->create([
            'product_id' => $product->id,
            'variant_id' => null,
            'product_name' => $product->name,
            'product_image' => $product->primary_image_url,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
        ]);

        if ($product->stock_quantity > 0) {
            $product->decrement('stock_quantity', $quantity);
        }

        // Single parcel delivery: shipping charge remains unchanged
        $newSubtotal = (float) $order->subtotal + $totalPrice;
        $newGrandTotal = (float) $order->grand_total + $totalPrice;

        $order->update([
            'subtotal' => $newSubtotal,
            'grand_total' => $newGrandTotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'পণ্যটি সফলভাবে আপনার বর্তমান অর্ডারে যুক্ত করা হয়েছে!',
            'item' => [
                'id' => $orderItem->id,
                'name' => $orderItem->product_name,
                'image' => $orderItem->product_image,
                'unit_price' => number_format($orderItem->unit_price, 0),
                'quantity' => $orderItem->quantity,
                'total_price' => number_format($orderItem->total_price, 0),
            ],
            'subtotal' => number_format($newSubtotal, 0),
            'grand_total' => number_format($newGrandTotal, 0),
        ]);
    }

    public function applyCoupon(Request $request, CartService $cart)
    {
        $request->validate(['code' => 'required|string']);

        $code = strtoupper(trim($request->input('code')));
        $subtotal = $cart->getSubtotal();

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'কুপন কোডটি সঠিক নয় বা মেয়াদ শেষ হয়ে গেছে।',
            ], 422);
        }

        if (!$coupon->isValidForSubtotal($subtotal)) {
            return response()->json([
                'success' => false,
                'message' => "এই কুপনটি ব্যবহার করতে ন্যূনতম ৳{$coupon->min_spend} টাকার অর্ডার প্রয়োজন।",
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        Session::put('applied_coupon', [
            'code' => $coupon->code,
            'discount' => $discount,
        ]);

        return response()->json([
            'success' => true,
            'message' => "কুপন প্রয়োগ করা হয়েছে! ৳{$discount} ডিসকাউন্ট পেয়েছেন।",
            'discount' => $discount,
            'code' => $coupon->code,
        ]);
    }

    public function removeCoupon()
    {
        Session::forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'কুপন বাতিল করা হয়েছে।',
        ]);
    }
}
