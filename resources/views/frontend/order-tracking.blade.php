@extends('layouts.app')

@section('title', 'অর্ডার ট্র্যাকিং | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

            <!-- Page Title & Search Box -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center mb-4 bg-white">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle shadow-2xs" style="width: 64px; height: 64px;">
                        <i class="fas fa-truck-fast fa-2x"></i>
                    </span>
                </div>
                <h2 class="fw-bold text-dark mb-2 fs-3 fs-md-2">অর্ডার ট্র্যাক করুন</h2>
                <p class="text-muted small mb-4">আপনার অর্ডার নম্বর অথবা মোবাইল নম্বর লিখে বর্তমান অবস্থা ও ডেলিভারি আপডেট জানুন</p>

                <!-- Search Input Form -->
                <form action="{{ route('order.tracking') }}" method="GET" class="col-12 col-md-10 col-lg-8 mx-auto">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border border-2 border-danger-subtle p-1 bg-white">
                        <span class="input-group-text bg-transparent border-0 ps-3 pe-2 text-muted">
                            <i class="fas fa-search text-danger"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-0 shadow-none px-2 text-dark fs-6" 
                               placeholder="অর্ডার নম্বর বা মোবাইল নম্বর লিখুন..." 
                               value="{{ $searchQuery }}" 
                               required 
                               autocomplete="off">
                        <button type="submit" class="btn btn-danger px-4 fw-bold rounded-pill shadow-xs d-inline-flex align-items-center gap-2">
                            <span>ট্র্যাক করুন</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Results Area -->
            @if($hasSearched)
                @if($orders->isEmpty())
                <!-- No Order Found State -->
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center bg-white mb-4">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 70px; height: 70px;">
                            <i class="fas fa-box-open fa-2x"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">কোনো অর্ডার পাওয়া যায়নি</h5>
                    <p class="text-muted small mb-3">
                        "<strong>{{ $searchQuery }}</strong>" দিয়ে কোনো অর্ডার রেকর্ড খুঁজে পাওয়া যায়নি। অনুগ্রহ করে সঠিক অর্ডার নম্বর বা মোবাইল নম্বর দিয়ে পুনরায় চেষ্টা করুন।
                    </p>
                    <div>
                        <a href="{{ route('order.tracking') }}" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                            <i class="fas fa-redo me-1"></i> নতুন করে খুঁজুন
                        </a>
                    </div>
                </div>
                @else
                <!-- Orders Found -->
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h5 class="fw-bold text-dark mb-0 fs-6">
                        <i class="fas fa-clipboard-check text-success me-1"></i>
                        <span>অর্ডারের ফলাফল ({{ $orders->count() }}টি পাওয়া গেছে)</span>
                    </h5>
                    <a href="{{ route('order.tracking') }}" class="btn btn-sm btn-light border text-muted px-3 rounded-pill small">
                        <i class="fas fa-rotate-left me-1"></i> নতুন সার্চ
                    </a>
                </div>

                @foreach($orders as $order)
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                    
                    <!-- Order Header Banner -->
                    <div class="card-header bg-light border-bottom p-3 p-md-4">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="text-muted small">অর্ডার নম্বর:</span>
                                    <span class="fw-bold text-danger fs-6 tabular-nums">{{ $order->order_number }}</span>
                                    @php
                                        $badgeBg = match($order->order_status) {
                                            'pending' => 'bg-warning text-dark',
                                            'processing' => 'bg-info text-white',
                                            'shipped' => 'bg-primary text-white',
                                            'delivered' => 'bg-success text-white',
                                            'cancelled' => 'bg-danger text-white',
                                            default => 'bg-secondary text-white',
                                        };
                                        $bengaliStatus = match($order->order_status) {
                                            'pending' => 'অর্ডার গৃহীত হয়েছে',
                                            'processing' => 'প্রসেসিং চলছে',
                                            'shipped' => 'কুরিয়ারে পাঠানো হয়েছে',
                                            'delivered' => 'ডেলিভারি সম্পন্ন',
                                            'cancelled' => 'অর্ডারটি বাতিল',
                                            default => 'প্রক্রিয়াধীন',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeBg }} small fw-semibold px-2.5 py-1 rounded-pill">
                                        {{ $bengaliStatus }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-secondary"></i>
                                    <span>অর্ডারের সময়: {{ $order->created_at->format('d/m/Y - h:i A') }}</span>
                                </div>
                            </div>

                            <div class="text-start text-sm-end">
                                <span class="text-muted small d-block" style="font-size: 11px;">সর্বমোট বিল</span>
                                <span class="fs-4 fw-bold text-danger tabular-nums">৳{{ number_format($order->grand_total, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        
                        <!-- Visual 4-Step Progress Tracker -->
                        @if($order->order_status === 'cancelled')
                        <div class="alert alert-danger d-flex align-items-center gap-3 rounded-3 mb-4 p-3" role="alert">
                            <i class="fas fa-circle-xmark fa-2x text-danger flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold mb-1">অর্ডারটি বাতিল করা হয়েছে</h6>
                                <p class="small mb-0 opacity-75">এই অর্ডারটি বর্তমানে বাতিল অবস্থায় রয়েছে।</p>
                            </div>
                        </div>
                        @else
                        @php
                            // Stepper stages: 1 = Placed, 2 = Processing, 3 = Shipped, 4 = Delivered
                            $currentStep = match($order->order_status) {
                                'pending' => 1,
                                'processing' => 2,
                                'shipped' => 3,
                                'delivered' => 4,
                                default => 1,
                            };
                        @endphp
                        <div class="order-stepper-wrap mb-4 py-2">
                            <div class="order-stepper">
                                
                                <!-- Step 1 -->
                                <div class="step-item {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep === 1 ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas {{ $currentStep > 1 ? 'fa-check' : 'fa-clipboard-check' }}"></i>
                                    </div>
                                    <div class="step-label">অর্ডার গৃহীত হয়েছে</div>
                                    <div class="step-desc text-muted">{{ $order->created_at->format('d M') }}</div>
                                </div>

                                <!-- Connecting Line 1 -->
                                <div class="step-line {{ $currentStep >= 2 ? 'filled' : '' }}"></div>

                                <!-- Step 2 -->
                                <div class="step-item {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep === 2 ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas {{ $currentStep > 2 ? 'fa-check' : 'fa-box-open' }}"></i>
                                    </div>
                                    <div class="step-label">প্রসেসিং চলছে</div>
                                    <div class="step-desc text-muted">প্যাকেজিং ও চেক</div>
                                </div>

                                <!-- Connecting Line 2 -->
                                <div class="step-line {{ $currentStep >= 3 ? 'filled' : '' }}"></div>

                                <!-- Step 3 -->
                                <div class="step-item {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep === 3 ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas {{ $currentStep > 3 ? 'fa-check' : 'fa-truck-fast' }}"></i>
                                    </div>
                                    <div class="step-label">কুরিয়ারে পাঠানো হয়েছে</div>
                                    <div class="step-desc text-muted">ডেলিভারির পথে</div>
                                </div>

                                <!-- Connecting Line 3 -->
                                <div class="step-line {{ $currentStep >= 4 ? 'filled' : '' }}"></div>

                                <!-- Step 4 -->
                                <div class="step-item {{ $currentStep >= 4 ? 'completed' : '' }} {{ $currentStep === 4 ? 'active' : '' }}">
                                    <div class="step-icon">
                                        <i class="fas fa-house-chimney-user"></i>
                                    </div>
                                    <div class="step-label">ডেলিভারি সম্পন্ন</div>
                                    <div class="step-desc text-muted">হাতে পেয়ে টাকা দিন</div>
                                </div>

                            </div>
                        </div>
                        @endif

                        <!-- Customer Delivery Information -->
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="small text-muted mb-1"><i class="fas fa-user text-secondary me-1"></i> গ্রাহকের নাম:</div>
                                    <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="small text-muted mb-1"><i class="fas fa-phone-alt text-secondary me-1"></i> মোবাইল নম্বর:</div>
                                    <div class="fw-bold text-dark tabular-nums">{{ $order->customer_phone }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> ডেলিভারির ঠিকানা:</div>
                                    <div class="text-dark small">{{ $order->shipping_address }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ordered Items Table -->
                        <div class="table-responsive rounded-3 border mb-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th class="ps-3">পণ্য</th>
                                        <th class="text-center">মূল্য</th>
                                        <th class="text-center">পরিমাণ</th>
                                        <th class="text-end pe-3">মোট</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->product && $item->product->primary_image_url)
                                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name }}" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold text-dark small" style="line-height: 1.3;">
                                                        {{ $item->product_name }}
                                                    </div>
                                                    @if(!empty($item->variant_text))
                                                    <span class="badge bg-light text-danger border small" style="font-size: 10px;">
                                                        {{ $item->variant_text }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center small tabular-nums">৳{{ number_format($item->unit_price, 0) }}</td>
                                        <td class="text-center small fw-bold">{{ $item->quantity }}</td>
                                        <td class="text-end pe-3 small fw-bold text-dark tabular-nums">৳{{ number_format($item->total_price, 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Financial Totals -->
                        <div class="row justify-content-end">
                            <div class="col-12 col-sm-7 col-md-6 col-lg-5">
                                <div class="p-3 bg-light rounded-3 border small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">পণ্যের মূল্য:</span>
                                        <span class="fw-semibold tabular-nums">৳{{ number_format($order->subtotal, 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">ডেলিভারি চার্জ:</span>
                                        <span class="fw-semibold tabular-nums">৳{{ number_format($order->shipping_charge, 0) }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-1 text-success">
                                        <span>ডিসকাউন্ট:</span>
                                        <span class="fw-semibold tabular-nums">-৳{{ number_format($order->discount_amount, 0) }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">পেমেন্ট মেথড:</span>
                                        <span class="fw-semibold text-capitalize">
                                            {{ $order->payment_method === 'cod' ? 'ক্যাশ অন ডেলিভারি' : ($order->payment_method === 'bkash' ? 'বিকাশ' : ($order->payment_method === 'nagad' ? 'নগদ' : $order->payment_method)) }}
                                        </span>
                                    </div>
                                    <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark fs-6">সর্বমোট বিল:</span>
                                        <span class="fw-bold text-danger fs-5 tabular-nums">৳{{ number_format($order->grand_total, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach

                @endif
            @endif

        </div>
    </div>
</div>

<style>
/* Modern Responsive Step Progress Tracker */
.order-stepper-wrap {
    overflow-x: auto;
    padding-bottom: 8px;
}
.order-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 520px;
    position: relative;
}
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    position: relative;
    z-index: 2;
}
.step-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 2px solid #cbd5e1;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}
.step-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 2px;
}
.step-desc {
    font-size: 11px;
}
.step-line {
    flex-grow: 1;
    height: 4px;
    background-color: #e2e8f0;
    margin: 0 -10px 28px -10px;
    z-index: 1;
    transition: background-color 0.3s ease;
}
.step-line.filled {
    background-color: #10b981;
}

/* Completed State */
.step-item.completed .step-icon {
    background-color: #10b981;
    color: #ffffff;
    border-color: #10b981;
}
.step-item.completed .step-label {
    color: #0f172a;
}

/* Active State */
.step-item.active .step-icon {
    background-color: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.2);
}
.step-item.active .step-label {
    color: #dc2626;
    font-weight: 700;
}
</style>
@endsection
