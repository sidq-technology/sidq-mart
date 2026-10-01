@extends('layouts.app')

@section('title', 'অর্ডার সফল হয়েছে - ' . $order->order_number . ' | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">
            
            <!-- Main Success Card -->
            <div class="card border-0 shadow-sm p-3 p-sm-4 p-md-5 rounded-4 text-center mb-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width: 68px; height: 68px;">
                        <i class="fas fa-check fa-2x"></i>
                    </span>
                </div>

                <h2 class="fw-bold text-dark mb-1 fs-3 fs-md-2">অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!</h2>
                <p class="text-muted small mb-3">আপনার অর্ডারের জন্য আন্তরিক ধন্যবাদ। আমাদের প্রতিনিধি শীঘ্রই আপনার সাথে যোগাযোগ করবেন।</p>

                <div class="alert alert-light border py-2 py-sm-3 my-2 my-sm-3 d-inline-block w-100 rounded-3">
                    <span class="text-muted d-block small" style="font-size: 12px;">অর্ডার রেফারেন্স নম্বর:</span>
                    <strong class="fs-4 text-danger tabular-nums">{{ $order->order_number }}</strong>
                </div>

                <!-- Toast / Notification Box for 1-Click Upsell -->
                <div id="upsellToastBox" class="mt-2 text-start"></div>

                <!-- ==========================================
                     Post-Purchase 1-Click Upsell Section
                =========================================== -->
                @if(!empty($upsellEnabled) && $upsellProducts->isNotEmpty())
                <div class="card border-0 rounded-4 p-3 p-md-4 my-3 my-md-4 text-start shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px dashed #f59e0b !important;">
                    <div class="text-center mb-3">
                        @if(!empty($upsellBadgeText))
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border border-warning text-warning-emphasis small fw-bold mb-2 shadow-xs" style="font-size: 11.5px;">
                            <i class="fas fa-fire text-danger"></i>
                            <span>{{ $upsellBadgeText }}</span>
                        </div>
                        @endif
                        <h4 class="fw-bold text-dark mb-1 fs-5 fs-md-4">{{ $upsellHeading }}</h4>
                        <p class="text-muted small mb-0">{{ $upsellSubtitle }}</p>
                    </div>

                    @php
                        $upsellCount = $upsellProducts->count();
                        $colClass = $upsellCount === 1 ? 'col-12 col-sm-9 col-md-7 mx-auto' : ($upsellCount === 2 ? 'col-12 col-sm-6' : 'col-12 col-sm-6 col-md-4');
                        $imgBoxClass = $upsellCount === 1 ? 'upsell-img-box-single' : 'upsell-img-box-multi';
                        $titleSizeClass = $upsellCount === 1 ? 'fs-5' : 'fs-6';
                    @endphp
                    <div class="row g-3 justify-content-center">
                        @foreach($upsellProducts as $upsellProd)
                        <div class="{{ $colClass }}" id="upsellCard-{{ $upsellProd->id }}">
                            <div class="card h-100 border bg-white rounded-3 shadow-sm p-3 p-md-4 text-center d-flex flex-column justify-content-between upsell-item-card">
                                <div>
                                    <div class="position-relative mb-3 rounded-3 overflow-hidden bg-white border {{ $imgBoxClass }}">
                                        <img src="{{ $upsellProd->primary_image_url }}" alt="{{ $upsellProd->name }}" class="w-100 h-100 upsell-main-img">
                                        @if($upsellProd->is_on_sale)
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2 px-2.5 py-1.5 shadow-sm fw-bold" style="font-size: 12px;">
                                            -{{ $upsellProd->discount_percentage }}% ছাড়
                                        </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2 {{ $titleSizeClass }} upsell-product-title" title="{{ $upsellProd->name }}">
                                        {{ $upsellProd->name }}
                                    </h5>
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                                        @if($upsellProd->is_on_sale)
                                            <span class="text-muted text-decoration-line-through fs-6">৳{{ number_format($upsellProd->price, 0) }}</span>
                                        @endif
                                        <span class="{{ $upsellCount === 1 ? 'fs-2' : 'fs-3' }} fw-bold text-danger">৳{{ number_format($upsellProd->final_price, 0) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="btn btn-danger w-100 py-2.5 py-md-3 fw-bold btn-add-upsell shadow-sm rounded-pill fs-6 d-inline-flex align-items-center justify-content-center gap-2" 
                                            data-product-id="{{ $upsellProd->id }}"
                                            data-product-name="{{ $upsellProd->name }}">
                                        <i class="fas fa-cart-plus fs-5"></i>
                                        <span>অর্ডারে যুক্ত করুন (+ ১-ক্লিক)</span>
                                    </button>
                                    <div class="mt-2 text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1" style="font-size: 11px;">
                                            <i class="fas fa-truck-moving me-1"></i> ফ্রি ডেলিভারি (আগের অর্ডারের সাথে যুক্ত হবে)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- ==========================================
                     Compact Collapsed Order Overview Bar
                =========================================== -->
                <div class="mt-3 text-start">
                    <div class="card border rounded-3 p-3 bg-light shadow-2xs">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                                    <span class="badge bg-white text-dark border small fw-normal text-uppercase">
                                        <i class="fas fa-wallet text-secondary me-1"></i> {{ $order->payment_method }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark fs-6">{{ $order->customer_name }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $order->customer_phone }}
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end w-100 w-sm-auto gap-3 pt-2 pt-sm-0 border-top border-sm-0">
                                <div class="text-start text-sm-end">
                                    <span class="text-muted small d-block" style="font-size: 11px;">সর্বমোট বিল</span>
                                    <span class="fs-4 fw-bold text-danger tabular-nums" id="overviewCompactTotal">৳{{ number_format($order->grand_total, 0) }}</span>
                                </div>
                                <button class="btn btn-sm btn-white bg-white border text-dark fw-semibold px-3 py-2 rounded-2 shadow-2xs d-inline-flex align-items-center gap-2 collapsed" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#orderDetailsCollapse" 
                                        aria-expanded="false" 
                                        aria-controls="orderDetailsCollapse"
                                        id="btnToggleOrderDetails">
                                    <span id="toggleDetailsText">বিস্তারিত দেখুন</span>
                                    <i class="fas fa-chevron-down text-muted transition-transform" id="toggleDetailsIcon" style="transition: transform 0.25s ease;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Collapsible Full Order Breakdown (Default: Collapsed) -->
                        <div class="collapse mt-3 pt-3 border-top" id="orderDetailsCollapse">
                            <!-- Full Delivery Details -->
                            <div class="mb-3">
                                <div class="small fw-bold text-dark mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> ডেলিভারির ঠিকানা:</div>
                                <div class="p-2 bg-white rounded border small text-secondary">{{ $order->shipping_address }}</div>
                            </div>

                            <!-- Full Order Items Table -->
                            <div class="table-responsive mb-3 bg-white rounded border">
                                <table class="table table-sm align-middle mb-0">
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
                                                    <img src="{{ $item->product_image }}" class="rounded me-2" style="width: 36px; height: 36px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium text-dark small">{{ $item->product_name }}</div>
                                                        @if(!empty($item->variant_text))
                                                        <small class="text-danger fw-semibold d-block" style="font-size: 10px;">{{ $item->variant_text }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small">৳{{ number_format($item->unit_price, 0) }}</td>
                                            <td class="small">{{ $item->quantity }}</td>
                                            <td class="text-end fw-bold small">৳{{ number_format($item->total_price, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="border-top small">
                                        <tr>
                                            <th colspan="3" class="text-end text-muted">সাবটোটাল:</th>
                                            <th class="text-end" id="orderSubtotalText">৳{{ number_format($order->subtotal, 0) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end text-muted">ডেলিভারি চার্জ:</th>
                                            <th class="text-end">৳{{ number_format($order->shipping_charge, 0) }}</th>
                                        </tr>
                                        @if($order->discount_amount > 0)
                                        <tr>
                                            <th colspan="3" class="text-end text-success">ডিসকাউন্ট:</th>
                                            <th class="text-end text-success">-৳{{ number_format($order->discount_amount, 0) }}</th>
                                        </tr>
                                        @endif
                                        <tr class="fs-6">
                                            <th colspan="3" class="text-end text-danger fw-bold">সর্বমোট বিল:</th>
                                            <th class="text-end text-danger fw-bold" id="orderGrandTotalText">৳{{ number_format($order->grand_total, 0) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2">
                                <a href="{{ route('home') }}" class="btn btn-sm btn-secondary-sidq">
                                    <i class="fas fa-home me-1"></i> হোম পেজে যান
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-dark" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> প্রিন্ট ইনভয়েস
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 Similar / Recommended Items (Up to 4 Products)
            =========================================== -->
            @if(isset($similarProducts) && $similarProducts->isNotEmpty())
            <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 text-start mb-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-5">
                            <i class="fas fa-sparkles text-warning"></i>
                            <span>আপনার পছন্দ হতে পারে এমন আরও পণ্য</span>
                        </h5>
                        <p class="text-muted small mb-0">আমাদের স্টোরের অন্যান্য সেরা ও জনপ্রিয় কালেকশন</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-danger d-none d-sm-inline-flex align-items-center gap-1">
                        সবগুলো দেখুন <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-md-4 g-2 g-md-3">
                    @foreach($similarProducts as $simProduct)
                    <div class="col">
                        @include('frontend.partials.product-card', ['product' => $simProduct])
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
.upsell-item-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.upsell-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.08) !important;
}
.upsell-img-box-single {
    height: 240px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 768px) {
    .upsell-img-box-single {
        height: 280px;
    }
}
.upsell-img-box-multi {
    height: 190px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 768px) {
    .upsell-img-box-multi {
        height: 220px;
    }
}
.upsell-main-img {
    object-fit: contain;
    padding: 8px;
    transition: transform 0.3s ease;
}
.upsell-item-card:hover .upsell-main-img {
    transform: scale(1.04);
}
.upsell-product-title {
    line-height: 1.45;
    min-height: 46px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // E-Commerce Tracking: Purchase Event (Fires once on page load)
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

        // Toggle Details Collapse Button Label & Icon
        const collapseEl = document.getElementById('orderDetailsCollapse');
        const toggleText = document.getElementById('toggleDetailsText');
        const toggleIcon = document.getElementById('toggleDetailsIcon');

        if (collapseEl && toggleText && toggleIcon) {
            collapseEl.addEventListener('show.bs.collapse', function () {
                toggleText.textContent = 'লুকিয়ে রাখুন';
                toggleIcon.style.transform = 'rotate(180deg)';
            });
            collapseEl.addEventListener('hide.bs.collapse', function () {
                toggleText.textContent = 'বিস্তারিত দেখুন';
                toggleIcon.style.transform = 'rotate(0deg)';
            });
        }

        // 1-Click Upsell AJAX Handler
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
                                        ${data.item.image ? `<img src="${data.item.image}" class="rounded me-2" style="width: 36px; height: 36px; object-fit: cover;">` : ''}
                                        <div>
                                            <div class="fw-medium text-dark small">${data.item.name}</div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">
                                                <i class="fas fa-bolt me-1"></i> ১-ক্লিক আপসেল (ফ্রি ডেলিভারি)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">৳${data.item.unit_price}</td>
                                <td class="small">${data.item.quantity}</td>
                                <td class="text-end fw-bold text-success small">৳${data.item.total_price}</td>
                            `;
                            tbody.appendChild(tr);
                        }

                        // Update Table Subtotal & Grand Total
                        const subtotalEl = document.getElementById('orderSubtotalText');
                        if (subtotalEl && data.subtotal) {
                            subtotalEl.textContent = `৳${data.subtotal}`;
                            subtotalEl.classList.add('text-success');
                        }

                        const grandTotalEl = document.getElementById('orderGrandTotalText');
                        if (grandTotalEl && data.grand_total) {
                            grandTotalEl.textContent = `৳${data.grand_total}`;
                        }

                        // Update Visible Compact Total
                        const compactTotalEl = document.getElementById('overviewCompactTotal');
                        if (compactTotalEl && data.grand_total) {
                            compactTotalEl.textContent = `৳${data.grand_total}`;
                            compactTotalEl.style.transition = 'transform 0.25s ease';
                            compactTotalEl.style.transform = 'scale(1.15)';
                            setTimeout(() => {
                                compactTotalEl.style.transform = 'scale(1)';
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
