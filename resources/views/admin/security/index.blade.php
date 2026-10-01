@extends('layouts.admin')

@section('title', 'সিকিউরিটি ও অর্ডার প্রোটেকশন — Admin Console')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-white text-dark border px-2 py-1 small fw-semibold">
                    <i class="fas fa-shield-alt text-success me-1"></i> Security &amp; Fraud Prevention
                </span>
                <span class="text-muted small">Anti-Fraud, Rate Limiting &amp; Access Controls</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">সিকিউরিটি ও অর্ডার প্রোটেকশন</h1>
            <p class="text-muted small mb-0">ফেক বা স্প্যাম অর্ডার রোধ, কাস্টমার অর্ডার ফ্রিকোয়েন্সি লিমিট এবং নির্দিষ্ট আইপি ব্লকিং পরিচালনা করুন।</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="submit" form="securitySettingsForm" class="btn btn-sm btn-admin-primary px-4 py-2 fw-bold shadow-xs rounded-3 d-inline-flex align-items-center gap-2">
                <i class="fas fa-save"></i>
                <span>পরিবর্তন সংরক্ষণ করুন</span>
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
        <i class="fas fa-check-circle fs-5"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <div class="fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="fw-bold">কিছু ফিল্ডে তথ্যগত ত্রুটি রয়েছে:</span>
        </div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Single focused column on the left (No right side widgets) -->
        <div class="col-12 col-lg-9 col-xl-8">
            <div class="d-flex flex-column gap-3">

                <!-- ==========================================
                     1. ORDER FRAUD & SPAM PROTECTION (COLLAPSIBLE)
                =========================================== -->
                <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                    <div class="card-header bg-white p-3 p-sm-4 d-flex align-items-center justify-content-between" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#orderProtectionCollapse" aria-expanded="false" aria-controls="orderProtectionCollapse">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 20px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span>অর্ডার প্রোটেকশন (Anti-Fraud)</span>
                                    @if($settings['order_protection_enabled'] == '1')
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-0 fw-normal small" style="font-size: 11px;">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-0 fw-normal small" style="font-size: 11px;">Inactive</span>
                                    @endif
                                </h5>
                                <span class="text-muted small">গ্রাহকের নির্দিষ্ট সময়ের মধ্যে অতিরিক্ত বা ভুয়া অর্ডার নিয়ন্ত্রণ</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3" onclick="event.stopPropagation();">
                            <div class="form-check form-switch m-0" title="সক্রিয় / নিষ্ক্রিয় করুন">
                                <input class="form-check-input" type="checkbox" role="switch" form="securitySettingsForm" id="order_protection_enabled" name="order_protection_enabled" value="1" {{ old('order_protection_enabled', $settings['order_protection_enabled']) == '1' ? 'checked' : '' }} style="width: 2.75rem; height: 1.4rem; cursor: pointer;">
                            </div>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-muted" data-bs-toggle="collapse" data-bs-target="#orderProtectionCollapse" aria-label="Toggle">
                                <i class="fas fa-chevron-down text-secondary transition-icon" id="orderProtectionChevron"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsed by default -->
                    <div class="collapse" id="orderProtectionCollapse">
                        <div class="card-body p-3 p-sm-4 border-top">
                            <div class="alert alert-light border small text-muted rounded-3 mb-4 d-flex align-items-start gap-2">
                                <i class="fas fa-info-circle text-primary mt-1"></i>
                                <div>
                                    এই অপশনটি চালু থাকলে একজন গ্রাহক নির্দিষ্ট সময়সীমার মধ্যে সর্বোচ্চ যতগুলো অর্ডার নির্ধারণ করবেন তার বেশি দিতে পারবে না। এটি ভুয়া ফোন নাম্বার বা বট দিয়ে বারবার অর্ডার প্লেস করা রুখে দেবে।
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold text-dark small">
                                        সর্বোচ্চ অর্ডার অনুমোদিত <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" form="securitySettingsForm" name="order_protection_max_orders" min="1" max="50" class="form-control" value="{{ old('order_protection_max_orders', $settings['order_protection_max_orders']) }}">
                                        <span class="input-group-text bg-light text-muted small">টি অর্ডার</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 11px;">নির্ধারিত সময়ের ভেতর একজন কাস্টমার সর্বোচ্চ এতটি অর্ডার দিতে পারবে (সুপারিশ: ১ বা ২ টি)।</span>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold text-dark small">
                                        সময়সীমা (Time Window) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" form="securitySettingsForm" name="order_protection_time_window" min="1" max="1440" class="form-control" value="{{ old('order_protection_time_window', $settings['order_protection_time_window']) }}">
                                        <span class="input-group-text bg-light text-muted small">মিনিট</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 11px;">কত মিনিটের মধ্যে অর্ডার কাউন্ট হবে (যেমন: ৩০ মিনিট, ৬০ মিনিট)।</span>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark small">
                                        অর্ডার ট্র্যাকিং ও ফিল্টারিং পদ্ধতি <span class="text-danger">*</span>
                                    </label>
                                    <select name="order_protection_track_by" form="securitySettingsForm" class="form-select">
                                        <option value="phone_and_ip" {{ old('order_protection_track_by', $settings['order_protection_track_by']) == 'phone_and_ip' ? 'selected' : '' }}>
                                            মোবাইল নম্বর ও আইপি অ্যাড্রেস উভয়ই (Phone &amp; IP - সবচেয়ে সুরক্ষিত)
                                        </option>
                                        <option value="phone" {{ old('order_protection_track_by', $settings['order_protection_track_by']) == 'phone' ? 'selected' : '' }}>
                                            শুধুমাত্র মোবাইল নম্বর (Phone Number Only)
                                        </option>
                                        <option value="ip" {{ old('order_protection_track_by', $settings['order_protection_track_by']) == 'ip' ? 'selected' : '' }}>
                                            শুধুমাত্র আইপি অ্যাড্রেস (IP Address Only)
                                        </option>
                                    </select>
                                    <span class="text-muted" style="font-size: 11px;">'মোবাইল ও আইপি উভয়ই' সিলেক্ট রাখলে একই নম্বর বা একই ডিভাইস থেকে মাল্টিপল অর্ডার প্রতিরোধ করা সহজ হয়।</span>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark small">
                                        সীমা অতিক্রম করলে কাস্টমারকে প্রদর্শিত বার্তা <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="order_protection_block_message" form="securitySettingsForm" rows="3" class="form-control" placeholder="আপনি সম্প্রতি একটি অর্ডার প্লেস করেছেন...">{{ old('order_protection_block_message', $settings['order_protection_block_message']) }}</textarea>
                                    <span class="text-muted" style="font-size: 11px;">কোনো গ্রাহক সীমা অতিক্রম করার পর চেকআউটে অর্ডার প্লেস বাটনে ক্লিক করলে এই সতর্কতা দেখতে পাবে।</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     2. IP BLACKLISTING SYSTEM (COLLAPSIBLE)
                =========================================== -->
                <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                    <div class="card-header bg-white p-3 p-sm-4 d-flex align-items-center justify-content-between" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#ipBlockCollapse" aria-expanded="false" aria-controls="ipBlockCollapse">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 20px;">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span>আইপি ব্লকিং সিস্টেম (IP Blacklist)</span>
                                    @if($settings['ip_blocking_enabled'] == '1')
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0 fw-normal small" style="font-size: 11px;">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-0 fw-normal small" style="font-size: 11px;">Inactive</span>
                                    @endif
                                </h5>
                                <span class="text-muted small">নির্দিষ্ট আইপি অ্যাড্রেস থেকে অর্ডার বা চেকআউট এক্সেস ব্লক করুন</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3" onclick="event.stopPropagation();">
                            <div class="form-check form-switch m-0" title="সক্রিয় / নিষ্ক্রিয় করুন">
                                <input class="form-check-input" type="checkbox" role="switch" form="securitySettingsForm" id="ip_blocking_enabled" name="ip_blocking_enabled" value="1" {{ old('ip_blocking_enabled', $settings['ip_blocking_enabled']) == '1' ? 'checked' : '' }} style="width: 2.75rem; height: 1.4rem; cursor: pointer;">
                            </div>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-muted" data-bs-toggle="collapse" data-bs-target="#ipBlockCollapse" aria-label="Toggle">
                                <i class="fas fa-chevron-down text-secondary transition-icon" id="ipBlockChevron"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsed by default -->
                    <div class="collapse" id="ipBlockCollapse">
                        <div class="card-body p-3 p-sm-4 border-top">
                            
                            <!-- Action Header: Add New IP Button & Counter -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                                <div>
                                    <span class="fw-semibold text-dark small d-block">ব্লক করা আইপি তালিকা</span>
                                    <span class="text-muted" style="font-size: 11px;">মোট ব্লককৃত: <strong class="text-danger">{{ $blockedCount }}</strong> টি আইপি</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold px-3 py-1-5 rounded-2 d-inline-flex align-items-center gap-2 shadow-xs" data-bs-toggle="collapse" data-bs-target="#addNewIpBox" aria-expanded="false" aria-controls="addNewIpBox" id="btnToggleAddIp">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add New IP</span>
                                </button>
                            </div>

                            <!-- "Add New IP" Input Box (Toggles cleanly when clicking "Add New IP") -->
                            <div class="collapse mb-4" id="addNewIpBox">
                                <div class="p-3 bg-light rounded-3 border border-danger-subtle">
                                    <form method="POST" action="{{ route('admin.security.ip.add') }}">
                                        @csrf
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label fw-bold text-dark small mb-0">
                                                <i class="fas fa-shield-alt text-danger me-1"></i> নতুন আইপি ব্লক করুন:
                                            </label>
                                            <button type="button" class="btn-close small" data-bs-toggle="collapse" data-bs-target="#addNewIpBox" aria-label="Close"></button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12 col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white text-muted small font-monospace">IP</span>
                                                    <input type="text" name="ip" class="form-control font-monospace" placeholder="e.g. 103.145.22.10 বা 192.168.1.50" required autofocus>
                                                </div>
                                                <span class="text-muted" style="font-size: 11px;">সঠিক IPv4 বা IPv6 অ্যাড্রেস প্রদান করুন।</span>
                                            </div>
                                            <div class="col-12 col-sm-4 d-flex align-items-start gap-2">
                                                <button type="submit" class="btn btn-danger w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-xs">
                                                    <i class="fas fa-ban"></i>
                                                    <span>সাবমিট ও ব্লক</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Blocked IPs List Display (Clean, compact, not cluttered together) -->
                            <div class="mb-4">
                                @if(empty($blockedIpsList))
                                    <div class="p-4 text-center bg-light rounded-3 border border-dashed text-muted">
                                        <i class="fas fa-check-circle text-success fs-3 mb-2 d-block"></i>
                                        <span class="small fw-semibold d-block">বর্তমানে কোনো আইপি ব্লকলিস্টে নেই।</span>
                                        <span style="font-size: 11px;">নতুন আইপি ব্লক করতে উপরের 'Add New IP' বাটনে ক্লিক করুন।</span>
                                    </div>
                                @else
                                    <div class="border rounded-3 overflow-hidden bg-white shadow-xs">
                                        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                            <table class="table table-hover table-sm align-middle mb-0">
                                                <thead class="bg-light text-muted small position-sticky top-0" style="z-index: 1;">
                                                    <tr>
                                                        <th class="ps-3 py-2 fw-semibold" style="width: 50px;">#</th>
                                                        <th class="py-2 fw-semibold">আইপি অ্যাড্রেস (IP Address)</th>
                                                        <th class="py-2 fw-semibold text-center" style="width: 120px;">স্ট্যাটাস</th>
                                                        <th class="pe-3 py-2 fw-semibold text-end" style="width: 100px;">অ্যাকশন</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="small">
                                                    @foreach($blockedIpsList as $index => $ip)
                                                    <tr>
                                                        <td class="ps-3 text-muted font-monospace">{{ $index + 1 }}</td>
                                                        <td class="fw-semibold text-dark font-monospace">
                                                            <i class="fas fa-ban text-danger me-1 small"></i>
                                                            {{ $ip }}
                                                            @if($ip === $currentIp)
                                                                <span class="badge bg-warning-subtle text-dark border ms-1" style="font-size: 10px;">আপনার বর্তমান আইপি</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0" style="font-size: 10px;">Blocked</span>
                                                        </td>
                                                        <td class="pe-3 text-end">
                                                            <form method="POST" action="{{ route('admin.security.ip.remove') }}" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে আইপি {{ $ip }} আনব্লক করতে চান?');">
                                                                @csrf
                                                                <input type="hidden" name="ip" value="{{ $ip }}">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-2 border-0 text-danger hover-bg-light" title="আনব্লক করুন">
                                                                    <i class="fas fa-trash-alt me-1"></i> আনব্লক
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Custom Message Shown to Blocked IPs -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-dark small">
                                    ব্লক করা আইপি ব্যবহারকারীকে প্রদর্শিত বার্তা <span class="text-danger">*</span>
                                </label>
                                <textarea name="ip_block_message" form="securitySettingsForm" rows="2" class="form-control" placeholder="আপনার আইপি সাময়িকভাবে স্থগিত রাখা হয়েছে...">{{ old('ip_block_message', $settings['ip_block_message']) }}</textarea>
                                <span class="text-muted" style="font-size: 11px;">ব্লককৃত আইপি থেকে চেকআউটে প্রবেশ বা অর্ডারের চেষ্টা করলে এই বার্তা প্রদর্শিত হবে।</span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden Main Settings Form -->
    <form id="securitySettingsForm" method="POST" action="{{ route('admin.security.update') }}" class="d-none">
        @csrf
    </form>
</div>

<style>
.transition-icon {
    transition: transform 0.25s ease-in-out;
}
.collapse.show + .transition-icon,
[aria-expanded="true"] .transition-icon {
    transform: rotate(180deg);
}
</style>
@endsection
