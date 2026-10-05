@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড - Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.55rem; letter-spacing: -0.3px;">
            Dashboard Overview
        </h3>
        @php
            $hour = now('Asia/Dhaka')->hour;
            $timeGreeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
            $adminName = auth()->user()->name ?? 'Admin';
        @endphp
        <p class="text-muted mb-0" style="font-size: 14.5px;">
            Hi <span class="fw-semibold text-dark">{{ $adminName }}</span>, Good {{ $timeGreeting }}!
        </p>
    </div>
    
    <!-- Time-Range Filter Pills (Today, 7 Days, 30 Days, All Time) -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="bg-white border rounded-pill p-1 shadow-sm d-inline-flex align-items-center">
            <span class="text-muted small px-2 d-none d-sm-inline fw-semibold">
                <i class="fas fa-filter text-primary me-1"></i> ফিল্টার:
            </span>
            <a href="{{ route('admin.dashboard', ['period' => 'today']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 transition-all {{ $period === 'today' ? 'filter-btn-active' : 'filter-btn-inactive' }}">
                Today
            </a>
            <a href="{{ route('admin.dashboard', ['period' => '7_days']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 transition-all {{ $period === '7_days' ? 'filter-btn-active' : 'filter-btn-inactive' }}">
                7 Days
            </a>
            <a href="{{ route('admin.dashboard', ['period' => '30_days']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 transition-all {{ $period === '30_days' ? 'filter-btn-active' : 'filter-btn-inactive' }}">
                30 Days
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'all']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 transition-all {{ $period === 'all' ? 'filter-btn-active' : 'filter-btn-inactive' }}">
                All Time
            </a>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary btn-sm px-3 fw-bold">
            <i class="fas fa-plus me-1"></i> নতুন পণ্য
        </a>
    </div>
</div>

