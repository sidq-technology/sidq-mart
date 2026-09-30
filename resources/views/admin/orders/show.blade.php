@extends('layouts.admin')

@section('title', 'অর্ডার বিস্তারিত: ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> অর্ডারের তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">অর্ডার: {{ $order->order_number }}</h3>
        <span class="text-muted small">তারিখ: {{ $order->created_at->format('d F Y, h:i A') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-dark">
            <i class="fas fa-print me-1"></i> ইনভয়েস প্রিন্ট করুন
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Order Items & Pricing Breakdown -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">অর্ডারকৃত পণ্যসমূহ (Order Items)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>পণ্য</th>
                                <th>একক মূল্য</th>
                                <th>পরিমাণ</th>
                                <th class="text-end">মোট মূল্য</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="rounded me-2" style="width: 48px; height: 48px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $item->product_name }}</div>
                                            @if($item->product)
                                            <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>৳{{ number_format($item->unit_price, 0) }}</td>
                                <td>{{ $item->quantity }} টি</td>
                                <td class="text-end fw-bold">৳{{ number_format($item->total_price, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="3" class="text-end text-muted">পণ্যের মূল্য (Subtotal):</th>
                                <th class="text-end">৳{{ number_format($order->subtotal, 0) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end text-muted">ডেলিভারি চার্জ ({{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}):</th>
                                <th class="text-end">৳{{ number_format($order->shipping_charge, 0) }}</th>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <th colspan="3" class="text-end text-success">কুপন ডিসকাউন্ট:</th>
                                <th class="text-end text-success">-৳{{ number_format($order->discount_amount, 0) }}</th>
                            </tr>
                            @endif
                            <tr class="fs-5">
                                <th colspan="3" class="text-end text-danger">সর্বমোট বিল (Grand Total):</th>
                                <th class="text-end text-danger">৳{{ number_format($order->grand_total, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($order->customer_note)
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-light">
            <h6 class="fw-bold mb-1 text-dark"><i class="fas fa-comment-dots text-danger me-1"></i> গ্রাহকের নোট:</h6>
            <p class="mb-0 text-muted">{{ $order->customer_note }}</p>
        </div>
        @endif
    </div>

    <!-- Right: Customer Info & Status Manager -->
    <div class="col-12 col-lg-4">
        <!-- Status Update Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">স্ট্যাটাস পরিবর্তন (Update Status)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="order_status" class="form-label small fw-bold">অর্ডার স্ট্যাটাস</label>
                        <select name="order_status" id="order_status" class="form-select">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending (পেন্ডিং)</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing (প্রসেসিং)</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped / In Courier (কুরিয়ারে আছে)</option>
                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered (ডেলিভার্ড)</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled (বাতিল)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="admin_notes" class="form-label small fw-bold">অ্যাডমিন নোট (Internal Notes)</label>
                        <textarea name="admin_notes" id="admin_notes" rows="2" class="form-control" placeholder="কুরিয়ার ট্র্যাকিং কোড বা ডেলিভারি তথ্য...">{{ $order->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2">
                        <i class="fas fa-save me-1"></i> স্ট্যাটাস আপডেট করুন
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer & Shipping Address Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">গ্রাহকের তথ্য</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small">গ্রাহকের নাম:</span>
                    <div class="fw-bold fs-6">{{ $order->customer_name }}</div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">মোবাইল নম্বর:</span>
                    <div class="fw-bold fs-6">
                        <a href="tel:{{ $order->customer_phone }}" class="text-danger text-decoration-none">
                            <i class="fas fa-phone-alt me-1"></i> {{ $order->customer_phone }}
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">ডেলিভারির সম্পূর্ণ ঠিকানা:</span>
                    <div class="p-2 bg-light rounded border small fw-medium mt-1">
                        {{ $order->shipping_address }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">পেমেন্ট মেথড:</span>
                    <div class="fw-bold text-uppercase">{{ $order->payment_method }}</div>
                    @if($order->transaction_id)
                    <div class="small text-danger fw-bold">TrxID: {{ $order->transaction_id }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
