<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
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
        $cart->add($product->id, $quantity);

        return redirect()->route('checkout');
    }

    public function index(CartService $cart)
    {
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

        return view('frontend.checkout', compact(
            'items',
            'subtotal',
            'insideDhakaFee',
            'outsideDhakaFee',
            'freeThreshold',
            'appliedCoupon',
            'discountAmount',
            'paymentSettings'
        ));
    }

    public function store(Request $request, CartService $cart, OrderService $orderService)
    {
        $items = $cart->getItems();

        if (empty($items)) {
            return redirect()->route('home')->with('error', 'আপনার কার্ট খালি!');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:100',
            'customer_phone' => ['required', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'shipping_address' => 'required|string|min:5|max:500',
            'delivery_zone' => 'required|in:inside_dhaka,outside_dhaka',
            'payment_method' => 'required|in:cod,bkash,nagad',
            'transaction_id' => 'nullable|string|max:100',
            'customer_note' => 'nullable|string|max:500',
        ], [
            'customer_name.required' => 'আপনার নাম প্রদান করুন।',
            'customer_phone.required' => '১১ ডিজিটের সঠিক মোবাইল নাম্বার প্রদান করুন।',
            'customer_phone.regex' => 'অনুগ্রহ করে সঠিক বাংলাদেশি মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)।',
            'shipping_address.required' => 'ডেলিভারির সম্পূর্ণ ঠিকানা প্রদান করুন।',
            'delivery_zone.required' => 'ডেলিভারি এরিয়া (ঢাকার ভিতরে বা বাহিরে) নির্বাচন করুন।',
        ]);

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

        return redirect()->route('order.success', $order->order_number)
            ->with('success', 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে! ধন্যবাদ।');
    }

    public function success(string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('frontend.order-success', compact('order'));
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
