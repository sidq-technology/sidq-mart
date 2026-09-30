@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড - Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-chart-line me-2" style="color: var(--admin-primary);"></i> ড্যাশবোর্ড (Dashboard Overview)
        </h3>
        <p class="text-muted small mb-0">আপনার ই-কমার্স স্টোরের সার্বিক রিপোর্ট, বিক্রয় তথ্য এবং সাম্প্রতিক কার্যক্রম।</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary btn-sm px-3 fw-bold">
            <i class="fas fa-plus me-1"></i> নতুন পণ্য যোগ করুন
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-shopping-cart me-1"></i> সকল অর্ডার
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-users me-1"></i> ইউজারসমূহ
        </a>
    </div>
</div>

<!-- 1. Key Metrics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">মোট বিক্রয় (Total Sales)</span>
                <h4 class="fw-bold mb-0 mt-1 text-dark">৳{{ number_format($metrics['total_sales'], 0) }}</h4>
                <small class="text-success"><i class="fas fa-arrow-up me-1"></i> সর্বমোট আয়</small>
            </div>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <!-- Today's Sales -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">আজকের বিক্রয় (Today)</span>
                <h4 class="fw-bold mb-0 mt-1 text-dark">৳{{ number_format($metrics['today_sales'], 0) }}</h4>
                <small class="text-muted">আজকের দিনের মোট আয়</small>
            </div>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>

    <!-- Total & Pending Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">মোট অর্ডার (Orders)</span>
                <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $metrics['total_orders'] }} টি</h4>
                <small class="text-warning fw-bold">{{ $metrics['pending_orders'] }} টি পেন্ডিং আছে</small>
            </div>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
    </div>

    <!-- Products & Low Stock -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">স্টোর পণ্য (Products)</span>
                <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $metrics['total_products'] }} টি</h4>
                @if($metrics['low_stock_products'] > 0)
                <small class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i> {{ $metrics['low_stock_products'] }} টি পণ্যের স্টক কম</small>
                @else
                <small class="text-success"><i class="fas fa-check me-1"></i> স্টক স্বাভাবিক</small>
                @endif
            </div>
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- 2. Recent Orders Table -->
    <div class="col-12 col-xl-8">
        <div class="card admin-surface-card">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-clock me-2" style="color: var(--admin-primary);"></i> সাম্প্রতিক অর্ডারসমূহ (Recent Orders)
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold">সবগুলো দেখুন &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead>
                            <tr>
                                <th class="ps-4">অর্ডার নম্বর</th>
                                <th>গ্রাহক</th>
                                <th>তারিখ</th>
                                <th>বিল</th>
                                <th>পেমেন্ট</th>
                                <th>স্ট্যাটাস</th>
                                <th class="text-end pe-4">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none" style="color: var(--admin-primary);">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_phone }}</small>
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('d M, h:i A') }}</td>
                                <td class="fw-bold text-dark">৳{{ number_format($order->grand_total, 0) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }}">{{ $order->order_status }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border text-primary" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light border text-secondary" title="Print Invoice">
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
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i> স্টক সতর্কতা (Low Stock Alerts)
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
@endsection
