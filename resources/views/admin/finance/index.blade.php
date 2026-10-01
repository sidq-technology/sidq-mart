@extends('layouts.admin')

@section('title', 'Financial Analytics & Revenue')

@section('content')
    <!-- Header Section (Fully Responsive) -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-white text-dark border px-2 py-1 small fw-semibold">
                    <i class="fas fa-coins text-warning me-1"></i> Cash Flow &amp; Earnings
                </span>
                <span class="text-muted small">Realtime Ledger</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Finance &amp; Revenue</h1>
            <p class="text-muted small mb-0">Overview of sales velocity, collected cash, transit receivables, and payments.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            <!-- Period Filter Pills -->
            <div class="d-inline-flex flex-wrap p-1 bg-white border rounded-3 shadow-xs gap-1">
                @php
                    $periods = [
                        'today' => 'Today',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'this_year' => 'This Year',
                        'all' => 'All Time',
                    ];
                @endphp
                @foreach($periods as $key => $label)
                <a href="{{ route('admin.finance.index', ['range' => $key]) }}" 
                   class="btn btn-sm py-1 px-2 px-sm-3 {{ $range === $key ? 'btn-dark fw-bold text-white' : 'btn-light border-0 text-dark fw-medium' }}"
                   style="font-size: 12.5px; border-radius: 6px;">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            <!-- Print Button -->
            <button onclick="window.print()" class="btn btn-sm btn-white bg-white border text-dark fw-semibold px-3 py-1 shadow-xs rounded-3 d-inline-flex align-items-center gap-1">
                <i class="fas fa-print text-secondary"></i>
                <span class="d-none d-sm-inline">Print</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Responsive Grid) -->
    <div class="card border bg-white shadow-xs rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.finance.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="range" value="custom">
                
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm border" value="{{ request('start_date', $startDate ? $startDate->format('Y-m-d') : '') }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm border" value="{{ request('end_date', $endDate ? $endDate->format('Y-m-d') : '') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Gateway</label>
                    <select name="payment_method" class="form-select form-select-sm border">
                        <option value="">All Gateways</option>
                        <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="rocket" {{ request('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Payment Status</label>
                    <select name="payment_status" class="form-select form-select-sm border">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid / Settled</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending / Unpaid</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-sm btn-light border px-2" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Clean Style KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Gross Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Gross Revenue</span>
                        <div class="text-muted">
                            <i class="fas fa-chart-line fs-6"></i>
                        </div>
                    </div>
                    <div class="fs-4 fs-sm-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">
                        ৳{{ number_format($grossRevenue, 2) }}
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px;">
                        <span>All orders (excl. cancelled)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Realized Cash (Paid) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Realized Cash</span>
                        <div class="text-success">
                            <i class="fas fa-check-circle fs-6"></i>
                        </div>
                    </div>
                    <div class="fs-4 fs-sm-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">
                        ৳{{ number_format($paidRevenue, 2) }}
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px;">
                        <span class="text-success fw-medium">Settled &amp; Paid In Full</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Pending Receivables -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Transit Receivables</span>
                        <div class="text-warning">
                            <i class="fas fa-clock fs-6"></i>
                        </div>
                    </div>
                    <div class="fs-4 fs-sm-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">
                        ৳{{ number_format($pendingReceivables, 2) }}
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px;">
                        <span class="text-warning fw-medium">COD Collection In Transit</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Delivered Sales -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Delivered Sales</span>
                        <div class="text-primary">
                            <i class="fas fa-box-check fs-6"></i>
                        </div>
                    </div>
                    <div class="fs-4 fs-sm-3 fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">
                        ৳{{ number_format($netDeliveredRevenue, 2) }}
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px;">
                        <span>Delivered to customer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Clean Metrics Strip -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white border rounded-3 shadow-xs d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small" style="font-size: 11.5px;">Shipping Collected</div>
                    <div class="fw-bold fs-5 text-dark" style="font-family: 'Outfit', sans-serif;">৳{{ number_format($totalShippingCollected, 2) }}</div>
                </div>
                <div class="text-muted opacity-50"><i class="fas fa-shipping-fast fs-5"></i></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 bg-white border rounded-3 shadow-xs d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small" style="font-size: 11.5px;">Total Discounts</div>
                    <div class="fw-bold fs-5 text-dark" style="font-family: 'Outfit', sans-serif;">৳{{ number_format($totalDiscountsGiven, 2) }}</div>
                </div>
                <div class="text-muted opacity-50"><i class="fas fa-ticket-alt fs-5"></i></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 bg-white border rounded-3 shadow-xs d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small" style="font-size: 11.5px;">Average Order (AOV)</div>
                    <div class="fw-bold fs-5 text-dark" style="font-family: 'Outfit', sans-serif;">৳{{ number_format($averageOrderValue, 2) }}</div>
                </div>
                <div class="text-muted opacity-50"><i class="fas fa-divide fs-5"></i></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="p-3 bg-white border rounded-3 shadow-xs d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small" style="font-size: 11.5px;">Total Orders Placed</div>
                    <div class="fw-bold fs-5 text-dark" style="font-family: 'Outfit', sans-serif;">{{ $totalOrdersCount }} <span class="small fw-normal text-muted" style="font-size: 12px;">orders</span></div>
                </div>
                <div class="text-muted opacity-50"><i class="fas fa-shopping-bag fs-5"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics (Clean Minimalist Style) -->
    <div class="row g-4 mb-4">
        <!-- Revenue Velocity Chart -->
        <div class="col-12 col-xl-8">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-3 px-sm-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">30-Day Revenue Trend</h6>
                        <span class="text-muted small">Daily sales trajectory and order count</span>
                    </div>
                    <span class="badge bg-light text-dark border px-2 py-1 small fw-medium">
                        Last 30 Days
                    </span>
                </div>
                <div class="card-body px-3 px-sm-4 pb-3 pt-2">
                    <div style="position: relative; height: 260px; width: 100%; max-width: 100%; overflow: hidden;">
                        <canvas id="revenueTrendChart" style="max-width: 100% !important;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown by Payment Gateway & Logistics -->
        <div class="col-12 col-xl-4">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-3 px-sm-4 pb-1">
                    <h6 class="fw-bold text-dark mb-0">Payment Channels &amp; Zones</h6>
                    <span class="text-muted small">Distribution by payment type &amp; region</span>
                </div>
                <div class="card-body px-3 px-sm-4 pb-3 pt-0">
                    <div style="position: relative; height: 160px; width: 100%; max-width: 100%; overflow: hidden;" class="my-2">
                        <canvas id="paymentMethodChart" style="max-width: 100% !important;"></canvas>
                    </div>

                    <div class="pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Delivery Zones</span>
                        </div>
                        @forelse($zoneBreakdown as $zone)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                            <div>
                                <span class="fw-semibold text-dark text-capitalize small">
                                    {{ $zone->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : ($zone->delivery_zone === 'outside_dhaka' ? 'Outside Dhaka' : ucfirst($zone->delivery_zone)) }}
                                </span>
                                <div class="text-muted" style="font-size: 11px;">{{ $zone->count }} orders (Shipping: ৳{{ number_format($zone->shipping_total, 0) }})</div>
                            </div>
                            <span class="fw-bold text-dark small">৳{{ number_format($zone->total, 2) }}</span>
                        </div>
                        @empty
                        <div class="text-muted small py-2 text-center">No zone records available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clean Transaction Ledger Table (Fully Responsive with smooth horizontal scroll) -->
    <div class="card border bg-white rounded-3 shadow-xs mb-4">
        <div class="card-header bg-transparent border-bottom pt-3 px-3 px-sm-4 pb-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Financial Transaction Ledger</h6>
                <span class="text-muted small">Real-time invoice records, settlement, and customer breakdown</span>
            </div>
            <div class="text-muted small">
                Showing <strong class="text-dark">{{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $transactions->total() }}</strong> records
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr class="text-muted small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <th class="ps-3 ps-sm-4 text-nowrap py-3">Order #</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Gateway</th>
                            <th class="py-3">Zone</th>
                            <th class="py-3">Shipping</th>
                            <th class="py-3">Discount</th>
                            <th class="py-3">Net Total</th>
                            <th class="py-3">Payment</th>
                            <th class="py-3">Status</th>
                            <th class="text-end pe-3 pe-sm-4 text-nowrap py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $order)
                        <tr>
                            <td class="ps-3 ps-sm-4 text-nowrap">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
                                <div class="text-muted" style="font-size: 11.5px;">{{ $order->customer_phone }}</div>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $order->created_at->format('d M, Y') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($order->payment_method === 'bkash')
                                    <span class="badge rounded-pill" style="background: rgba(226, 19, 110, 0.12); color: #e2136e; font-weight: 600;">
                                        bKash
                                    </span>
                                @elseif($order->payment_method === 'nagad')
                                    <span class="badge rounded-pill" style="background: rgba(247, 148, 29, 0.12); color: #f7941d; font-weight: 600;">
                                        Nagad
                                    </span>
                                @elseif($order->payment_method === 'cod')
                                    <span class="badge rounded-pill bg-light text-dark border fw-medium">
                                        COD
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                @endif
                                
                                @if($order->transaction_id)
                                    <div class="text-muted" style="font-size: 10.5px;">Trx: <code>{{ $order->transaction_id }}</code></div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border small">
                                    {{ $order->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : ($order->delivery_zone === 'outside_dhaka' ? 'Outside Dhaka' : ucfirst($order->delivery_zone)) }}
                                </span>
                            </td>
                            <td class="text-muted small">৳{{ number_format($order->shipping_charge, 2) }}</td>
                            <td class="text-danger small">
                                @if($order->discount_amount > 0)
                                    -৳{{ number_format($order->discount_amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6" style="font-family: 'Outfit', sans-serif;">৳{{ number_format($order->grand_total, 2) }}</span>
                            </td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">
                                        <i class="fas fa-check me-1"></i> Paid
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold px-2 py-1">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }} rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3 pe-sm-4 text-nowrap">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border px-2 py-1" title="View Order">
                                    <i class="fas fa-eye text-dark"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1 ms-1" title="Invoice">
                                    <i class="fas fa-file-invoice text-secondary"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-2x text-muted mb-2 opacity-50"></i>
                                <h6 class="text-dark fw-bold">No financial transactions found</h6>
                                <p class="small text-muted mb-0">Try clearing your filters or selecting a broader time period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="px-3 px-sm-4 py-3 border-top d-flex justify-content-between align-items-center">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Revenue Velocity Line Chart
    const trendData = @json(array_values($daysTrend));
    const labels = trendData.map(item => item.label);
    const revenues = trendData.map(item => item.revenue);
    const orderCounts = trendData.map(item => item.orders);

    const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(15, 23, 42, 0.08)');
    gradient.addColorStop(1, 'rgba(15, 23, 42, 0.00)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Gross Sales (৳)',
                    data: revenues,
                    borderColor: '#0f172a',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#0f172a',
                    yAxisID: 'y'
                },
                {
                    label: 'Order Volume',
                    data: orderCounts,
                    borderColor: 'rgba(100, 116, 139, 0.6)',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    borderDash: [3, 3],
                    pointRadius: 1.5,
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
                        boxWidth: 10,
                        font: { family: "'Outfit', sans-serif", size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return ' Gross Sales: ৳' + Number(context.parsed.y).toLocaleString();
                            }
                            return ' Orders: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, minRotation: 0, font: { family: "'Outfit', sans-serif", size: 10 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(226, 232, 240, 0.5)' },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value;
                        },
                        font: { family: "'Outfit', sans-serif", size: 10 }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { stepSize: 1, font: { family: "'Outfit', sans-serif", size: 10 } }
                }
            }
        }
    });

    // 2. Payment Method Distribution Doughnut Chart
    const paymentBreakdown = @json($paymentMethodsBreakdown);
    const pLabels = paymentBreakdown.map(p => {
        if (p.payment_method === 'cod') return 'COD';
        if (p.payment_method === 'bkash') return 'bKash';
        if (p.payment_method === 'nagad') return 'Nagad';
        return p.payment_method.toUpperCase();
    });
    const pTotals = paymentBreakdown.map(p => p.total);

    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: pLabels.length > 0 ? pLabels : ['No Data'],
            datasets: [{
                data: pTotals.length > 0 ? pTotals : [1],
                backgroundColor: [
                    '#0f172a',
                    '#e2136e',
                    '#f7941d',
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
                        boxWidth: 8,
                        font: { size: 10.5, family: "'Outfit', sans-serif" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ৳' + Number(context.parsed).toLocaleString();
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
