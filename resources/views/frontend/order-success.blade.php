@extends('layouts.app')

@section('title', 'অর্ডার সফল হয়েছে - ' . $order->order_number . ' | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-8">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 text-center mb-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width: 72px; height: 72px;">
                        <i class="fas fa-check fa-2x"></i>
                    </span>
                </div>

                <h2 class="fw-bold text-dark mb-1">অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!</h2>
                <p class="text-muted">আপনার অর্ডারের জন্য আন্তরিক ধন্যবাদ। আমাদের প্রতিনিধি শীঘ্রই আপনার সাথে যোগাযোগ করবেন।</p>

                <div class="alert alert-light border py-3 my-3">
                    <span class="text-muted d-block small">অর্ডার রেফারেন্স নম্বর:</span>
                    <strong class="fs-4 text-danger">{{ $order->order_number }}</strong>
                </div>

                <!-- Toast / Notification Box for 1-Click Upsell -->
                <div id="upsellToastBox" class="mt-2 text-start"></div>

                <!-- ==========================================
                     Post-Purchase 1-Click Upsell Section
                =========================================== -->
                @if(!empty($upsellEnabled) && $upsellProducts->isNotEmpty())
                <div class="card border-0 rounded-4 p-3 p-md-4 my-4 text-start shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px dashed #f59e0b !important;">
                    <div class="text-center mb-3">
                        @if(!empty($upsellBadgeText))
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border border-warning text-warning-emphasis small fw-bold mb-2 shadow-xs">
                            <i class="fas fa-fire text-danger"></i>
                            <span>{{ $upsellBadgeText }}</span>
                        </div>
                        @endif
                        <h4 class="fw-bold text-dark mb-1">{{ $upsellHeading }}</h4>
                        <p class="text-muted small mb-0">{{ $upsellSubtitle }}</p>
                    </div>

                    <div class="row g-3 justify-content-center">
                        @foreach($upsellProducts as $upsellProd)
                        <div class="col-12 col-sm-{{ $upsellProducts->count() === 1 ? '10 col-md-8' : ($upsellProducts->count() === 2 ? '6' : '6 col-md-4') }}" id="upsellCard-{{ $upsellProd->id }}">
                            <div class="card h-100 border bg-white rounded-3 shadow-xs p-3 text-center d-flex flex-column justify-content-between">
                                <div>
                                    <div class="position-relative mb-2 rounded-2 overflow-hidden bg-light" style="height: 140px;">
                                        <img src="{{ $upsellProd->primary_image_url }}" alt="{{ $upsellProd->name }}" class="w-100 h-100" style="object-fit: contain; padding: 6px;">
                                        @if($upsellProd->is_on_sale)
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2 shadow-xs" style="font-size: 11px;">
                                            -{{ $upsellProd->discount_percentage }}% ছাড়
                                        </span>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="min-height: 38px; font-size: 13.5px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $upsellProd->name }}">
                                        {{ $upsellProd->name }}
                                    </h6>
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                                        @if($upsellProd->is_on_sale)
                                            <span class="text-muted text-decoration-line-through small">৳{{ number_format($upsellProd->price, 0) }}</span>
                                        @endif
                                        <span class="fs-5 fw-bold text-danger">৳{{ number_format($upsellProd->final_price, 0) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="btn btn-danger w-100 py-2 fw-bold btn-add-upsell shadow-xs rounded-2 d-inline-flex align-items-center justify-content-center gap-1" 
                                            data-product-id="{{ $upsellProd->id }}"
                                            data-product-name="{{ $upsellProd->name }}">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>অর্ডারে যুক্ত করুন</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Order Details Summary -->
                <div class="text-start mt-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">ডেলিভারির তথ্য</h5>
                    <div class="row g-2 small mb-4">
                        <div class="col-sm-6">
                            <span class="text-muted">গ্রাহকের নাম:</span>
                            <div class="fw-bold">{{ $order->customer_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">মোবাইল নম্বর:</span>
                            <div class="fw-bold">{{ $order->customer_phone }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <span class="text-muted">ডেলিভারি ঠিকানা:</span>
                            <div class="fw-bold">{{ $order->shipping_address }}</div>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted">পেমেন্ট মেথড:</span>
                            <div class="fw-bold text-uppercase">{{ $order->payment_method }} ({{ $order->payment_status }})</div>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted">অর্ডার স্ট্যাটাস:</span>
                            <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                        </div>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3">অর্ডারকৃত পণ্যসমূহ</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>পণ্য</th>
                                    <th>মূল্য</th>
                                    <th>পরিমাণ</th>
                                    <th class="text-end">মোট</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody">
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product_image)
                                            <img src="{{ $item->product_image }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $item->product_name }}</div>
                                                @if(!empty($item->variant_text))
                                                <small class="text-danger fw-semibold d-block" style="font-size: 11px;">{{ $item->variant_text }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>৳{{ number_format($item->unit_price, 0) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($item->total_price, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th colspan="3" class="text-end">সাবটোটাল:</th>
                                    <th class="text-end" id="orderSubtotalText">৳{{ number_format($order->subtotal, 0) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">ডেলিভারি চার্জ:</th>
                                    <th class="text-end">৳{{ number_format($order->shipping_charge, 0) }}</th>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <th colspan="3" class="text-end text-success">ডিসকাউন্ট:</th>
                                    <th class="text-end text-success">-৳{{ number_format($order->discount_amount, 0) }}</th>
                                </tr>
                                @endif
                                <tr class="fs-5">
                                    <th colspan="3" class="text-end text-danger">সর্বমোট বিল:</th>
                                    <th class="text-end text-danger fw-bold" id="orderGrandTotalText">৳{{ number_format($order->grand_total, 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('home') }}" class="btn btn-secondary-sidq">
                            <i class="fas fa-home me-1"></i> হোম পেজে যান
                        </a>
                        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> প্রিন্ট ইনভয়েস
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // E-Commerce Tracking: Purchase / Conversion Event
        try {
            const orderGrandTotal = {{ (float) $order->grand_total }};
            const orderShipping = {{ (float) $order->shipping_charge }};
            const orderNumber = '{{ $order->order_number }}';
            const orderItemIds = [ {!! implode(',', $order->items->pluck('product_id')->filter()->map(fn($id) => "'$id'")->toArray()) !!} ];
            const totalQty = {{ (int) $order->items->sum('quantity') }};

            // Meta (Facebook) Pixel: Purchase
            if (typeof fbq === 'function') {
                fbq('track', 'Purchase', {
                    content_type: 'product',
                    content_ids: orderItemIds,
                    value: orderGrandTotal,
                    currency: 'BDT',
                    num_items: totalQty
                });
            }

            // TikTok Pixel: CompletePayment
            if (typeof ttq === 'object') {
                ttq.track('CompletePayment', {
                    content_type: 'product',
                    value: orderGrandTotal,
                    currency: 'BDT'
                });
            }

            // Google Tag Manager / GA4: purchase
            if (window.dataLayer) {
                window.dataLayer.push({
                    event: 'purchase',
                    ecommerce: {
                        transaction_id: orderNumber,
                        value: orderGrandTotal,
                        shipping: orderShipping,
                        currency: 'BDT',
                        items: {!! json_encode($order->items->map(fn($it) => [
                            'item_id' => (string)$it->product_id,
                            'item_name' => (string)$it->product_name,
                            'price' => (float)$it->unit_price,
                            'quantity' => (int)$it->quantity,
                        ])) !!}
                    }
                });
            }
        } catch (e) {
            console.error('Tracking Error (Purchase):', e);
        }

        document.querySelectorAll('.btn-add-upsell').forEach(button => {
            button.addEventListener('click', function () {
                const prodId = this.dataset.productId;
                const originalHtml = this.innerHTML;

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> যোগ হচ্ছে...';

                fetch("{{ route('order.add-upsell', $order->order_number) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: prodId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Track Additional Upsell Purchase
                        try {
                            const upsellVal = parseFloat(data.item.total_price) || 0;
                            if (typeof fbq === 'function') {
                                fbq('track', 'Purchase', {
                                    content_type: 'product',
                                    content_ids: [String(prodId)],
                                    value: upsellVal,
                                    currency: 'BDT',
                                    num_items: 1
                                });
                            }
                            if (typeof ttq === 'object') {
                                ttq.track('CompletePayment', {
                                    content_type: 'product',
                                    value: upsellVal,
                                    currency: 'BDT'
                                });
                            }
                        } catch (err) {}

                        // Change button to success state
                        this.classList.remove('btn-danger', 'btn-primary-sidq');
                        this.classList.add('btn-success');
                        this.innerHTML = '<i class="fas fa-check-circle me-1"></i> অর্ডারে যুক্ত হয়েছে!';

                        // Append row into table
                        const tbody = document.getElementById('orderItemsBody');
                        if (tbody && data.item) {
                            const tr = document.createElement('tr');
                            tr.className = 'table-success';
                            tr.innerHTML = `
                                <td>
                                    <div class="d-flex align-items-center">
                                        ${data.item.image ? `<img src="${data.item.image}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                                        <div>
                                            <div class="fw-medium">${data.item.name}</div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">
                                                <i class="fas fa-bolt me-1"></i> ১-ক্লিক আপসেল (ফ্রি ডেলিভারি)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>৳${data.item.unit_price}</td>
                                <td>${data.item.quantity}</td>
                                <td class="text-end fw-bold text-success">৳${data.item.total_price}</td>
                            `;
                            tbody.appendChild(tr);
                        }

                        // Update Subtotal & Grand Total
                        const subtotalEl = document.getElementById('orderSubtotalText');
                        if (subtotalEl && data.subtotal) {
                            subtotalEl.textContent = `৳${data.subtotal}`;
                            subtotalEl.classList.add('text-success');
                        }

                        const grandTotalEl = document.getElementById('orderGrandTotalText');
                        if (grandTotalEl && data.grand_total) {
                            grandTotalEl.textContent = `৳${data.grand_total}`;
                            grandTotalEl.style.transition = 'transform 0.25s ease';
                            grandTotalEl.style.transform = 'scale(1.15)';
                            setTimeout(() => {
                                grandTotalEl.style.transform = 'scale(1)';
                            }, 350);
                        }

                        // Banner notification
                        const toastBox = document.getElementById('upsellToastBox');
                        if (toastBox) {
                            toastBox.innerHTML = `
                                <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center justify-content-between p-3 rounded-3" role="alert">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-check-circle fs-4 text-success"></i>
                                        <div>
                                            <div class="fw-bold">${data.item.name} সফলভাবে অর্ডারে যুক্ত হয়েছে!</div>
                                            <small class="text-muted">অতিরিক্ত ডেলিভারি চার্জ ছাড়াই একই পার্সেলে এটি পাঠানো হবে।</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            toastBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    } else {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                        alert(data.message || 'পণ্য যুক্ত করতে সমস্যা হয়েছে।');
                    }
                })
                .catch(() => {
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                    alert('সার্ভারের সাথে যোগাযোগ করতে সমস্যা হয়েছে।');
                });
            });
        });
    });
</script>
@endpush
