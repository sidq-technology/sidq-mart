@extends('layouts.app')

@section('title', 'চেকআউট - অর্ডার সম্পন্ন করুন | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4">
    <div class="row g-4">
        <!-- 1. Customer Information & Delivery Form -->
        <div class="col-12 col-lg-7">
            <div class="checkout-box">
                <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                    <i class="fas fa-shipping-fast text-danger me-2"></i> বিলিং ও ডেলিভারি তথ্য
                </h4>

                @guest
                <div class="alert border d-flex justify-content-between align-items-center py-2 px-3 mb-3 rounded-3 small" style="background: #f8fffa; border-color: #e2f0e8 !important;">
                    <span><i class="fas fa-user-circle me-1 text-danger"></i> পূর্বেই অ্যাকাউন্ট আছে? দ্রুত অর্ডারের জন্য লগইন করুন।</span>
                    <button type="button" class="btn btn-sm btn-outline-danger fw-semibold px-3 py-1" data-bs-toggle="modal" data-bs-target="#customerAuthModal">
                        লগইন
                    </button>
                </div>
                @endguest

                @if(isset($errors) && $errors->any())
                <div class="alert alert-danger mb-4 shadow-xs border-0 rounded-3">
                    <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>অর্ডার সম্পন্ন করতে নিচের তথ্যগুলো সঠিক করুন:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf

                    <!-- Customer Name -->
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">আপনার নাম (Full Name) <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="সম্পূর্ণ নাম লিখুন" required>
                        @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Customer Phone -->
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">মোবাইল নাম্বার (Mobile Number) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">+88</span>
                            <input type="tel" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" placeholder="01XXXXXXXXX (১১ ডিজিট)" required>
                        </div>
                        @error('customer_phone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">অর্ডার কনফার্ম করার জন্য এই নম্বরে যোগাযোগ করা হবে।</div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">সম্পূর্ণ ঠিকানা (Full Delivery Address) <span class="text-danger">*</span></label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" placeholder="বাড়ি নম্বর, রোড নম্বর, এলাকা, থানা, জেলা বিস্তারিত লিখুন..." required>{{ old('shipping_address', auth()->user()->address ?? '') }}</textarea>
                        @error('shipping_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Zone Selection -->
                    <div class="mb-4">
                        <label class="form-label d-block">ডেলিভারি এরিয়া নির্বাচন করুন <span class="text-danger">*</span></label>
                        
                        <div class="row g-2">
                            <!-- Inside Dhaka -->
                            <div class="col-12 col-sm-6">
                                <label class="delivery-zone-card d-flex align-items-center justify-content-between active" id="labelInsideDhaka" for="zoneInsideDhaka">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="delivery_zone" id="zoneInsideDhaka" value="inside_dhaka" class="form-check-input me-2 mt-0" checked onchange="updateDeliveryCharge('inside_dhaka')">
                                        <div>
                                            <div class="fw-bold">ঢাকার ভিতরে</div>
                                            <small class="text-muted">হোম ডেলিভারি (১-২ দিন)</small>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-danger tabular-nums">৳{{ $insideDhakaFee }}</span>
                                </label>
                            </div>

                            <!-- Outside Dhaka -->
                            <div class="col-12 col-sm-6">
                                <label class="delivery-zone-card d-flex align-items-center justify-content-between" id="labelOutsideDhaka" for="zoneOutsideDhaka">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="delivery_zone" id="zoneOutsideDhaka" value="outside_dhaka" class="form-check-input me-2 mt-0" onchange="updateDeliveryCharge('outside_dhaka')">
                                        <div>
                                            <div class="fw-bold">ঢাকার বাহিরে</div>
                                            <small class="text-muted">হোম ডেলিভারি (২-৩ দিন)</small>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-danger tabular-nums">৳{{ $outsideDhakaFee }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Section (Configured dynamically in Admin) -->
                    <div class="mb-4">
                        <label class="form-label d-block fw-bold border-bottom pb-2">
                            <i class="fas fa-wallet text-danger me-1"></i> মূল্য পরিশোধের মাধ্যম (Payment Method)
                        </label>

                        @php
                            $defaultPay = old('payment_method');
                            if (!$defaultPay) {
                                if ($paymentSettings['cod_enabled']) $defaultPay = 'cod';
                                elseif ($paymentSettings['bkash_enabled']) $defaultPay = 'bkash';
                                elseif ($paymentSettings['nagad_enabled']) $defaultPay = 'nagad';
                                else $defaultPay = 'cod';
                            }
                        @endphp

                        <!-- Option 1: Cash on Delivery -->
                        @if($paymentSettings['cod_enabled'])
                        <div class="form-check p-3 rounded-2 border mb-2 bg-light">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payCod" value="cod" {{ $defaultPay === 'cod' ? 'checked' : '' }} onchange="togglePaymentInstructions('cod')">
                            <label class="form-check-label fw-bold" for="payCod">
                                <i class="fas fa-money-bill-wave text-success me-1"></i> ক্যাশ অন ডেলিভারি (Cash on Delivery)
                            </label>
                            <div class="mt-2 text-muted small ps-4" id="codInstructions">
                                {{ $paymentSettings['cod_instructions'] }}
                            </div>
                        </div>
                        @endif

                        <!-- Option 2: bKash -->
                        @if($paymentSettings['bkash_enabled'])
                        <div class="form-check p-3 rounded-2 border mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payBkash" value="bkash" {{ $defaultPay === 'bkash' ? 'checked' : '' }} onchange="togglePaymentInstructions('bkash')">
                            <label class="form-check-label fw-bold text-danger" for="payBkash">
                                <i class="fas fa-mobile-alt me-1"></i> বিকাশ (bKash)
                            </label>
                            <div class="mt-2 ps-4 {{ $defaultPay === 'bkash' ? '' : 'd-none' }}" id="bkashDetails">
                                <div class="alert alert-danger py-2 mb-2 small">
                                    <strong>বিকাশ নম্বর:</strong> {{ $paymentSettings['bkash_number'] }} ({{ $paymentSettings['bkash_type'] }})<br>
                                    {{ $paymentSettings['bkash_instructions'] }}
                                </div>
                                <div class="mt-2">
                                    <label for="bkash_trx" class="form-label small">বিকাশ ট্রানজেকশন আইডি (bKash TrxID):</label>
                                    <input type="text" name="transaction_id" id="bkash_trx" class="form-control form-control-sm" value="{{ old('transaction_id') }}" placeholder="যেমন: 9J4K2L8M...">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Option 3: Nagad -->
                        @if($paymentSettings['nagad_enabled'])
                        <div class="form-check p-3 rounded-2 border mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payNagad" value="nagad" {{ $defaultPay === 'nagad' ? 'checked' : '' }} onchange="togglePaymentInstructions('nagad')">
                            <label class="form-check-label fw-bold text-warning" for="payNagad">
                                <i class="fas fa-mobile-alt me-1"></i> নগদ (Nagad)
                            </label>
                            <div class="mt-2 ps-4 {{ $defaultPay === 'nagad' ? '' : 'd-none' }}" id="nagadDetails">
                                <div class="alert alert-warning py-2 mb-2 small">
                                    <strong>নগদ নম্বর:</strong> {{ $paymentSettings['nagad_number'] }} ({{ $paymentSettings['nagad_type'] }})<br>
                                    {{ $paymentSettings['nagad_instructions'] }}
                                </div>
                                <div class="mt-2">
                                    <label for="nagad_trx" class="form-label small">নগদ ট্রানজেকশন আইডি (Nagad TrxID):</label>
                                    <input type="text" name="transaction_id" id="nagad_trx" class="form-control form-control-sm" value="{{ old('transaction_id') }}" placeholder="যেমন: 7H8N2K5L...">
                                </div>
                            </div>
                        </div>
                        @endif

                        @error('payment_method')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Customer Order Notes -->
                    <div class="mb-4">
                        <label for="customer_note" class="form-label">অর্ডার সংক্রান্ত কোনো নির্দেশনা থাকলে লিখুন (Optional)</label>
                        <input type="text" name="customer_note" id="customer_note" class="form-control" placeholder="যেমন: বিকেলে ডেলিভারি করবেন বা কল দিবেন...">
                    </div>

                    <!-- Submit Order Button -->
                    <button type="submit" class="btn btn-primary-sidq w-100 py-3 fs-5 shadow-sm">
                        <i class="fas fa-lock me-2"></i> অর্ডার কনফার্ম করুন (Confirm Order)
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Order Summary & Coupon Area -->
        <div class="col-12 col-lg-5">
            <div class="checkout-box bg-light">
                <h4 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i class="fas fa-clipboard-list text-danger me-2"></i> আপনার অর্ডার
                </h4>

                <!-- Item list -->
                <div class="mb-3" style="max-height: 280px; overflow-y: auto;">
                    @foreach($items as $item)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 text-truncate" style="max-width: 170px; font-size: 13px;">{{ $item['name'] }}</h6>
                                <small class="text-muted">{{ $item['quantity'] }} টি &times; ৳{{ number_format($item['unit_price'], 0) }}</small>
                            </div>
                        </div>
                        <span class="fw-bold tabular-nums">৳{{ number_format($item['total_price'], 0) }}</span>
                    </div>
                    @endforeach
                </div>

                <!-- Coupon Input Form -->
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="couponCodeInput" class="form-control" placeholder="কুপন কোড (Coupon Code)" value="{{ $appliedCoupon ? $appliedCoupon['code'] : '' }}" @if($appliedCoupon) disabled @endif>
                        @if($appliedCoupon)
                        <button class="btn btn-outline-danger" type="button" onclick="removeCoupon()">বাতিল</button>
                        @else
                        <button class="btn btn-dark" type="button" onclick="applyCoupon()">প্রয়োগ করুন</button>
                        @endif
                    </div>
                    <div id="couponMessage" class="small mt-1"></div>
                </div>

                <!-- Price Calculations -->
                <div class="pt-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">পণ্যের মূল্য (Subtotal):</span>
                        <span class="fw-bold tabular-nums">৳<span id="summarySubtotal">{{ number_format($subtotal, 0) }}</span></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ডেলিভারি চার্জ (Delivery):</span>
                        <span class="fw-bold text-primary tabular-nums">৳<span id="summaryShipping">{{ number_format($insideDhakaFee, 0) }}</span></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-success {{ $discountAmount > 0 ? '' : 'd-none' }}" id="discountRow">
                        <span>কুপন ডিসকাউন্ট:</span>
                        <span class="fw-bold tabular-nums">-৳<span id="summaryDiscount">{{ number_format($discountAmount, 0) }}</span></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-5 fw-bold text-dark">সর্বমোট প্রদেয় বিল:</span>
                        <span class="fs-3 fw-bold text-danger tabular-nums">৳<span id="summaryGrandTotal">{{ number_format($subtotal + $insideDhakaFee - $discountAmount, 0) }}</span></span>
                    </div>
                </div>

                <!-- Free delivery indicator -->
                @if($freeThreshold > 0)
                <div class="alert alert-info py-2 mb-0 small">
                    <i class="fas fa-gift me-1"></i> <strong>৳{{ number_format($freeThreshold, 0) }}</strong> টাকার কেনাকাটায় সারা বাংলাদেশে ফ্রি ডেলিভারি!
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const subtotal = {{ $subtotal }};
    const insideDhakaFee = {{ $insideDhakaFee }};
    const outsideDhakaFee = {{ $outsideDhakaFee }};
    const freeThreshold = {{ $freeThreshold }};
    let currentDiscount = {{ $discountAmount }};
    let currentShipping = insideDhakaFee;

    function updateDeliveryCharge(zone) {
        if (zone === 'outside_dhaka') {
            currentShipping = outsideDhakaFee;
            document.getElementById('labelOutsideDhaka').classList.add('active');
            document.getElementById('labelInsideDhaka').classList.remove('active');
        } else {
            currentShipping = insideDhakaFee;
            document.getElementById('labelInsideDhaka').classList.add('active');
            document.getElementById('labelOutsideDhaka').classList.remove('active');
        }

        if (freeThreshold > 0 && subtotal >= freeThreshold) {
            currentShipping = 0;
        }

        document.getElementById('summaryShipping').textContent = currentShipping;
        recalculateGrandTotal();
    }

    function recalculateGrandTotal() {
        const grandTotal = Math.max(0, subtotal + currentShipping - currentDiscount);
        document.getElementById('summaryGrandTotal').textContent = grandTotal;
    }

    function togglePaymentInstructions(method) {
        const bkashDetails = document.getElementById('bkashDetails');
        const nagadDetails = document.getElementById('nagadDetails');

        if (bkashDetails) bkashDetails.classList.toggle('d-none', method !== 'bkash');
        if (nagadDetails) nagadDetails.classList.toggle('d-none', method !== 'nagad');
    }

    function applyCoupon() {
        const code = document.getElementById('couponCodeInput').value.trim();
        const msgDiv = document.getElementById('couponMessage');
        if (!code) return;

        fetch("{{ route('coupon.apply') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
                "Accept": "application/json"
            },
            body: JSON.stringify({ code: code })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                msgDiv.className = 'small mt-1 text-success';
                msgDiv.textContent = body.message;
                currentDiscount = body.discount;
                document.getElementById('summaryDiscount').textContent = currentDiscount;
                document.getElementById('discountRow').classList.remove('d-none');
                recalculateGrandTotal();
                setTimeout(() => location.reload(), 1000);
            } else {
                msgDiv.className = 'small mt-1 text-danger';
                msgDiv.textContent = body.message || 'কুপন প্রয়োগ ব্যর্থ হয়েছে।';
            }
        });
    }

    function removeCoupon() {
        fetch("{{ route('coupon.remove') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN
            }
        })
        .then(() => location.reload());
    }

    // ==========================================
    // Abandoned / Failed Checkout Auto-Capture
    // ==========================================
    let captureTimer = null;
    function captureCheckoutDraft() {
        clearTimeout(captureTimer);
        captureTimer = setTimeout(() => {
            const name = document.getElementById('customer_name')?.value?.trim() || '';
            const phone = document.getElementById('customer_phone')?.value?.trim() || '';
            const address = document.getElementById('shipping_address')?.value?.trim() || '';
            const zoneInput = document.querySelector('input[name="delivery_zone"]:checked');
            const deliveryZone = zoneInput ? zoneInput.value : 'inside_dhaka';
            const payInput = document.querySelector('input[name="payment_method"]:checked');
            const paymentMethod = payInput ? payInput.value : 'cod';
            const note = document.querySelector('textarea[name="customer_note"]')?.value?.trim() || '';

            if (phone.length >= 3 || name.length >= 2 || address.length >= 3) {
                fetch("{{ route('checkout.capture-draft') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        customer_name: name,
                        customer_phone: phone,
                        shipping_address: address,
                        delivery_zone: deliveryZone,
                        payment_method: paymentMethod,
                        customer_note: note
                    })
                }).catch(() => {});
            }
        }, 600);
    }

    ['customer_name', 'customer_phone', 'shipping_address'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', captureCheckoutDraft);
            el.addEventListener('blur', captureCheckoutDraft);
        }
    });

    document.querySelectorAll('input[name="delivery_zone"], input[name="payment_method"]').forEach(el => {
        el.addEventListener('change', captureCheckoutDraft);
    });

    const noteEl = document.querySelector('textarea[name="customer_note"]');
    if (noteEl) {
        noteEl.addEventListener('input', captureCheckoutDraft);
    }
</script>
@endpush
@endsection
