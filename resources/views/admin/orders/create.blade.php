@extends('layouts.admin')

@section('title', 'নতুন অর্ডার তৈরি করুন - Create Order')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> অর্ডার তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">নতুন অর্ডার তৈরি করুন (Manual Order Creation)</h3>
        <p class="text-muted small mb-0">ফোন বা সামাজিক যোগাযোগ মাধ্যমে আসা গ্রাহকের সরাসরি অর্ডার ম্যানুয়ালি এন্ট্রি করুন।</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
    <i class="fas fa-exclamation-circle fs-5"></i>
    <div class="fw-semibold">{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4" role="alert">
    <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> ফর্ম পূরণে কিছু ত্রুটি রয়েছে:</div>
    <ul class="mb-0 ps-3 small">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.orders.store') }}" method="POST" id="manualOrderForm">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Customer & Shipping Details -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-user-check text-primary"></i>
                        <span>গ্রাহক ও ডেলিভারির তথ্য</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Customer Name -->
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-bold text-dark small mb-1">
                            গ্রাহকের নাম (Customer Name) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="যেমন: মো: আরিফুল ইসলাম" required>
                    </div>

                    <!-- Customer Phone -->
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label fw-bold text-dark small mb-1">
                            মোবাইল নম্বর (Customer Phone) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-phone-alt"></i></span>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="form-text small text-muted">১১ ডিজিটের সঠিক নম্বর দিন।</div>
                    </div>

                    <!-- Delivery Zone -->
                    <div class="mb-3">
                        <label for="delivery_zone" class="form-label fw-bold text-dark small mb-1">
                            ডেলিভারি এরিয়া (Delivery Zone) <span class="text-danger">*</span>
                        </label>
                        <select name="delivery_zone" id="deliveryZoneSelect" class="form-select @error('delivery_zone') is-invalid @enderror" required>
                            <option value="inside_dhaka" data-fee="{{ $insideDhakaFee }}" {{ old('delivery_zone') === 'inside_dhaka' ? 'selected' : '' }}>
                                ঢাকার ভেতরে (Inside Dhaka - ৳{{ number_format($insideDhakaFee, 0) }})
                            </option>
                            <option value="outside_dhaka" data-fee="{{ $outsideDhakaFee }}" {{ old('delivery_zone') === 'outside_dhaka' ? 'selected' : '' }}>
                                ঢাকার বাইরে (Outside Dhaka - ৳{{ number_format($outsideDhakaFee, 0) }})
                            </option>
                        </select>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-bold text-dark small mb-1">
                            ডেলিভারির সম্পূর্ণ ঠিকানা (Shipping Address) <span class="text-danger">*</span>
                        </label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" placeholder="বাড়ি/হোল্ডিং নম্বর, রোড, এলাকা, থানা, জেলা..." required>{{ old('shipping_address') }}</textarea>
                    </div>

                    <!-- Payment Information -->
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="payment_method" class="form-label fw-bold text-dark small mb-1">পেমেন্ট মেথড</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="cod" {{ old('payment_method') === 'cod' ? 'selected' : '' }}>ক্যাশ অন ডেলিভারি (COD)</option>
                                <option value="bkash" {{ old('payment_method') === 'bkash' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                                <option value="nagad" {{ old('payment_method') === 'nagad' ? 'selected' : '' }}>নগদ (Nagad)</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="payment_status" class="form-label fw-bold text-dark small mb-1">পেমেন্ট স্ট্যাটাস</label>
                            <select name="payment_status" id="payment_status" class="form-select">
                                <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>পেন্ডিং (Pending)</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>পরিশোধিত (Paid)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transaction ID (Optional) -->
                    <div class="mb-3" id="trxIdContainer">
                        <label for="transaction_id" class="form-label fw-bold text-dark small mb-1">ট্রানজ্যাকশন আইডি (TrxID - ঐচ্ছিক)</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="বিকাশ বা নগদ ট্রানজ্যাকশন আইডি">
                    </div>

                    <!-- Order Status -->
                    <div class="mb-3">
                        <label for="order_status" class="form-label fw-bold text-dark small mb-1">প্রাথমিক অর্ডার স্ট্যাটাস</label>
                        <select name="order_status" id="order_status" class="form-select">
                            <option value="pending" {{ old('order_status') === 'pending' ? 'selected' : '' }}>পেন্ডিং (Pending)</option>
                            <option value="processing" {{ old('order_status') === 'processing' ? 'selected' : '' }}>প্রসেসিং (Processing)</option>
                            <option value="shipped" {{ old('order_status') === 'shipped' ? 'selected' : '' }}>কুরিয়ারে আছে (Shipped)</option>
                        </select>
                    </div>

                    <!-- Customer Note -->
                    <div class="mb-3">
                        <label for="customer_note" class="form-label fw-bold text-dark small mb-1">গ্রাহকের বিশেষ নোট (Customer Note)</label>
                        <textarea name="customer_note" id="customer_note" rows="2" class="form-control" placeholder="গ্রাহকের কোনো বিশেষ নির্দেশনা থাকলে...">{{ old('customer_note') }}</textarea>
                    </div>

                    <!-- Admin Notes -->
                    <div class="mb-0">
                        <label for="admin_notes" class="form-label fw-bold text-dark small mb-1">অভ্যন্তরীণ নোট (Admin Note)</label>
                        <textarea name="admin_notes" id="admin_notes" rows="2" class="form-control" placeholder="যেমন: ফেসবুক মেসেঞ্জারে কনফার্ম করা অর্ডার...">{{ old('admin_notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Products Cart & Financial Calculation -->
        <div class="col-12 col-lg-7">
            <!-- Product Selector Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-cart-plus text-success"></i>
                        <span>পণ্য নির্বাচন ও কার্ট</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-2 align-items-end mb-3">
                        <!-- Product Selection -->
                        <div class="col-12 col-md-6">
                            <label for="productPicker" class="form-label fw-bold small text-dark mb-1">পণ্য নির্বাচন করুন</label>
                            <select id="productPicker" class="form-select">
                                <option value="" selected disabled>-- তালিকা থেকে পণ্য বেছে নিন --</option>
                                @foreach($products as $prod)
                                <option value="{{ $prod->id }}"
                                        data-name="{{ $prod->name }}"
                                        data-price="{{ $prod->final_price }}"
                                        data-image="{{ $prod->primary_image_url }}"
                                        data-stock="{{ $prod->stock_quantity }}"
                                        data-variants="{{ json_encode($prod->variants) }}">
                                    {{ $prod->name }} (৳{{ number_format($prod->final_price, 0) }}) - স্টক: {{ $prod->stock_quantity }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Variant Selection (Dynamically populated) -->
                        <div class="col-12 col-md-6" id="variantPickerContainer" style="display: none;">
                            <label for="variantPicker" class="form-label fw-bold small text-dark mb-1">ভ্যারিয়েন্ট (কালার / সাইজ)</label>
                            <select id="variantPicker" class="form-select">
                                <option value="">-- সাধারণ পণ্য (ডিফল্ট) --</option>
                            </select>
                        </div>

                        <!-- Unit Price Input -->
                        <div class="col-6 col-md-4">
                            <label for="pickerUnitPrice" class="form-label fw-bold small text-dark mb-1">একক মূল্য (৳)</label>
                            <input type="number" id="pickerUnitPrice" class="form-control" min="0" step="1" placeholder="মূল্য">
                        </div>

                        <!-- Quantity Input -->
                        <div class="col-6 col-md-4">
                            <label for="pickerQuantity" class="form-label fw-bold small text-dark mb-1">পরিমাণ</label>
                            <input type="number" id="pickerQuantity" class="form-control text-center fw-bold" value="1" min="1" max="999">
                        </div>

                        <!-- Add Button -->
                        <div class="col-12 col-md-4">
                            <button type="button" class="btn btn-primary-sidq w-100 fw-bold py-2 shadow-sm" id="btnAddToCart">
                                <i class="fas fa-plus me-1"></i> অর্ডারে যোগ করুন
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive rounded-3 border mb-3">
                        <table class="table table-hover align-middle mb-0" id="orderItemsTable">
                            <thead class="table-light small text-uppercase fw-bold text-muted">
                                <tr>
                                    <th style="min-width: 200px;">পণ্য বিবরণ</th>
                                    <th style="width: 100px;" class="text-center">পরিমাণ</th>
                                    <th style="width: 120px;" class="text-end">একক মূল্য</th>
                                    <th style="width: 120px;" class="text-end">মোট মূল্য</th>
                                    <th style="width: 60px;" class="text-center"><i class="fas fa-trash text-muted"></i></th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsTableBody">
                                <!-- Populated dynamically by JavaScript -->
                                <tr id="emptyItemsRow">
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        <i class="fas fa-shopping-basket fs-4 d-block mb-2 text-secondary opacity-50"></i>
                                        এখনও কোনো পণ্য যুক্ত করা হয়নি। ওপর থেকে পণ্য নির্বাচন করে <strong>"অর্ডারে যোগ করুন"</strong> চাপুন।
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Calculation & Bill Breakdown Card -->
                    <div class="bg-light p-3 rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">পণ্যের সাবটোটাল (Subtotal):</span>
                            <span class="fw-bold fs-6 text-dark" id="displaySubtotal">৳০.০০</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">ডেলিভারি চার্জ (Delivery Fee):</span>
                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                <span class="input-group-text bg-white">৳</span>
                                <input type="number" name="shipping_charge" id="shippingChargeInput" class="form-control text-end fw-bold" value="{{ old('shipping_charge', $insideDhakaFee) }}" min="0" step="1">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">অতিরিক্ত ডিসকাউন্ট (Discount):</span>
                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                <span class="input-group-text bg-white text-danger">-৳</span>
                                <input type="number" name="discount_amount" id="discountAmountInput" class="form-control text-end text-danger fw-bold" value="{{ old('discount_amount', 0) }}" min="0" step="1">
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5 text-dark">সর্বমোট প্রদেয় বিল (Grand Total):</span>
                            <span class="fw-bold fs-4 text-danger" id="displayGrandTotal">৳{{ number_format($insideDhakaFee, 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-3 text-end">
                    <button type="submit" class="btn btn-danger btn-lg px-4 fw-bold shadow-sm" id="btnSubmitOrder">
                        <i class="fas fa-check-circle me-1"></i> অর্ডার সম্পন্ন ও কনফার্ম করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let cartItems = [];
    let itemCounter = 0;

    const productPicker = document.getElementById('productPicker');
    const variantContainer = document.getElementById('variantPickerContainer');
    const variantPicker = document.getElementById('variantPicker');
    const pickerUnitPrice = document.getElementById('pickerUnitPrice');
    const pickerQuantity = document.getElementById('pickerQuantity');
    const btnAddToCart = document.getElementById('btnAddToCart');
    const itemsTableBody = document.getElementById('orderItemsTableBody');
    const emptyRow = document.getElementById('emptyItemsRow');

    const deliveryZoneSelect = document.getElementById('deliveryZoneSelect');
    const shippingChargeInput = document.getElementById('shippingChargeInput');
    const discountAmountInput = document.getElementById('discountAmountInput');
    const displaySubtotal = document.getElementById('displaySubtotal');
    const displayGrandTotal = document.getElementById('displayGrandTotal');
    const orderForm = document.getElementById('manualOrderForm');

    // 1. When Product is Selected
    productPicker.addEventListener('change', function() {
        const selectedOpt = productPicker.options[productPicker.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) return;

        const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
        pickerUnitPrice.value = price;

        // Check Variants
        let variants = [];
        try {
            variants = JSON.parse(selectedOpt.getAttribute('data-variants')) || [];
        } catch(e) {}

        variantPicker.innerHTML = '';
        if (variants && variants.length > 0) {
            variantContainer.style.display = 'block';
            let defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- কোনো ভ্যারিয়েন্ট নেই (সাধারণ) --';
            variantPicker.appendChild(defaultOpt);

            variants.forEach(function(v) {
                let opt = document.createElement('option');
                opt.value = v.id;
                let details = [];
                if (v.color) details.push('কালার: ' + v.color);
                if (v.size) details.push('সাইজ: ' + v.size);
                let text = details.join(' | ') || ('ভ্যারিয়েন্ট #' + v.id);
                text += ' (স্টক: ' + v.stock_quantity + ')';
                opt.textContent = text;
                opt.setAttribute('data-color', v.color || '');
                opt.setAttribute('data-size', v.size || '');
                opt.setAttribute('data-price', v.price || price);
                variantPicker.appendChild(opt);
            });
        } else {
            variantContainer.style.display = 'none';
        }
    });

    // Variant change price update
    variantPicker.addEventListener('change', function() {
        const vOpt = variantPicker.options[variantPicker.selectedIndex];
        if (vOpt && vOpt.value) {
            const vPrice = parseFloat(vOpt.getAttribute('data-price'));
            if (vPrice > 0) {
                pickerUnitPrice.value = vPrice;
            }
        }
    });

    // 2. Add Item to Table
    btnAddToCart.addEventListener('click', function() {
        const selectedOpt = productPicker.options[productPicker.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            alert('অনুগ্রহ করে একটি পণ্য নির্বাচন করুন!');
            productPicker.focus();
            return;
        }

        const productId = selectedOpt.value;
        const productName = selectedOpt.getAttribute('data-name');
        const productImage = selectedOpt.getAttribute('data-image');
        const unitPrice = parseFloat(pickerUnitPrice.value) || 0;
        const quantity = parseInt(pickerQuantity.value) || 1;

        if (unitPrice < 0) {
            alert('পণ্যের মূল্য ০ বা তার বেশি হতে হবে!');
            return;
        }
        if (quantity < 1) {
            alert('পণ্যের পরিমাণ অন্তত ১ হতে হবে!');
            return;
        }

        let variantId = null;
        let variantColor = '';
        let variantSize = '';
        const vOpt = variantPicker.options[variantPicker.selectedIndex];
        if (vOpt && vOpt.value) {
            variantId = vOpt.value;
            variantColor = vOpt.getAttribute('data-color') || '';
            variantSize = vOpt.getAttribute('data-size') || '';
        }

        // Check if item with same product and variant is already in cart
        const existingIndex = cartItems.findIndex(function(it) {
            return it.productId == productId && it.variantId == variantId;
        });

        if (existingIndex > -1) {
            // Update quantity
            cartItems[existingIndex].quantity += quantity;
            cartItems[existingIndex].unitPrice = unitPrice;
        } else {
            itemCounter++;
            cartItems.push({
                uid: itemCounter,
                productId: productId,
                variantId: variantId,
                productName: productName,
                productImage: productImage,
                variantColor: variantColor,
                variantSize: variantSize,
                unitPrice: unitPrice,
                quantity: quantity
            });
        }

        // Reset Picker Inputs
        pickerQuantity.value = 1;

        renderCart();
    });

    // 3. Render Cart Table
    function renderCart() {
        if (cartItems.length === 0) {
            itemsTableBody.innerHTML = `
                <tr id="emptyItemsRow">
                    <td colspan="5" class="text-center py-4 text-muted small">
                        <i class="fas fa-shopping-basket fs-4 d-block mb-2 text-secondary opacity-50"></i>
                        এখনও কোনো পণ্য যুক্ত করা হয়নি। ওপর থেকে পণ্য নির্বাচন করে <strong>"অর্ডারে যোগ করুন"</strong> চাপুন।
                    </td>
                </tr>
            `;
            updateCalculations();
            return;
        }

        let html = '';
        cartItems.forEach(function(item, index) {
            const rowTotal = item.unitPrice * item.quantity;
            let variantBadge = '';
            if (item.variantColor || item.variantSize) {
                variantBadge = `<span class="badge bg-light text-dark border small mt-1">
                    ${item.variantColor ? 'কালার: ' + item.variantColor : ''} 
                    ${item.variantSize ? 'সাইজ: ' + item.variantSize : ''}
                </span>`;
            }

            html += `
                <tr data-uid="${item.uid}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${item.productImage}" alt="${item.productName}" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                            <div>
                                <div class="fw-bold small text-dark lh-sm">${item.productName}</div>
                                ${variantBadge}
                            </div>
                        </div>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                        <input type="hidden" name="items[${index}][variant_id]" value="${item.variantId || ''}">
                    </td>
                    <td class="text-center">
                        <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm text-center fw-bold row-qty-input" value="${item.quantity}" min="1" max="999" style="width: 70px; margin: 0 auto;" onchange="updateItemQuantity(${item.uid}, this.value)">
                    </td>
                    <td class="text-end">
                        <input type="number" name="items[${index}][unit_price]" class="form-control form-control-sm text-end fw-bold row-price-input" value="${item.unitPrice}" min="0" step="1" style="width: 95px; margin-left: auto;" onchange="updateItemPrice(${item.uid}, this.value)">
                    </td>
                    <td class="text-end fw-bold text-dark">
                        ৳${rowTotal.toLocaleString('en-US', { minimumFractionDigits: 0 })}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 border-0" onclick="removeCartItem(${item.uid})" title="মুছে ফেলুন">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        itemsTableBody.innerHTML = html;
        updateCalculations();
    }

    // 4. Update Item Quantity & Price from table
    window.updateItemQuantity = function(uid, newQty) {
        const qty = parseInt(newQty) || 1;
        const item = cartItems.find(it => it.uid == uid);
        if (item) {
            item.quantity = Math.max(1, qty);
            renderCart();
        }
    };

    window.updateItemPrice = function(uid, newPrice) {
        const price = parseFloat(newPrice) || 0;
        const item = cartItems.find(it => it.uid == uid);
        if (item) {
            item.unitPrice = Math.max(0, price);
            renderCart();
        }
    };

    window.removeCartItem = function(uid) {
        cartItems = cartItems.filter(it => it.uid != uid);
        renderCart();
    };

    // 5. Delivery Zone Change -> Update Shipping Fee automatically
    deliveryZoneSelect.addEventListener('change', function() {
        const opt = deliveryZoneSelect.options[deliveryZoneSelect.selectedIndex];
        if (opt) {
            const fee = parseFloat(opt.getAttribute('data-fee')) || 0;
            shippingChargeInput.value = fee;
            updateCalculations();
        }
    });

    // 6. Recalculate Subtotal, Shipping, Discount, Grand Total
    function updateCalculations() {
        let subtotal = 0;
        cartItems.forEach(function(it) {
            subtotal += (it.unitPrice * it.quantity);
        });

        const shipping = parseFloat(shippingChargeInput.value) || 0;
        const discount = parseFloat(discountAmountInput.value) || 0;
        const grandTotal = Math.max(0, (subtotal + shipping - discount));

        displaySubtotal.textContent = '৳' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 0 });
        displayGrandTotal.textContent = '৳' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 0 });
    }

    shippingChargeInput.addEventListener('input', updateCalculations);
    discountAmountInput.addEventListener('input', updateCalculations);

    // 7. Form Submit Validation
    orderForm.addEventListener('submit', function(e) {
        if (cartItems.length === 0) {
            e.preventDefault();
            alert('অর্ডার সম্পন্ন করতে অন্তত একটি পণ্য যুক্ত করা আবশ্যক!');
            productPicker.focus();
            return false;
        }

        const phoneInput = document.getElementById('customer_phone');
        if (phoneInput && !phoneInput.value.trim()) {
            e.preventDefault();
            alert('অনুগ্রহ করে গ্রাহকের মোবাইল নম্বর লিখুন!');
            phoneInput.focus();
            return false;
        }

        const btnSubmit = document.getElementById('btnSubmitOrder');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> অর্ডার তৈরি হচ্ছে...';
    });
});
</script>
@endsection
