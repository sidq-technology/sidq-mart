@extends('layouts.admin')

@section('title', 'SMS ও মার্কেটিং অটোমেশন - SMS & Marketing')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.55rem; letter-spacing: -0.3px;">
            SMS & মার্কেটিং অটোমেশন
        </h3>
        <p class="text-muted mb-0" style="font-size: 14.5px;">
            MRAM টেকনোলজিস SMS গেটওয়ে, ট্রানজেকশনাল মেসেজ হিস্ট্রি এবং লাইভ ব্যালেন্স ট্র্যাকিং।
        </p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('admin.settings.index') }}#secSms" class="btn btn-outline-dark btn-sm px-3 fw-bold rounded-3">
            <i class="fas fa-cog me-1"></i> SMS API সেটিংস
        </a>
        <button type="button" class="btn btn-admin-primary btn-sm px-3 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#testSmsModal">
            <i class="fas fa-paper-plane me-1"></i> টেস্ট SMS পাঠান
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-xs mb-4" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-xs mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- 1. SMS Key Metrics & Live Balance -->
<div class="row g-3 mb-4">
    <!-- Live Balance Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #ecfdf5; color: #059669; border: 1px solid #d1fae5;">
                    <i class="fas fa-coins"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="stat-card-title">লাইভ SMS ব্যালেন্স</span>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted" id="btnRefreshBalance" title="ব্যালেন্স রিফ্রেশ করুন">
                            <i class="fas fa-sync-alt" id="balanceSpinner"></i>
                        </button>
                    </div>
                    <div class="stat-card-value text-dark fw-bold my-1" id="liveBalanceDisplay">
                        {{ $balance !== null ? $balance : 'N/A' }}
                    </div>
                    <div class="stat-card-sub text-success">
                        <i class="fas fa-bolt me-1"></i> MRAM API সংযুক্ত
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sent SMS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe;">
                    <i class="fas fa-paper-plane"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">আজকের প্রেরিত SMS</span>
                    <div class="stat-card-value text-dark fw-bold my-1">{{ $stats['today_sent'] }} <span class="fs-6 fw-normal text-muted">টি</span></div>
                    <div class="stat-card-sub text-muted">
                        <i class="far fa-clock me-1"></i> আজকের দিন
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sent SMS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;">
                    <i class="fas fa-check-double"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">সর্বমোট সফল SMS</span>
                    <div class="stat-card-value text-dark fw-bold my-1">{{ $stats['total_sent'] }} <span class="fs-6 fw-normal text-muted">টি</span></div>
                    <div class="stat-card-sub text-success">
                        <i class="fas fa-arrow-trend-up me-1"></i> সফল ডেলিভারি
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue & Failed Count -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-badge-icon" style="background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6;">
                    <i class="fas fa-clock-rotate-left"></i>
                </span>
                <div class="flex-grow-1 min-w-0">
                    <span class="stat-card-title d-block">পেন্ডিং / ফেইল্ড</span>
                    <div class="stat-card-value text-dark fw-bold my-1">
                        {{ $stats['total_queued'] }} <span class="fs-6 fw-normal text-muted">কিউ</span> / {{ $stats['total_failed'] }} <span class="fs-6 fw-normal text-danger">ব্যর্থ</span>
                    </div>
                    <div class="stat-card-sub {{ $stats['total_failed'] > 0 ? 'text-danger' : 'text-muted' }}">
                        <i class="fas fa-info-circle me-1"></i> {{ $stats['total_failed'] > 0 ? 'লগে ব্যর্থ মেসেজ দেখুন' : 'সব স্বাভাবিক' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Master & Event Quick Toggles Card -->
<div class="card admin-surface-card mb-4 shadow-sm border">
    <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-toggle-on text-primary fs-5"></i>
            <h6 class="fw-bold text-dark mb-0">অর্ডার নোটিফিকেশন কুইক সুইচ (SMS Automation Triggers)</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-semibold text-muted">মাস্টার SMS সার্ভিস:</span>
            <form action="{{ route('admin.marketing.sms.toggle') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="key" value="sms_enabled">
                <input type="hidden" name="value" value="{{ ($settings['sms_enabled'] ?? '0') === '1' ? '0' : '1' }}">
                <button type="submit" class="btn btn-sm {{ ($settings['sms_enabled'] ?? '0') === '1' ? 'btn-success' : 'btn-secondary' }} px-3 rounded-pill fw-bold">
                    {{ ($settings['sms_enabled'] ?? '0') === '1' ? 'চালু আছে (Active)' : 'বন্ধ আছে (Inactive)' }}
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-4 bg-light">
        <div class="row g-3">
            @php
                $events = [
                    'sms_event_order_placed' => ['title' => 'নতুন অর্ডার (Order Placed)', 'desc' => 'চেকআউট সম্পন্ন হলে গ্রাহককে কনফার্মেশন SMS পাঠানো হবে।', 'icon' => 'fa-shopping-cart'],
                    'sms_event_order_processing' => ['title' => 'প্রসেসিং (Processing)', 'desc' => 'অর্ডার প্রসেসিং-এ গেলে গ্রাহককে প্রস্তুতিমূলক মেসেজ পাঠানো হবে।', 'icon' => 'fa-box'],
                    'sms_event_order_shipped' => ['title' => 'কুরিয়ারে হস্তান্তর (Shipped)', 'desc' => 'পণ্য কুরিয়ারে জমা দিলে গ্রাহককে ট্র্যাকিং মেসেজ পাঠানো হবে।', 'icon' => 'fa-truck'],
                    'sms_event_order_delivered' => ['title' => 'ডেলিভার্ড (Delivered)', 'desc' => 'পণ্য গ্রাহক রিসিভ করলে ধন্যবাদ জানিয়ে মেসেজ পাঠানো হবে।', 'icon' => 'fa-check-circle'],
                    'sms_event_order_cancelled' => ['title' => 'অর্ডার বাতিল (Cancelled)', 'desc' => 'কোনো অর্ডার বাতিল হলে গ্রাহককে কারণ ও নোটিশ পাঠানো হবে।', 'icon' => 'fa-times-circle'],
                ];
            @endphp

            @foreach($events as $eventKey => $evt)
            @php $isActive = ($settings[$eventKey] ?? '1') === '1'; @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="p-3 bg-white rounded-3 border h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas {{ $evt['icon'] }} text-primary"></i>
                            <strong class="text-dark" style="font-size: 14.5px;">{{ $evt['title'] }}</strong>
                        </div>
                        <form action="{{ route('admin.marketing.sms.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{ $eventKey }}">
                            <input type="hidden" name="value" value="{{ $isActive ? '0' : '1' }}">
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" onchange="this.form.submit()" {{ $isActive ? 'checked' : '' }} style="cursor: pointer; transform: scale(1.2);">
                            </div>
                        </form>
                    </div>
                    <p class="text-muted small mb-0">{{ $evt['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 3. SMS Transmission Logs & History -->
<div class="card admin-surface-card shadow-sm border">
    <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom">
        <div>
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-list-alt text-danger"></i>
                <span>SMS ডেলিভারি লগ ও হিস্ট্রি (Transmission Logs)</span>
            </h5>
            <small class="text-muted">সিস্টেম থেকে প্রেরিত সমস্ত মেসেজের স্থিতি ও রিপোর্ট</small>
        </div>

        <!-- Filter & Search Form -->
        <form action="{{ route('admin.marketing.sms') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 130px;">
                <option value="">সকল স্ট্যাটাস</option>
                <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>সফল (Sent)</option>
                <option value="queued" {{ $status === 'queued' ? 'selected' : '' }}>কিউ (Queued)</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>ব্যর্থ (Failed)</option>
            </select>
            <div class="input-group input-group-sm" style="width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="নম্বর বা মেসেজ..." value="{{ $search }}">
                <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            </div>
            @if($search || $status)
            <a href="{{ route('admin.marketing.sms') }}" class="btn btn-sm btn-link text-decoration-none">রিসেট</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light border-bottom">
                    <tr style="font-size: 15px; color: #334155;">
                        <th class="ps-4 fw-bold">প্রাপক (Phone)</th>
                        <th class="fw-bold">উদ্দেশ্য / ইভেন্ট</th>
                        <th class="fw-bold">মেসেজ কন্টেন্ট</th>
                        <th class="fw-bold">অর্ডার</th>
                        <th class="fw-bold">স্ট্যাটাস</th>
                        <th class="fw-bold">তারিখ ও সময়</th>
                        <th class="text-end pe-4 fw-bold">API রেসপন্স</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark font-monospace" style="font-size: 14.5px;">
                                {{ $log->recipient }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-medium" style="font-size: 12px;">
                                {{ $log->purpose }}
                            </span>
                        </td>
                        <td style="max-width: 320px;">
                            <div class="text-truncate text-dark" style="font-size: 13.5px;" title="{{ $log->message }}">
                                {{ $log->message }}
                            </div>
                        </td>
                        <td>
                            @if($log->order)
                            <a href="{{ route('admin.orders.show', $log->order_id) }}" class="fw-semibold text-primary text-decoration-none small">
                                {{ $log->order->order_number }}
                            </a>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'sent')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Sent
                                </span>
                            @elseif($log->status === 'queued')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-bold">
                                    <i class="fas fa-clock me-1"></i> Queued
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                    <i class="fas fa-times-circle me-1"></i> Failed
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 13px; color: #475569;">
                            {{ $log->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small text-muted" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="left" title="API রেসপন্স লগ" data-bs-content="{{ $log->response_data ?? 'কোনো অতিরিক্ত রেসপন্স নেই' }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fs-2 mb-2 d-block text-secondary"></i>
                            কোনো SMS লগ রেকর্ড পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Modal: Test SMS -->
<div class="modal fade" id="testSmsModal" tabindex="-1" aria-labelledby="testSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold text-dark" id="testSmsModalLabel">
                    <i class="fas fa-paper-plane text-danger me-2"></i> টেস্ট SMS প্রেরণ করুন
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.marketing.sms.test') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        আপনার কনফিগার করা MRAM API কাজ করছে কি-না তা যাচাই করতে নিজের মোবাইল নম্বরে একটি টেস্ট মেসেজ পাঠান।
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">প্রাপকের মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="test_phone" class="form-control" placeholder="যেমন: 01712345678 বা 8801712345678" required>
                        <div class="form-text small">১১ ডিজিটের বাংলাদেশি মোবাইল নম্বর দিন।</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">মেসেজ কন্টেন্ট <span class="text-danger">*</span></label>
                        <textarea name="test_message" class="form-control" rows="3" required>এটি {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }} থেকে একটি সফল টেস্ট মেসেজ।</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-sm btn-admin-primary px-4 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> মেসেজ পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap popovers for API response details
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Live balance refresh handler
    const btnRefresh = document.getElementById('btnRefreshBalance');
    const spinner = document.getElementById('balanceSpinner');
    const balanceDisplay = document.getElementById('liveBalanceDisplay');

    if (btnRefresh && balanceDisplay) {
        btnRefresh.addEventListener('click', function() {
            spinner.classList.add('fa-spin');
            fetch('{{ route("admin.marketing.sms.balance") }}')
                .then(res => res.json())
                .then(data => {
                    spinner.classList.remove('fa-spin');
                    if (data.success) {
                        balanceDisplay.innerText = data.balance;
                    } else {
                        balanceDisplay.innerText = 'ত্রুটি';
                    }
                })
                .catch(() => {
                    spinner.classList.remove('fa-spin');
                    balanceDisplay.innerText = 'ত্রুটি';
                });
        });
    }
});
</script>
@endpush
