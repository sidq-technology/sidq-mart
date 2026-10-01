@extends('layouts.admin')

@section('title', 'Help & Developer Support — SIDQ Technology')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-danger text-white px-2 py-1 small fw-semibold">
                    <i class="fas fa-headset me-1"></i> Developer &amp; Technical Support
                </span>
                <span class="text-muted small">SIDQ Technology Core Architecture</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Help &amp; Developer Support</h1>
            <p class="text-muted small mb-0">যেকোনো টেকনিক্যাল সমস্যা, সিস্টেম বাগ, বা সাইট ফিচার আপগ্রেডের জন্য ডেভেলপার টিমের সাথে সরাসরি যোগাযোগ করুন।</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="tel:{{ $systemInfo['phone'] }}" class="btn btn-sm btn-dark d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-xs">
                <i class="fas fa-phone-alt text-success"></i>
                <span class="fw-semibold">জরুরি কল: {{ $systemInfo['phone'] }}</span>
            </a>
            <a href="{{ $systemInfo['whatsapp_url'] }}" target="_blank" class="btn btn-sm btn-success d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-xs text-white">
                <i class="fab fa-whatsapp"></i>
                <span class="fw-semibold">WhatsApp Chat</span>
            </a>
        </div>
    </div>

    <!-- Quick Contact Cards Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Phone & Direct Call -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100 p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-light text-dark border px-2 py-1 small fw-semibold">Direct Call</span>
                    <div class="text-primary fs-5">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                </div>
                <div class="text-muted small mb-1" style="font-size: 12px;">মোবাইল হেল্পলাইন</div>
                <div class="fs-5 fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif;">
                    {{ $systemInfo['phone'] }}
                </div>
                <p class="text-muted small mb-3" style="font-size: 11.5px;">যেকোনো সময় সরাসরি ফোন দিয়ে কথা বলুন।</p>
                <a href="tel:{{ $systemInfo['phone'] }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold rounded-2 mt-auto d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-phone-alt"></i> Call Now
                </a>
            </div>
        </div>

        <!-- 2. WhatsApp Direct -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100 p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold">Live Support</span>
                    <div class="text-success fs-5">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                </div>
                <div class="text-muted small mb-1" style="font-size: 12px;">WhatsApp চ্যাট</div>
                <div class="fs-5 fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif;">
                    {{ $systemInfo['whatsapp'] }}
                </div>
                <p class="text-muted small mb-3" style="font-size: 11.5px;">স্ক্রিনশট বা মেসেজ পাঠিয়ে দ্রুত রেসপন্স পান।</p>
                <a href="{{ $systemInfo['whatsapp_url'] }}" target="_blank" class="btn btn-sm btn-success w-100 fw-semibold rounded-2 mt-auto d-inline-flex align-items-center justify-content-center gap-2 text-white">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
        </div>

        <!-- 3. Bio.link Portfolio -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100 p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-light text-dark border px-2 py-1 small fw-semibold">Developer Hub</span>
                    <div class="text-warning fs-5">
                        <i class="fas fa-link"></i>
                    </div>
                </div>
                <div class="text-muted small mb-1" style="font-size: 12px;">বায়ো ও পোর্টফোলিও</div>
                <div class="fs-6 fw-bold text-dark mb-2 text-truncate" style="font-family: 'Outfit', sans-serif;">
                    bio.link/jasimuddin
                </div>
                <p class="text-muted small mb-3" style="font-size: 11.5px;">ডেভেলপারের সকল প্রোফাইল ও লিংকসমূহ।</p>
                <a href="{{ $systemInfo['bio_link'] }}" target="_blank" class="btn btn-sm btn-outline-warning text-dark w-100 fw-semibold rounded-2 mt-auto d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-external-link-alt"></i> Open Bio.link
                </a>
            </div>
        </div>

        <!-- 4. Facebook Profile -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border bg-white rounded-3 shadow-xs h-100 p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small fw-semibold">Social Connect</span>
                    <div class="text-primary fs-5">
                        <i class="fab fa-facebook"></i>
                    </div>
                </div>
                <div class="text-muted small mb-1" style="font-size: 12px;">ফেসবুক কানেক্ট</div>
                <div class="fs-6 fw-bold text-dark mb-2 text-truncate" style="font-family: 'Outfit', sans-serif;">
                    jasimuddinevan
                </div>
                <p class="text-muted small mb-3" style="font-size: 11.5px;">ফেসবুক মেসেঞ্জারে সরাসরি কথা বলুন।</p>
                <a href="{{ $systemInfo['facebook_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 fw-semibold rounded-2 mt-auto d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fab fa-facebook-messenger"></i> Message on Facebook
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Developer & Agency Profile Card -->
        <div class="col-12 col-lg-7">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">সফটওয়্যার আর্কিটেক্ট ও কোম্পানি পরিচিতি</h6>
                        <span class="text-muted small">Official Technology Partner &amp; Engine Provider</span>
                    </div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small fw-semibold">
                        SIDQ Technology
                    </span>
                </div>
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 p-3 bg-light rounded-3 mb-4 border">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" style="width: 58px; height: 58px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); font-size: 22px;">
                            <i class="fas fa-laptop-code text-warning"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold text-dark mb-0">{{ $systemInfo['developer'] }}</h5>
                                <span class="badge bg-success small py-0 px-2" style="font-size: 10px;">Verified Dev</span>
                            </div>
                            <div class="text-muted small mt-1">
                                Lead Software Engineer &amp; Architect: <strong class="text-dark">{{ $systemInfo['lead_developer'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-shield-alt text-success me-2"></i>প্রোডাকশন সাপোর্ট ও সার্ভিস লেভেল (SLA):</h6>
                    <ul class="list-unstyled mb-4" style="font-size: 13.5px; line-height: 1.8;">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-check-circle text-success mt-1"></i>
                            <div><strong>২৪/৭ ইমার্জেন্সি সাপোর্ট:</strong> সার্ভার ডাউনটাইম, পেমেন্ট গেটওয়ে ফেইলিউর বা কোনো ক্রিটিক্যাল বাগ দেখা দিলে তাৎক্ষণিক ফিক্সের সুবিধা।</div>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-check-circle text-success mt-1"></i>
                            <div><strong>কাস্টম ফিচার ডেভেলপমেন্ট:</strong> সাইটে নতুন কোনো মডিউল, বিশেষ অফার ক্যাম্পেইন বা কুরিয়ার অটোমেশন যোগ করার সুবিধা।</div>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-check-circle text-success mt-1"></i>
                            <div><strong>সিকিউরিটি ও ডাটা ব্যাকআপ:</strong> ক্লাউড ব্যাকআপ এবং রেগুলার সিকিউরিটি আপডেট পরিচালনা।</div>
                        </li>
                    </ul>

                    <div class="p-3 border rounded-3 bg-warning-subtle border-warning-subtle">
                        <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold small mb-1">
                            <i class="fas fa-info-circle fs-6"></i> কোনো সমস্যা রিপোর্ট করার নিয়ম:
                        </div>
                        <p class="small text-muted mb-0" style="font-size: 12px;">
                            যদি কোনো এরর বা সমস্যা দেখা দেয়, তবে অনুগ্রহ করে সমস্যাটির একটি স্ক্রিনশট বা এরর মেসেজ এবং সংশ্লিষ্ট অর্ডার নম্বর বা পেজের লিঙ্ক WhatsApp-এ পাঠিয়ে দিন। আমাদের টেকনিক্যাল টিম দ্রুত সমাধান করে দেবে।
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Diagnostic Health Check -->
        <div class="col-12 col-lg-5">
            <div class="card border bg-white rounded-3 shadow-xs h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">System Health &amp; Diagnostics</h6>
                        <span class="text-muted small">সার্ভার ও সফটওয়্যার স্ট্যাটাস</span>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold">
                        <i class="fas fa-check-circle me-1"></i> Operational
                    </span>
                </div>
                <div class="card-body p-3 p-sm-4">
                    <ul class="list-group list-group-flush mb-3" style="font-size: 13px;">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Platform Engine</span>
                            <span class="fw-bold text-dark">{{ $systemInfo['engine'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Application Name</span>
                            <span class="fw-semibold text-dark">{{ $systemInfo['app_name'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">PHP Version</span>
                            <span class="badge bg-light text-dark border">{{ $systemInfo['php_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Laravel Framework</span>
                            <span class="badge bg-light text-dark border">v{{ $systemInfo['laravel_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Database Engine</span>
                            <span class="badge bg-success text-white"><i class="fas fa-database me-1"></i> {{ $systemInfo['database_status'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Environment</span>
                            <span class="badge bg-secondary text-uppercase">{{ $systemInfo['environment'] }}</span>
                        </li>
                    </ul>

                    <div class="p-3 bg-light border rounded-3 text-center">
                        <div class="fw-bold text-dark small mb-1">প্রোডাকশন লাইসেন্স</div>
                        <div class="text-muted" style="font-size: 11px;">
                            Core Software &amp; Source Architecture &copy; {{ date('Y') }} <br>
                            <strong class="text-dark">SIDQ Technology (সিদিক টেকনোলজি)</strong>. All Rights Reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
