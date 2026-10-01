@extends('layouts.admin')

@section('title', 'Integrations & Marketing Scripts — Admin Console')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-white text-dark border px-2 py-1 small fw-semibold">
                    <i class="fas fa-plug text-primary me-1"></i> Marketing &amp; Automation Hub
                </span>
                <span class="text-muted small">Tracking, Pixels &amp; Third-Party APIs</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Integrations &amp; Tracking Scripts</h1>
            <p class="text-muted small mb-0">মেটা পিক্সেল, গুগল ট্যাগ ম্যানেজার, জিএ৪, টিকটক পিক্সেল এবং কাস্টম স্ক্রিপ্ট পরিচালনা করুন।</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-white bg-white border text-dark fw-semibold px-3 py-2 shadow-xs rounded-3 d-inline-flex align-items-center gap-1" id="btnToggleAllIntegrations">
                <i class="fas fa-expand-alt text-secondary" id="toggleAllIntIcon"></i>
                <span id="toggleAllIntText">সবগুলো খুলুন (Expand All)</span>
            </button>
            <button type="submit" form="integrationForm" class="btn btn-sm btn-admin-primary px-4 py-2 fw-bold shadow-xs rounded-3 d-inline-flex align-items-center gap-2">
                <i class="fas fa-save"></i>
                <span>ইন্টিগ্রেশন সেভ করুন</span>
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form id="integrationForm" method="POST" action="{{ route('admin.integrations.update') }}">
        @csrf

        <div class="row g-4">
            <!-- Left Side: Collapsible Integration Modules (Clean Accordion Stack) -->
            <div class="col-12 col-lg-7 col-xl-8">
                <div class="d-flex flex-column gap-3" id="integrationsSections">

                    <!-- ==========================================
                         1. META (FACEBOOK) PIXEL & CAPI
                    =========================================== -->
                    <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                        <div class="card-header bg-white p-3 p-sm-4 integration-collapse-header d-flex align-items-center justify-content-between"
                             data-bs-toggle="collapse" 
                             data-bs-target="#intMeta" 
                             aria-expanded="true"
                             role="button">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(24, 119, 242, 0.12); color: #1877f2; font-size: 20px;">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0">১. Meta (Facebook) Pixel &amp; Conversions API</h6>
                                        @if($settings['meta_pixel_enabled'] == '1')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 small fw-semibold" style="font-size: 10px;">Active</span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-0 small fw-medium" style="font-size: 10px;">Disabled</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">ফেসবুক অ্যাডস ট্র্যাকিং, ViewContent, AddToCart ও Purchase ইভেন্ট</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3" onclick="event.stopPropagation()">
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="meta_pixel_enabled" value="1" id="metaPixelSwitch" {{ $settings['meta_pixel_enabled'] == '1' ? 'checked' : '' }} title="Meta Pixel চালু / বন্ধ করুন">
                                </div>
                                <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                            </div>
                        </div>

                        <div id="intMeta" class="collapse show">
                            <div class="card-body p-3 p-sm-4 border-top">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">Meta Pixel ID (Dataset ID) <span class="text-danger">*</span></label>
                                    <input type="text" name="meta_pixel_id" class="form-control" placeholder="যেমন: 123456789012345" value="{{ $settings['meta_pixel_id'] }}">
                                    <div class="form-text small">Meta Events Manager থেকে পাওয়া ১৫ বা ১৬ ডিজিটের পিক্সেল আইডি।</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">
                                        Conversions API (CAPI) Access Token <span class="text-muted fw-normal">(Optional for Server-side Tracking)</span>
                                    </label>
                                    <textarea name="meta_capi_token" class="form-control font-monospace small" rows="2" placeholder="EAABsbCS... (long server-side access token)">{{ $settings['meta_capi_token'] }}</textarea>
                                    <div class="form-text small">iOS 14.5+ অ্যাড-ব্লকার বাইপাস করে ১০০% নির্ভুল সার্ভার-সাইড পারচেজ ট্র্যাকিং নিশ্চিত করে।</div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-dark mb-1">
                                        Test Event Code <span class="text-muted fw-normal">(টেস্টিংয়ের জন্য ঐচ্ছিক)</span>
                                    </label>
                                    <input type="text" name="meta_test_event_code" class="form-control" placeholder="যেমন: TEST12345" value="{{ $settings['meta_test_event_code'] }}">
                                    <div class="form-text small">Events Manager &gt; Test Events ট্যাবে ইভেন্ট টেস্ট করতে এই কোড ব্যবহার করুন।</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         2. GOOGLE MARKETING PLATFORM (GTM & GA4)
                    =========================================== -->
                    <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                        <div class="card-header bg-white p-3 p-sm-4 integration-collapse-header d-flex align-items-center justify-content-between"
                             data-bs-toggle="collapse" 
                             data-bs-target="#intGoogle" 
                             aria-expanded="false"
                             role="button">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(234, 67, 53, 0.12); color: #ea4335; font-size: 20px;">
                                    <i class="fab fa-google"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0">২. Google Marketing Platform (GTM &amp; GA4)</h6>
                                        @if($settings['gtm_enabled'] == '1' || $settings['ga4_enabled'] == '1')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 small fw-semibold" style="font-size: 10px;">Active</span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-0 small fw-medium" style="font-size: 10px;">Disabled</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">গুগল ট্যাগ ম্যানেজার কন্টেইনার এবং গুগল অ্যানালিটিক্স ৪ ট্র্যাকিং</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                            </div>
                        </div>

                        <div id="intGoogle" class="collapse">
                            <div class="card-body p-3 p-sm-4 border-top">
                                <!-- GTM Subsection -->
                                <div class="p-3 rounded-3 bg-light border mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark small"><i class="fas fa-tag me-1 text-primary"></i> Google Tag Manager (GTM)</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="gtm_enabled" value="1" id="gtmSwitch" {{ $settings['gtm_enabled'] == '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <input type="text" name="gtm_container_id" class="form-control" placeholder="যেমন: GTM-XXXXXXX" value="{{ $settings['gtm_container_id'] }}">
                                    <div class="form-text small" style="font-size: 11.5px;">GTM- দিয়ে শুরু হওয়া কন্টেইনার আইডি। এটি হেডার এবং বডির NoScript ট্যাগ দুটিই অটো ইনজেক্ট করে।</div>
                                </div>

                                <!-- GA4 Subsection -->
                                <div class="p-3 rounded-3 bg-light border">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark small"><i class="fas fa-chart-pie me-1 text-warning"></i> Google Analytics 4 (GA4)</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="ga4_enabled" value="1" id="ga4Switch" {{ $settings['ga4_enabled'] == '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <input type="text" name="ga4_measurement_id" class="form-control" placeholder="যেমন: G-XXXXXXXXXX" value="{{ $settings['ga4_measurement_id'] }}">
                                    <div class="form-text small" style="font-size: 11.5px;">G- দিয়ে শুরু হওয়া মেজারমেন্ট আইডি। পেজভিউ এবং ভিজিটর রিপোর্ট ট্র্যাক করবে।</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         3. TIKTOK PIXEL & CONVERSIONS
                    =========================================== -->
                    <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                        <div class="card-header bg-white p-3 p-sm-4 integration-collapse-header d-flex align-items-center justify-content-between"
                             data-bs-toggle="collapse" 
                             data-bs-target="#intTiktok" 
                             aria-expanded="false"
                             role="button">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(0, 0, 0, 0.08); color: #000000; font-size: 20px;">
                                    <i class="fab fa-tiktok"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0">৩. TikTok Pixel &amp; Conversion Tracking</h6>
                                        @if($settings['tiktok_pixel_enabled'] == '1')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 small fw-semibold" style="font-size: 10px;">Active</span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-0 small fw-medium" style="font-size: 10px;">Disabled</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">টিকটক বিজ্ঞাপন ও কনভার্সন অপটিমাইজেশন ট্র্যাকিং</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3" onclick="event.stopPropagation()">
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tiktok_pixel_enabled" value="1" id="tiktokPixelSwitch" {{ $settings['tiktok_pixel_enabled'] == '1' ? 'checked' : '' }} title="TikTok Pixel চালু / বন্ধ করুন">
                                </div>
                                <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                            </div>
                        </div>

                        <div id="intTiktok" class="collapse">
                            <div class="card-body p-3 p-sm-4 border-top">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">TikTok Pixel ID <span class="text-danger">*</span></label>
                                    <input type="text" name="tiktok_pixel_id" class="form-control" placeholder="যেমন: CXXXXXXXXXXXXXXX" value="{{ $settings['tiktok_pixel_id'] }}">
                                    <div class="form-text small">TikTok Ads Manager &gt; Assets &gt; Events থেকে পাওয়া পিক্সেল কোড।</div>
                                </div>
                                <div class="p-3 bg-light rounded-3 small text-muted">
                                    <i class="fas fa-info-circle text-primary me-1"></i> টিকটক পিক্সেল সক্রিয় থাকলে স্টোরফ্রন্টের পেজভিউ ও ইউজার ইন্টারেকশন স্বয়ংক্রিয়ভাবে টিকটক অ্যাডস ম্যানেজারে রিপোর্ট হবে।
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         4. WHATSAPP FLOATING CHAT WIDGET
                    =========================================== -->
                    <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                        <div class="card-header bg-white p-3 p-sm-4 integration-collapse-header d-flex align-items-center justify-content-between"
                             data-bs-toggle="collapse" 
                             data-bs-target="#intWhatsapp" 
                             aria-expanded="false"
                             role="button">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(37, 211, 102, 0.15); color: #25d366; font-size: 22px;">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0">৪. WhatsApp Floating Live Chat Widget</h6>
                                        @if($settings['whatsapp_chat_enabled'] == '1')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 small fw-semibold" style="font-size: 10px;">Active</span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-0 small fw-medium" style="font-size: 10px;">Disabled</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">স্টোরফ্রন্টে ১-ক্লিক ফ্লোটিং হোয়াটসঅ্যাপ বাটন ও লাইভ সাপোর্ট</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3" onclick="event.stopPropagation()">
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="whatsapp_chat_enabled" value="1" id="whatsappSwitch" {{ $settings['whatsapp_chat_enabled'] == '1' ? 'checked' : '' }} title="WhatsApp বাটন চালু / বন্ধ করুন">
                                </div>
                                <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                            </div>
                        </div>

                        <div id="intWhatsapp" class="collapse">
                            <div class="card-body p-3 p-sm-4 border-top">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">হোয়াটসঅ্যাপ নম্বর (কান্ট্রি কোড সহ)</label>
                                        <input type="text" name="whatsapp_number" class="form-control" placeholder="যেমন: 88015XXXXXXXX" value="{{ $settings['whatsapp_number'] }}">
                                        <div class="form-text small">বাংলাদেশি নম্বরের ক্ষেত্রে প্লাস ছাড়া লিখুন: <code>8801XXXXXXXXX</code></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">বাটনের পজিশন (Position)</label>
                                        <select name="whatsapp_widget_position" class="form-select">
                                            <option value="bottom-right" {{ $settings['whatsapp_widget_position'] === 'bottom-right' ? 'selected' : '' }}>Bottom Right (ডানপাশে নিচে)</option>
                                            <option value="bottom-left" {{ $settings['whatsapp_widget_position'] === 'bottom-left' ? 'selected' : '' }}>Bottom Left (বামপাশে নিচে)</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark mb-1">ডিফল্ট মেসেজ প্রিসেট (Default Message)</label>
                                        <input type="text" name="whatsapp_default_message" class="form-control" placeholder="যেমন: আসসালামু আলাইকুম! আমার একটি অর্ডার সম্পর্কে জানতে চাই।" value="{{ $settings['whatsapp_default_message'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         5. CUSTOM CODE & SCRIPT INJECTION
                    =========================================== -->
                    <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
                        <div class="card-header bg-white p-3 p-sm-4 integration-collapse-header d-flex align-items-center justify-content-between"
                             data-bs-toggle="collapse" 
                             data-bs-target="#intCustomScripts" 
                             aria-expanded="false"
                             role="button">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(15, 23, 42, 0.08); color: #0f172a; font-size: 20px;">
                                    <i class="fas fa-code"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">৫. Custom Code &amp; Script Injection</h6>
                                    <span class="text-muted small">কাস্টম HTML, CSS, JavaScript, ডোমেইন ভেরিফিকেশন ও এক্সটার্নাল চ্যাটবট</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border d-none d-md-inline-block small">Developer</span>
                                <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                            </div>
                        </div>

                        <div id="intCustomScripts" class="collapse">
                            <div class="card-body p-3 p-sm-4 border-top">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            <i class="fas fa-file-code me-1 text-primary"></i> Header Scripts (<code class="text-dark">&lt;head&gt;...&lt;/head&gt;</code>)
                                        </label>
                                        <textarea name="custom_header_scripts" class="form-control font-monospace small" rows="5" placeholder="<!-- Paste domain verification tags, custom CSS, or third-party tracking scripts here -->">{{ $settings['custom_header_scripts'] }}</textarea>
                                        <div class="form-text small">কাস্টমার সাইটের <code>&lt;/head&gt;</code> ট্যাগের ঠিক আগে রেন্ডার হবে।</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            <i class="fas fa-file-code me-1 text-success"></i> Footer Scripts (<code class="text-dark">before &lt;/body&gt;</code>)
                                        </label>
                                        <textarea name="custom_footer_scripts" class="form-control font-monospace small" rows="5" placeholder="<!-- Paste external live chats (Tawk.to, Crisp), remarketing pixels, or custom JS here -->">{{ $settings['custom_footer_scripts'] }}</textarea>
                                        <div class="form-text small">কাস্টমার সাইটের <code>&lt;/body&gt;</code> শেষ হওয়ার ঠিক আগে রান করবে।</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Side: Clean Sticky Overview & Status Widget -->
            <div class="col-12 col-lg-5 col-xl-4">
                <div class="sticky-top" style="top: 84px; z-index: 900;">
                    
                    <!-- Live Status Tracker Card -->
                    <div class="card border bg-white rounded-3 shadow-xs mb-3">
                        <div class="card-header bg-transparent border-bottom py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="fas fa-signal text-success me-2"></i>ইন্টিগ্রেশন স্ট্যাটাস (Live Status)
                            </h6>
                            <span class="badge bg-light text-dark border small fw-normal">5 Services</span>
                        </div>
                        <div class="card-body p-3 p-sm-4">
                            <ul class="list-group list-group-flush" style="font-size: 13px;">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-facebook text-primary fs-6"></i>
                                        <span class="fw-medium text-dark">Meta Pixel</span>
                                    </div>
                                    @if($settings['meta_pixel_enabled'] == '1')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border fw-normal px-2 py-1">Disabled</span>
                                    @endif
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-tag text-primary fs-6"></i>
                                        <span class="fw-medium text-dark">Google Tag Manager</span>
                                    </div>
                                    @if($settings['gtm_enabled'] == '1')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border fw-normal px-2 py-1">Disabled</span>
                                    @endif
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-chart-pie text-warning fs-6"></i>
                                        <span class="fw-medium text-dark">Google Analytics 4</span>
                                    </div>
                                    @if($settings['ga4_enabled'] == '1')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border fw-normal px-2 py-1">Disabled</span>
                                    @endif
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-tiktok text-dark fs-6"></i>
                                        <span class="fw-medium text-dark">TikTok Pixel</span>
                                    </div>
                                    @if($settings['tiktok_pixel_enabled'] == '1')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border fw-normal px-2 py-1">Disabled</span>
                                    @endif
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-whatsapp text-success fs-6"></i>
                                        <span class="fw-medium text-dark">WhatsApp Live Chat</span>
                                    </div>
                                    @if($settings['whatsapp_chat_enabled'] == '1')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border fw-normal px-2 py-1">Disabled</span>
                                    @endif
                                </li>
                            </ul>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" form="integrationForm" class="btn btn-admin-primary w-100 py-2 fw-bold shadow-xs rounded-3 d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-save"></i> সকল ইন্টিগ্রেশন সেভ করুন
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Developer Tips Card -->
                    <div class="card border bg-white rounded-3 shadow-xs">
                        <div class="card-body p-3 p-sm-4">
                            <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small">
                                <i class="fas fa-lightbulb text-warning"></i> ট্র্যাকিং সহায়িকা ও টিপস:
                            </div>
                            <ul class="text-muted small ps-3 mb-3" style="font-size: 11.5px; line-height: 1.7;">
                                <li>আইডি বা টোকেন যুক্ত করে উপরে সংশ্লিষ্ট সুইচটি চালু (On) করুন।</li>
                                <li>কাস্টম জাভাস্ক্রিপ্ট যুক্ত করলে তা স্বয়ংক্রিয়ভাবে ক্যাশ ক্লিয়ার হয়ে লাইভ সাইটে প্রয়োগ হবে।</li>
                                <li>যেকোনো ত্রুটিতে <a href="{{ route('admin.support.index') }}" class="text-danger fw-semibold text-decoration-none">Help &amp; Support</a> টিমের সাথে যোগাযোগ করতে পারেন।</li>
                            </ul>

                            <div class="p-2 bg-light rounded text-center small text-muted" style="font-size: 11px;">
                                Powered by <strong class="text-dark">SIDQ Technology Commerce Core</strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .integration-collapse-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
    }
    .integration-collapse-header:hover {
        background-color: #f8fafc !important;
    }
    .accordion-arrow {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
    }
    .integration-collapse-header:not(.collapsed) .accordion-arrow {
        transform: rotate(180deg);
        color: var(--admin-primary) !important;
    }
    .integration-collapse-header.collapsed .accordion-arrow {
        transform: rotate(0deg);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnToggleAll = document.getElementById('btnToggleAllIntegrations');
        const toggleAllIcon = document.getElementById('toggleAllIntIcon');
        const toggleAllText = document.getElementById('toggleAllIntText');
        const collapseElements = document.querySelectorAll('#integrationsSections .collapse');

        let isAllExpanded = false;

        btnToggleAll.addEventListener('click', function () {
            isAllExpanded = !isAllExpanded;
            collapseElements.forEach(el => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                if (isAllExpanded) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            });

            if (isAllExpanded) {
                toggleAllIcon.className = 'fas fa-compress-alt text-secondary';
                toggleAllText.textContent = 'সবগুলো বন্ধ করুন (Collapse All)';
            } else {
                toggleAllIcon.className = 'fas fa-expand-alt text-secondary';
                toggleAllText.textContent = 'সবগুলো খুলুন (Expand All)';
            }
        });
    });
</script>
@endpush