<!-- 1. Key Metrics Cards (Modern & Lightweight) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Sales in Period -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #ecfdf5; color: #059669; border: 1px solid #d1fae5;">
                    <i class="fas fa-wallet"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">{{ $metrics['card1_title'] }}</span>
                    <div class="stat-card-value text-dark fw-bold my-1">৳{{ number_format($metrics['card1_value'], 0) }}</div>
                    <div class="stat-card-sub text-success">
                        <i class="fas fa-arrow-trend-up me-1"></i> {{ $metrics['card1_subtitle'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Today's Sales / Delivered -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe;">
                    <i class="far fa-calendar-check"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">{{ $metrics['card2_title'] }}</span>
                    <div class="stat-card-value text-dark fw-bold my-1">৳{{ number_format($metrics['card2_value'], 0) }}</div>
                    <div class="stat-card-sub text-muted">
                        <i class="far fa-clock me-1"></i> {{ $metrics['card2_subtitle'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Orders in Period & Pending -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">{{ $metrics['card3_title'] }}</span>
                    <div class="stat-card-value text-dark fw-bold my-1">
                        {{ $metrics['card3_orders'] }} <span class="fs-6 fw-normal text-muted">টি</span>
                    </div>
                    @if($metrics['card3_pending'] > 0)
                    <div class="stat-card-sub text-warning fw-semibold">
                        <i class="fas fa-hourglass-half me-1"></i> {{ $metrics['card3_pending'] }} টি পেন্ডিং আছে
                    </div>
                    @else
                    <div class="stat-card-sub text-muted">সব প্রক্রিয়াধীন</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Store Products & Low Stock -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6;">
                    <i class="fas fa-box-open"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">স্টোর পণ্য</span>
                    <div class="stat-card-value text-dark fw-bold my-1">
                        {{ $metrics['total_products'] }} <span class="fs-6 fw-normal text-muted">টি</span>
                    </div>
                    @if($metrics['low_stock_products'] > 0)
                    <div class="stat-card-sub text-danger fw-semibold">
                        <i class="fas fa-triangle-exclamation me-1"></i> {{ $metrics['low_stock_products'] }} টি পণ্যের স্টক কম
                    </div>
                    @else
                    <div class="stat-card-sub text-success">
                        <i class="fas fa-check me-1"></i> স্টক স্বাভাবিক
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1.5 Interactive Analytics & Performance Charts (Revenue Trend & Payment Channels) -->
<div class="row g-4 mb-4">
    <!-- Revenue & Order Volume Velocity Trend Chart -->
    <div class="col-12 col-xl-8">
        <div class="card admin-surface-card h-100 shadow-sm border">
            <div class="card-header bg-transparent border-bottom py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-line text-danger"></i>
                        <span>Revenue & Order Trend</span>
                    </h6>
                    <small class="text-muted">দৈনিক বিক্রয় আয় এবং অর্ডারের সংখ্যা বিশ্লেষণ</small>
                </div>
                <span class="badge bg-light text-dark border px-2.5 py-1 small fw-medium">
                    @if($period === 'today')
                        Today
                    @elseif($period === '7_days')
                        Last 7 Days
                    @else
                        Last 30 Days
                    @endif
                </span>
            </div>
            <div class="card-body px-3 px-sm-4 pb-3 pt-2">
                <div style="position: relative; height: 260px; width: 100%; max-width: 100%; overflow: hidden;">
                    <canvas id="dashboardRevenueChart" style="max-width: 100% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Channels & Delivery Zones Breakdown -->
    <div class="col-12 col-xl-4">
        <div class="card admin-surface-card h-100 shadow-sm border">
            <div class="card-header bg-transparent border-bottom py-3 px-3 px-sm-4">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-chart-pie text-primary"></i>
                    <span>পেমেন্ট চ্যানেল ও জোন বণ্টন</span>
                </h6>
                <small class="text-muted">গেটওয়ে এবং ডেলিভারি অঞ্চলভিত্তিক হিসাব</small>
            </div>
            <div class="card-body px-3 px-sm-4 pb-3 pt-1">
                <div style="position: relative; height: 160px; width: 100%; max-width: 100%; overflow: hidden;" class="my-2">
                    <canvas id="dashboardPaymentChart" style="max-width: 100% !important;"></canvas>
                </div>

                <div class="pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold" style="font-size: 13.5px; color: #475569; letter-spacing: 0.5px;">ডেলিভারি অঞ্চল (Zones)</span>
                    </div>
                    @forelse($zoneBreakdown as $zone)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <div>
                            <span class="zone-title text-capitalize">
                                {{ $zone->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভেতরে (Inside Dhaka)' : ($zone->delivery_zone === 'outside_dhaka' ? 'ঢাকার বাইরে (Outside Dhaka)' : ucfirst($zone->delivery_zone)) }}
                            </span>
                            <div class="zone-meta">{{ $zone->count }} টি অর্ডার (ডেলিভারি ফি: ৳{{ number_format($zone->shipping_total, 0) }})</div>
                        </div>
                        <span class="zone-val">৳{{ number_format($zone->total, 0) }}</span>
                    </div>
                    @empty
                    <div class="text-muted small py-2 text-center">কোনো জোন রেকর্ড পাওয়া যায়নি।</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- 2. Recent Orders Table (As Now) -->
    <div class="col-12 col-xl-8">
        <div class="card admin-surface-card">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-bolt text-danger me-2"></i> সাম্প্রতিক অর্ডারসমূহ
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                        <i class="fas fa-circle text-success me-1 animate-pulse" style="font-size: 8px;"></i> লাইভ
                    </span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold">সবগুলো দেখুন &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light border-bottom">
                            <tr style="font-size: 15px; color: #334155;">
                                <th class="ps-4 fw-bold">অর্ডার নম্বর</th>
                                <th class="fw-bold">গ্রাহক</th>
                                <th class="fw-bold">তারিখ</th>
                                <th class="fw-bold">বিল</th>
                                <th class="fw-bold">পেমেন্ট</th>
                                <th class="fw-bold">স্ট্যাটাস</th>
                                <th class="text-end pe-4 fw-bold">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <!-- Entire row is clickable to view popup modal -->
                            <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#dashboardOrderModal{{ $order->id }}" class="order-table-row">
                                <td class="ps-4">
                                    <span class="fw-bold text-danger font-monospace" style="font-size: 15.5px;">
                                        {{ $order->order_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 16px;">{{ $order->customer_name }}</div>
                                    <span class="text-secondary font-monospace" style="font-size: 14.5px;">{{ $order->customer_phone }}</span>
                                </td>
                                <td style="font-size: 15px; color: #475569;">{{ $order->created_at->format('d M, h:i A') }}</td>
                                <td class="fw-bold text-dark" style="font-size: 16.5px; font-family: 'Outfit', 'Bornomala', sans-serif;">৳{{ number_format($order->grand_total, 0) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-semibold">{{ strtoupper($order->payment_method) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }} fw-semibold">{{ $order->order_status }}</span>
                                </td>
                                <td class="text-end pe-4" onclick="event.stopPropagation();">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#dashboardOrderModal{{ $order->id }}" title="অর্ডারের বিবরণ দেখুন">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light border text-secondary" title="ইনভয়েস প্রিন্ট করুন">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">কোনো অর্ডার পাওয়া যায়নি।</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Low Stock Alert & Quick Shortcuts -->
    <div class="col-12 col-xl-4">
        <!-- Low Stock Alerts -->
        <div class="card admin-surface-card mb-4">
            <div class="card-header bg-transparent py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i> Low Stock Alerts
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-bottom-3">
                    @forelse($lowStockProducts as $lowProd)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ $lowProd->primary_image_url }}" alt="{{ $lowProd->name }}" class="rounded-2 me-2" style="width: 42px; height: 42px; object-fit: cover; border: 1px solid var(--admin-mint-border);">
                            <div class="text-truncate" style="max-width: 170px;">
                                <div class="fw-semibold small text-truncate text-dark">{{ $lowProd->name }}</div>
                                <small class="text-muted">SKU: {{ $lowProd->sku }}</small>
                            </div>
                        </div>
                        <span class="badge bg-danger">স্টক: {{ $lowProd->stock_quantity }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">সকল পণ্যের পর্যাপ্ত স্টক রয়েছে।</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Quick Store Info & Setting Card -->
        <div class="card admin-surface-card p-4" style="background: var(--admin-mint-bg);">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-store" style="color: var(--admin-primary);"></i> স্টোর সেটিংস সামারি
            </h5>
            <div class="d-flex flex-column gap-2 small">
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span class="text-muted">ওয়েবসাইটের নাম:</span>
                    <strong class="text-dark">{{ \App\Models\Setting::get('site_name', 'SIDQ MART') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span class="text-muted">হটলাইন:</span>
                    <strong class="text-dark">{{ \App\Models\Setting::get('contact_phone', 'N/A') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span class="text-muted">ঢাকার ভিতরে চার্জ:</span>
                    <strong class="text-dark">৳{{ \App\Models\Setting::get('delivery_inside_dhaka', 70) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span class="text-muted">ঢাকার বাইরে চার্জ:</span>
                    <strong class="text-dark">৳{{ \App\Models\Setting::get('delivery_outside_dhaka', 130) }}</strong>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-admin-primary btn-sm w-100 fw-bold py-2">
                        <i class="fas fa-sliders-h me-1"></i> সেটিংস ও থিম পরিবর্তন করুন
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===========================================
     DASHBOARD RECENT ORDERS POPUP MODALS
     (Rendered outside table to prevent browser DOM foster-parenting spill)
=========================================== -->
@foreach($recentOrders as $order)
@php
    $cleanPhone = preg_replace('/[^\d]/', '', $order->customer_phone);
    if (str_starts_with($cleanPhone, '01')) {
        $cleanPhone = '88' . $cleanPhone;
    }
    $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode("আসসালামু আলাইকুম {$order->customer_name}, SIDQ MART-এ আপনার অর্ডার (#{$order->order_number}) সংক্রান্ত তথ্যের জন্য যোগাযোগ করছি।");
@endphp

<div class="modal fade" id="dashboardOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="dashboardOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-3 border-0 shadow">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">Order #{{ $order->order_number }}</span>
                    <h6 class="modal-title fw-bold text-dark mb-0">অর্ডার বিবরণ ও ব্যবস্থাপনা</h6>
                    <span class="badge {{ $order->status_badge_class }} ms-1">{{ ucfirst($order->order_status) }}</span>
                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                    <span class="text-muted small ms-2">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="row g-3">
                    
                    <!-- Left: Customer Info Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle text-primary me-1"></i> কাস্টমার তথ্য
                            </h6>
                            <div class="d-flex flex-column gap-2 small">
                                <div><strong>নাম:</strong> <span class="text-dark">{{ $order->customer_name }}</span></div>
                                <div>
                                    <strong>ফোন:</strong> 
                                    @if($order->customer_phone)
                                        <a href="tel:{{ $order->customer_phone }}" class="font-monospace text-primary fw-bold text-decoration-none">
                                            {{ $order->customer_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">নেই</span>
                                    @endif
                                </div>
                                <div><strong>ঠিকানা:</strong> <span class="text-dark">{{ $order->shipping_address }}</span></div>
                                <div>
                                    <strong>ডেলিভারি এরিয়া:</strong> 
                                    <span class="badge {{ $order->delivery_zone === 'inside_dhaka' ? 'bg-info text-dark' : 'bg-primary' }}">
                                        {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}
                                    </span>
                                </div>
                                <div>
                                    <strong>পেমেন্ট মেথড:</strong> 
                                    <span class="badge bg-white text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                    @if($order->transaction_id)
                                    <span class="text-muted font-monospace ms-1">(TrxID: {{ $order->transaction_id }})</span>
                                    @endif
                                </div>
                                @if($order->customer_note)
                                <div><strong>গ্রাহকের নোট:</strong> <em class="text-dark">{{ $order->customer_note }}</em></div>
                                @endif
                                @if($order->ip_address)
                                <div class="mt-2 pt-2 border-top">
                                    <strong>আইপি অ্যাড্রেস:</strong> <span class="font-monospace text-muted">{{ $order->ip_address }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Direct Call & WhatsApp Action Buttons -->
                            @if(!empty($order->customer_phone))
                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                <a href="tel:{{ $order->customer_phone }}" class="btn btn-sm btn-success flex-grow-1 fw-bold">
                                    <i class="fas fa-phone-alt me-1"></i> সরাসরি কল
                                </a>
                                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm text-white flex-grow-1 fw-bold" style="background: #25D366;">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Ordered Items Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-shopping-cart text-danger me-1"></i> অর্ডারকৃত পণ্যসমূহ ({{ count($order->items) }} টি)
                            </h6>
                            <div class="d-flex flex-column gap-2 mb-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($order->items as $item)
                                @php
                                    $productUrl = ($item->product && $item->product->slug) ? route('product.detail', $item->product->slug) : null;
                                    $imgUrl = $item->product_image ?: ($item->product ? $item->product->primary_image_url : null);
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-2 border gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 0;">
                                        @if(!empty($imgUrl))
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" target="_blank" title="লাইভ প্রোডাক্ট পেজ খুলুন" class="flex-shrink-0">
                                                    <img src="{{ $imgUrl }}" alt="" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                                </a>
                                            @else
                                                <img src="{{ $imgUrl }}" alt="" class="rounded border flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                                            @endif
                                        @endif
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" target="_blank" class="fw-semibold text-dark text-decoration-none d-inline-flex align-items-center gap-1 hover-text-primary" style="font-size: 12px; line-height: 1.35;" title="লাইভ প্রোডাক্ট পেজ দেখুন">
                                                    <span>{{ $item->product_name }}</span>
                                                    <i class="fas fa-external-link-alt text-primary flex-shrink-0 ms-1" style="font-size: 10px;"></i>
                                                </a>
                                            @else
                                                <div class="fw-semibold text-dark" style="font-size: 12px; line-height: 1.35;">{{ $item->product_name }}</div>
                                            @endif
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                <span>৳{{ number_format($item->unit_price, 0) }} × {{ $item->quantity }}</span>
                                                @if($item->product && $item->product->sku)
                                                <span class="font-monospace text-secondary ms-1">({{ $item->product->sku }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ps-2">
                                        <span class="fw-bold text-dark small d-block">৳{{ number_format($item->total_price, 0) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Financial Breakdown -->
                            <div class="border-top pt-2 small">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>সাব-টোটাল:</span>
                                    <span>৳{{ number_format($order->subtotal, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span>৳{{ number_format($order->shipping_charge, 0) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between text-success mb-1">
                                    <span>কুপন ডিসকাউন্ট:</span>
                                    <span>-৳{{ number_format($order->discount_amount, 0) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6 mt-1 border-top pt-1">
                                    <span>সর্বমোট বিল:</span>
                                    <span class="text-danger">৳{{ number_format($order->grand_total, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Quick Status Manager Form -->
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-clipboard-check text-secondary me-1"></i> অর্ডার স্ট্যাটাস ও অভ্যন্তরীণ নোট
                            </h6>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label small fw-semibold text-dark mb-1">অর্ডার স্ট্যাটাস:</label>
                                        <select name="order_status" class="form-select form-select-sm">
                                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending (পেন্ডিং)</option>
                                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing (প্রসেসিং)</option>
                                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped (কুরিয়ারে আছে)</option>
                                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered (ডেলিভার্ড)</option>
                                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled (বাতিল)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">অ্যাডমিন নোট:</label>
                                        <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="প্রয়োজনে অভ্যন্তরীণ নোট লিখুন..." value="{{ $order->admin_notes }}">
                                    </div>
                                    <div class="col-12 col-sm-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">আপডেট</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-dark fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                        <i class="fas fa-print me-1"></i>
                        <span>ইনভয়েস প্রিন্ট করুন</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endforeach

<style>
.order-table-row:hover {
    background-color: #f8fafc !important;
}
@keyframes livePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.15); }
}
.animate-pulse {
    display: inline-block;
    animation: livePulse 1.8s infinite ease-in-out;
}
.filter-btn-active {
    background-color: var(--admin-primary, #dc2626) !important;
    color: #fff !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 5px rgba(220, 38, 38, 0.25) !important;
}
.filter-btn-inactive {
    background-color: transparent !important;
    color: #64748b !important;
    font-weight: 600 !important;
    border: none !important;
}
.filter-btn-inactive:hover {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
}
.hover-text-primary:hover {
    color: var(--admin-primary, #dc2626) !important;
}
.stat-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #334155 !important;
    font-family: 'Bornomala', sans-serif;
    letter-spacing: -0.1px;
}
.stat-card-value {
    font-size: 1.95rem;
    font-weight: 800;
    line-height: 1.25;
    letter-spacing: -0.01em;
    font-family: 'Outfit', 'Bornomala', sans-serif;
    color: #090d16 !important;
}
.stat-card-sub {
    font-size: 14px;
    font-weight: 600;
    font-family: 'Outfit', 'Bornomala', sans-serif;
    display: inline-flex;
    align-items: center;
}
.stat-badge-icon {
    width: 46px;
    height: 46px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    flex-shrink: 0;
}
.dashboard-subtitle {
    font-size: 15px;
    color: #475569 !important;
}
.zone-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b !important;
}
.zone-meta {
    font-size: 14px;
    color: #475569 !important;
}
.zone-val {
    font-size: 16.5px;
    font-weight: 700;
    font-family: 'Outfit', 'Bornomala', sans-serif;
    color: #090d16 !important;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Outfit', 'Bornomala', sans-serif";
        Chart.defaults.color = '#334155';
    }
    // 1. Revenue & Orders Trend Chart
    const trendData = @json(array_values($daysTrend));
    const labels = trendData.map(d => d.label);
    const revenues = trendData.map(d => d.revenue);
    const orderCounts = trendData.map(d => d.orders);

    const trendCanvas = document.getElementById('dashboardRevenueChart');
    if (trendCanvas) {
        const trendCtx = trendCanvas.getContext('2d');
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--admin-primary').trim() || '#f13124';

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'বিক্রয় আয় (৳)',
                        data: revenues,
                        borderColor: primaryColor,
                        backgroundColor: 'rgba(241, 49, 36, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: labels.length > 15 ? 2 : 4,
                        pointHoverRadius: 6,
                        borderWidth: 2.5,
                        yAxisID: 'y'
                    },
                    {
                        label: 'অর্ডারের সংখ্যা',
                        data: orderCounts,
                        borderColor: '#0f172a',
                        backgroundColor: 'transparent',
                        borderWidth: 1.8,
                        borderDash: [3, 3],
                        pointRadius: labels.length > 15 ? 1.5 : 3,
                        pointHoverRadius: 5,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 14,
                            color: '#1e293b',
                            font: { family: "'Outfit', 'Bornomala', sans-serif", size: 12.5, weight: '600' }
                        }
                    },
                    tooltip: {
                        titleFont: { family: "'Outfit', 'Bornomala', sans-serif", size: 13 },
                        bodyFont: { family: "'Outfit', 'Bornomala', sans-serif", size: 12.5 },
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return ' বিক্রয়: ৳' + Number(context.parsed.y).toLocaleString();
                                }
                                return ' অর্ডার: ' + context.parsed.y + ' টি';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#475569', maxRotation: 45, minRotation: 0, font: { family: "'Outfit', 'Bornomala', sans-serif", size: 11, weight: '500' } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(226, 232, 240, 0.7)' },
                        ticks: {
                            color: '#475569',
                            callback: function(value) {
                                return '৳' + Number(value).toLocaleString();
                            },
                            font: { family: "'Outfit', 'Bornomala', sans-serif", size: 11, weight: '500' }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: '#475569', stepSize: 1, font: { family: "'Outfit', 'Bornomala', sans-serif", size: 11, weight: '500' } }
                    }
                }
            }
        });
    }

    // 2. Payment Method Distribution Chart
    const paymentCanvas = document.getElementById('dashboardPaymentChart');
    if (paymentCanvas) {
        const paymentBreakdown = @json($paymentMethodsBreakdown);
        const pLabels = paymentBreakdown.map(p => {
            if (p.payment_method === 'cod') return 'COD';
            if (p.payment_method === 'bkash') return 'bKash';
            if (p.payment_method === 'nagad') return 'Nagad';
            if (p.payment_method === 'rocket') return 'Rocket';
            return p.payment_method ? p.payment_method.toUpperCase() : 'Unknown';
        });
        const pTotals = paymentBreakdown.map(p => Number(p.total));

        const paymentCtx = paymentCanvas.getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: pLabels.length > 0 ? pLabels : ['No Orders Yet'],
                datasets: [{
                    data: pTotals.length > 0 ? pTotals : [1],
                    backgroundColor: [
                        '#0f172a',
                        '#e2136e',
                        '#f7941d',
                        '#8b5cf6',
                        '#3b82f6',
                        '#94a3b8'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            color: '#1e293b',
                            font: { size: 12, family: "'Outfit', 'Bornomala', sans-serif", weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (pTotals.length === 0) return ' কোনো অর্ডার নেই';
                                return ' ' + context.label + ': ৳' + Number(context.parsed).toLocaleString();
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush
