@extends('layouts.admin')

@section('title', 'Financial Analytics & Revenue')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section with Date Range Filter -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #047857; font-size: 13px; font-weight: 600;">
                    <i class="fas fa-wallet me-1"></i> Financial Intelligence
                </span>
                <span class="text-muted small">Updated Realtime</span>
            </div>
            <h1 class="h3 fw-bold text-dark mt-1 mb-0">Finance & Revenue Analytics</h1>
            <p class="text-muted small mb-0">Comprehensive gross earnings, payment distribution, delivery charges, and cash ledger.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Filter Pills -->
            <div class="btn-group shadow-sm bg-white p-1 rounded-3 border" role="group">
                <a href="{{ route('admin.finance.index', ['range' => 'today']) }}" class="btn btn-sm {{ $range === 'today' ? 'btn-primary' : 'btn-light border-0' }}" style="{{ $range === 'today' ? 'background: var(--admin-primary); border-color: var(--admin-primary);' : '' }}">Today</a>
                <a href="{{ route('admin.finance.index', ['range' => 'this_week']) }}" class="btn btn-sm {{ $range === 'this_week' ? 'btn-primary' : 'btn-light border-0' }}" style="{{ $range === 'this_week' ? 'background: var(--admin-primary); border-color: var(--admin-primary);' : '' }}">Week</a>
                <a href="{{ route('admin.finance.index', ['range' => 'this_month']) }}" class="btn btn-sm {{ $range === 'this_month' ? 'btn-primary' : 'btn-light border-0' }}" style="{{ $range === 'this_month' ? 'background: var(--admin-primary); border-color: var(--admin-primary);' : '' }}">Month</a>
                <a href="{{ route('admin.finance.index', ['range' => 'this_year']) }}" class="btn btn-sm {{ $range === 'this_year' ? 'btn-primary' : 'btn-light border-0' }}" style="{{ $range === 'this_year' ? 'background: var(--admin-primary); border-color: var(--admin-primary);' : '' }}">Year</a>
                <a href="{{ route('admin.finance.index', ['range' => 'all']) }}" class="btn btn-sm {{ $range === 'all' ? 'btn-primary' : 'btn-light border-0' }}" style="{{ $range === 'all' ? 'background: var(--admin-primary); border-color: var(--admin-primary);' : '' }}">All Time</a>
            </div>

            <!-- Print Ledger Button -->
            <button onclick="window.print()" class="btn btn-sm btn-white bg-white border shadow-sm text-dark d-inline-flex align-items-center gap-1">
                <i class="fas fa-print text-secondary"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Date Range Custom Filter Bar (if custom or expanded) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.finance.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="range" value="custom">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="far fa-calendar me-1"></i> From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $startDate ? $startDate->format('Y-m-d') : '') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="far fa-calendar me-1"></i> To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $endDate ? $endDate->format('Y-m-d') : '') }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">All Methods</option>
                        <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                        <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="rocket" {{ request('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Payment Status</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending / Unpaid</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end gap-2 pt-2 pt-md-0">
                    <button type="submit" class="btn btn-sm btn-primary w-100" style="background: var(--admin-primary); border-color: var(--admin-primary);">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-sm btn-light border" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="row g-3 mb-4">
        <!-- Gross Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Gross Sales</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.12); color: #059669;">
                            <i class="fas fa-chart-line fs-5"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">৳{{ number_format($grossRevenue, 2) }}</div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <span class="badge bg-light text-success fw-normal border">Total Value</span>
                        <span>Excl. cancelled orders</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid / Collected Cash -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-left: 4px solid #3b82f6 !important;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Realized Cash (Paid)</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                            <i class="fas fa-check-double fs-5"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">৳{{ number_format($paidRevenue, 2) }}</div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <span class="badge bg-light text-primary fw-normal border">Settled</span>
                        <span>Cleared transactions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Receivables -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Pending Receivables</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                            <i class="fas fa-clock fs-5"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">৳{{ number_format($pendingReceivables, 2) }}</div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <span class="badge bg-light text-warning fw-normal border text-dark">COD Transit</span>
                        <span>Pending collection</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivered Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-left: 4px solid var(--admin-primary) !important;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Delivered Revenue</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(241, 49, 36, 0.12); color: var(--admin-primary);">
                            <i class="fas fa-box-open fs-5"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bold text-dark mb-1">৳{{ number_format($netDeliveredRevenue, 2) }}</div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <span class="badge bg-light text-danger fw-normal border">Completed</span>
                        <span>Successfully handed over</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metric Strip -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-light text-primary">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Shipping Collected</div>
                    <div class="fw-bold fs-5 text-dark">৳{{ number_format($totalShippingCollected, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-light text-danger">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Coupons / Discounts</div>
                    <div class="fw-bold fs-5 text-dark">৳{{ number_format($totalDiscountsGiven, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-light text-success">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Avg Order Value (AOV)</div>
                    <div class="fw-bold fs-5 text-dark">৳{{ number_format($averageOrderValue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-light text-secondary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Total Orders Placed</div>
                    <div class="fw-bold fs-5 text-dark">{{ $totalOrdersCount }} <span class="small fw-normal text-muted">Orders</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Row -->
    <div class="row g-4 mb-4">
        <!-- 30-Day Revenue Trend Line Chart -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">30-Day Revenue Velocity</h5>
                        <p class="text-muted small mb-0">Daily gross revenue curve and transaction volume.</p>
                    </div>
                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                        <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> Live Trajectory
                    </span>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div style="height: 300px; position: relative;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown by Payment Method & Zone -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">Payment Channels & Logistics</h5>
                    <p class="text-muted small mb-0">Settlement method and geographical splits.</p>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div style="height: 180px; position: relative;" class="mb-3">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>

                    <div class="pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Delivery Zone Breakdown</span>
                        </div>
                        @forelse($zoneBreakdown as $zone)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                            <div>
                                <span class="fw-semibold text-dark text-capitalize small">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
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

    <!-- Detailed Financial Transaction Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Financial Transaction Ledger</h5>
                <p class="text-muted small mb-0">Detailed breakdown of invoices, payment gateways, and settlement status.</p>
            </div>
            <div class="text-muted small">
                Showing <strong class="text-dark">{{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $transactions->total() }}</strong> transactions
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Payment Gateway</th>
                            <th>Delivery Zone</th>
                            <th>Shipping</th>
                            <th>Discount</th>
                            <th>Net Total</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $order)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
                                <div class="text-muted" style="font-size: 12px;"><i class="fas fa-phone-alt me-1"></i>{{ $order->customer_phone }}</div>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $order->created_at->format('d M, Y') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($order->payment_method === 'bkash')
                                    <span class="badge rounded-pill" style="background: rgba(226, 19, 110, 0.12); color: #e2136e; font-weight: 600;">
                                        <i class="fas fa-mobile-alt me-1"></i> bKash
                                    </span>
                                @elseif($order->payment_method === 'nagad')
                                    <span class="badge rounded-pill" style="background: rgba(247, 148, 29, 0.12); color: #f7941d; font-weight: 600;">
                                        <i class="fas fa-mobile-alt me-1"></i> Nagad
                                    </span>
                                @elseif($order->payment_method === 'cod')
                                    <span class="badge rounded-pill bg-light text-dark border fw-medium">
                                        <i class="fas fa-hand-holding-usd me-1 text-success"></i> Cash on Delivery
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                @endif
                                
                                @if($order->transaction_id)
                                    <div class="text-muted" style="font-size: 11px;">Trx: <code>{{ $order->transaction_id }}</code></div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border small">
                                    {{ $order->delivery_zone === 'inside_dhaka' ? 'Inside Dhaka' : ($order->delivery_zone === 'outside_dhaka' ? 'Outside Dhaka' : ucfirst($order->delivery_zone)) }}
                                </span>
                            </td>
                            <td class="text-muted">৳{{ number_format($order->shipping_charge, 2) }}</td>
                            <td class="text-danger">
                                @if($order->discount_amount > 0)
                                    -৳{{ number_format($order->discount_amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6">৳{{ number_format($order->grand_total, 2) }}</span>
                            </td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.15); color: #047857; font-weight: 600;">
                                        <i class="fas fa-check-circle me-1"></i> Paid
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background: rgba(245, 158, 11, 0.15); color: #b45309; font-weight: 600;">
                                        <i class="fas fa-hourglass-half me-1"></i> Unpaid
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }} rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border px-2 py-1" title="View Order Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1 ms-1" title="Print Invoice">
                                    <i class="fas fa-file-invoice text-secondary"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x text-muted mb-3 opacity-25"></i>
                                <h5>No financial transactions found</h5>
                                <p class="small mb-0">Try changing your date filters or search parameters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

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
    
    // Gradient fill for chart
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Gross Sales (৳)',
                    data: revenues,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981',
                    yAxisID: 'y'
                },
                {
                    label: 'Order Volume',
                    data: orderCounts,
                    borderColor: 'rgba(99, 102, 241, 0.7)',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    borderDash: [4, 4],
                    pointRadius: 2,
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
                        boxWidth: 12,
                        font: { family: 'Rubik, sans-serif', size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return ' Revenue: ৳' + Number(context.parsed.y).toLocaleString();
                            }
                            return ' Orders: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Payment Method Distribution Doughnut Chart
    const paymentBreakdown = @json($paymentMethodsBreakdown);
    const pLabels = paymentBreakdown.map(p => {
        if (p.payment_method === 'cod') return 'Cash on Delivery';
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
                    '#10b981',
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
                        boxWidth: 10,
                        font: { size: 11, family: 'Rubik, sans-serif' }
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
            cutout: '65%'
        }
    });
});
</script>
@endpush
