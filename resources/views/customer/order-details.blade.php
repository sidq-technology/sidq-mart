@extends('layouts.app')

@section('title', 'অর্ডার বিবরণী #' . $order->order_number . ' - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="py-5" style="background: #f8fffa; min-height: 80vh;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1"></i> সকল অর্ডারে ফিরুন
                </a>
                <h3 class="fw-bold mb-1 text-dark">
                    অর্ডার বিবরণী: <span style="color: var(--color-brand-accent);">#{{ $order->order_number }}</span>
                </h3>
                <p class="text-muted small mb-0">অর্ডার তারিখ: {{ $order->created_at->format('d F Y, h:i A') }}</p>
            </div>
            <div>
                <span class="badge {{ $order->status_badge_class }} fs-6 px-3 py-2">
                    স্ট্যাটাস: {{ strtoupper($order->order_status) }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Order Items & Calculation -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-shopping-basket me-2 text-danger"></i> অর্ডারের পণ্যসমূহ
                    </h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>পণ্য</th>
                                    <th class="text-center">মূল্য</th>
                                    <th class="text-center">পরিমাণ</th>
                                    <th class="text-end">মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->product && $item->product->primary_image_url)
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                                @if($item->product)
                                                <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">৳{{ number_format($item->unit_price, 0) }}</td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($item->subtotal, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary -->
                    <div class="row justify-content-end mt-3">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">সাবটোটাল:</span>
                                    <strong>৳{{ number_format($order->subtotal, 0) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">শিপিং চার্জ:</span>
                                    <span>৳{{ number_format($order->shipping_charge, 0) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>ডিসকাউন্ট:</span>
                                    <span>-৳{{ number_format($order->discount_amount, 0) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 fs-5 fw-bold text-dark">
                                    <span>সর্বমোট প্রদেয়:</span>
                                    <span style="color: var(--color-brand-accent);">৳{{ number_format($order->grand_total, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Details -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-truck me-2 text-danger"></i> ডেলিভারি তথ্য
                    </h5>
                    <div class="small d-flex flex-column gap-2">
                        <div><strong class="text-muted">নাম:</strong> <span class="text-dark fw-bold">{{ $order->customer_name }}</span></div>
                        <div><strong class="text-muted">মোবাইল:</strong> <span class="text-dark">{{ $order->customer_phone }}</span></div>
                        <div><strong class="text-muted">ঠিকানা:</strong> <span class="text-dark">{{ $order->shipping_address }}</span></div>
                        <div><strong class="text-muted">এরিয়া:</strong> <span class="badge bg-light text-dark border">{{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাহিরে' }}</span></div>
                        @if($order->customer_note)
                        <div><strong class="text-muted">নোট:</strong> <span class="fst-italic text-dark">{{ $order->customer_note }}</span></div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-wallet me-2 text-danger"></i> পেমেন্ট তথ্য
                    </h5>
                    <div class="small d-flex flex-column gap-2">
                        <div><strong class="text-muted">পদ্ধতি:</strong> <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span></div>
                        <div><strong class="text-muted">পেমেন্ট স্ট্যাটাস:</strong> <span class="badge bg-secondary">{{ strtoupper($order->payment_status) }}</span></div>
                        @if($order->transaction_id)
                        <div><strong class="text-muted">TrxID:</strong> <code class="fw-bold text-danger">{{ $order->transaction_id }}</code></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
