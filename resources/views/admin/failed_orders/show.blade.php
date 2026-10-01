@extends('layouts.admin')

@section('title', 'ফেইল্ড অর্ডার বিবরণ — #' . $failedOrder->id)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.failed-orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
            <h3 class="fw-bold text-dark mb-0">ফেইল্ড অর্ডার #{{ $failedOrder->id }} বিবরণ</h3>
        </div>
        <div class="d-flex gap-2">
            @if(!$failedOrder->is_recovered && !empty($failedOrder->customer_phone))
            <form method="POST" action="{{ route('admin.failed-orders.convert', $failedOrder->id) }}" onsubmit="return confirm('এই রেকর্ডটিকে লাইভ কনফার্মড অর্ডারে রূপান্তর করবেন?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1">
                    <i class="fas fa-cart-plus"></i>
                    <span>মূল অর্ডারে রূপান্তর করুন</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">গ্রাহকের বিবরণ</h5>
                <dl class="row mb-0 small">
                    <dt class="col-sm-4 text-muted">নাম:</dt>
                    <dd class="col-sm-8 fw-semibold text-dark">{{ $failedOrder->customer_name ?: 'নামহীন' }}</dd>

                    <dt class="col-sm-4 text-muted">ফোন নম্বর:</dt>
                    <dd class="col-sm-8">
                        @if($failedOrder->customer_phone)
                            <a href="tel:{{ $failedOrder->customer_phone }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                {{ $failedOrder->customer_phone }}
                            </a>
                            @if($failedOrder->whatsapp_url)
                                <a href="{{ $failedOrder->whatsapp_url }}" target="_blank" class="badge ms-2" style="background: #25D366; color: white;">
                                    WhatsApp
                                </a>
                            @endif
                        @else
                            <span class="text-muted">দেওয়া হয়নি</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-muted">ডেলিভারি ঠিকানা:</dt>
                    <dd class="col-sm-8">{{ $failedOrder->shipping_address ?: 'দেওয়া হয়নি' }}</dd>

                    <dt class="col-sm-4 text-muted">ডেলিভারি জোন:</dt>
                    <dd class="col-sm-8">{{ $failedOrder->delivery_zone === 'outside_dhaka' ? 'ঢাকার বাহিরে' : 'ঢাকার ভিতরে' }}</dd>

                    <dt class="col-sm-4 text-muted">পেমেন্ট মেথড:</dt>
                    <dd class="col-sm-8">{{ strtoupper($failedOrder->payment_method ?: 'COD') }}</dd>

                    <dt class="col-sm-4 text-muted">আইপি অ্যাড্রেস:</dt>
                    <dd class="col-sm-8 font-monospace">{{ $failedOrder->ip_address ?: 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted">রেকর্ড তৈরির সময়:</dt>
                    <dd class="col-sm-8">{{ $failedOrder->created_at->format('d M, Y h:i A') }} ({{ $failedOrder->created_at->diffForHumans() }})</dd>
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">কার্ট ও প্রোডাক্টস</h5>
                @if(!empty($failedOrder->cart_items) && is_array($failedOrder->cart_items))
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($failedOrder->cart_items as $item)
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-2 border">
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" class="rounded" style="width: 36px; height: 36px; object-fit: cover;">
                                @endif
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $item['name'] ?? 'Product' }}</div>
                                    <div class="text-muted" style="font-size: 11px;">৳{{ number_format($item['unit_price'] ?? 0, 0) }} × {{ $item['quantity'] ?? 1 }}</div>
                                </div>
                            </div>
                            <span class="fw-bold text-dark small">৳{{ number_format($item['total_price'] ?? 0, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-top pt-2 small">
                        <div class="d-flex justify-content-between text-muted">
                            <span>সাব-টোটাল:</span>
                            <span>৳{{ number_format($failedOrder->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>ডেলিভারি চার্জ:</span>
                            <span>৳{{ number_format($failedOrder->shipping_charge, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold text-dark fs-6 mt-1 border-top pt-1">
                            <span>সর্বমোট:</span>
                            <span class="text-danger">৳{{ number_format($failedOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">কোনো কার্ট আইটেম সংরক্ষিত নেই।</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
