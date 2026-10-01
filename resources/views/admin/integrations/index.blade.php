@extends('layouts.admin')

@section('title', 'Integrations & Scripts')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #4338ca; font-size: 13px; font-weight: 600;">
                    <i class="fas fa-plug me-1"></i> Marketing &amp; Automation
                </span>
                <span class="text-muted small">Tracking &amp; Third-Party Services</span>
            </div>
            <h1 class="h3 fw-bold text-dark mt-1 mb-0">Integrations &amp; Scripts</h1>
            <p class="text-muted small mb-0">Configure Meta Pixel, Google Analytics, Tag Manager, TikTok Pixel, WhatsApp widget, and custom HTML/JS code.</p>
        </div>

        <div>
            <button type="submit" form="integrationForm" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" style="background: var(--admin-primary); border-color: var(--admin-primary);">
                <i class="fas fa-save"></i> Save All Integrations
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form id="integrationForm" method="POST" action="{{ route('admin.integrations.update') }}">
        @csrf

        <div class="row g-4">
            <!-- 1. Meta (Facebook) Pixel & CAPI -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(24, 119, 242, 0.12); color: #1877f2;">
                                <i class="fab fa-facebook-f fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Meta (Facebook) Pixel &amp; CAPI</h5>
                                <span class="text-muted small">Track AddToCart, ViewContent &amp; Purchases</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5">
                            <input class="form-check-input" type="checkbox" role="switch" name="meta_pixel_enabled" value="1" id="metaPixelSwitch" {{ $settings['meta_pixel_enabled'] == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Meta Pixel ID</label>
                            <input type="text" name="meta_pixel_id" class="form-control" placeholder="e.g. 123456789012345" value="{{ $settings['meta_pixel_id'] }}">
                            <div class="form-text small">Your 15 or 16 digit dataset/pixel ID from Meta Events Manager.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Conversions API (CAPI) Access Token <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea name="meta_capi_token" class="form-control font-monospace small" rows="2" placeholder="EAABsbCS... (long server access token)">{{ $settings['meta_capi_token'] }}</textarea>
                            <div class="form-text small">Allows high-accuracy server-side tracking bypassing iOS ad-blockers.</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-dark">Test Event Code <span class="text-muted fw-normal">(Optional for testing)</span></label>
                            <input type="text" name="meta_test_event_code" class="form-control" placeholder="e.g. TEST12345" value="{{ $settings['meta_test_event_code'] }}">
                            <div class="form-text small">Paste from Meta Events Manager &gt; Test Events tab during debug.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Google Tag Manager (GTM) & Google Analytics 4 (GA4) -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(234, 67, 53, 0.12); color: #ea4335;">
                                <i class="fab fa-google fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Google Marketing Platform</h5>
                                <span class="text-muted small">Google Tag Manager (GTM) &amp; GA4</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- GTM Section -->
                        <div class="p-3 rounded-3 bg-light border mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark small"><i class="fas fa-tag me-1 text-primary"></i> Google Tag Manager (GTM)</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="gtm_enabled" value="1" id="gtmSwitch" {{ $settings['gtm_enabled'] == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <input type="text" name="gtm_container_id" class="form-control form-control-sm" placeholder="e.g. GTM-XXXXXXX" value="{{ $settings['gtm_container_id'] }}">
                            <div class="form-text small" style="font-size: 11px;">Container ID starting with GTM-. Automatically injects both Head &amp; NoScript tags.</div>
                        </div>

                        <!-- GA4 Section -->
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark small"><i class="fas fa-chart-pie me-1 text-warning"></i> Google Analytics 4 (GA4)</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="ga4_enabled" value="1" id="ga4Switch" {{ $settings['ga4_enabled'] == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <input type="text" name="ga4_measurement_id" class="form-control form-control-sm" placeholder="e.g. G-XXXXXXXXXX" value="{{ $settings['ga4_measurement_id'] }}">
                            <div class="form-text small" style="font-size: 11px;">Measurement ID starting with G-. Tracks page views and standard eCommerce flow.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TikTok Pixel & TikTok Ads -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(0, 0, 0, 0.08); color: #000000;">
                                <i class="fab fa-tiktok fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">TikTok Pixel</h5>
                                <span class="text-muted small">Track TikTok Ads &amp; Conversion Campaigns</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5">
                            <input class="form-check-input" type="checkbox" role="switch" name="tiktok_pixel_enabled" value="1" id="tiktokPixelSwitch" {{ $settings['tiktok_pixel_enabled'] == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">TikTok Pixel ID</label>
                            <input type="text" name="tiktok_pixel_id" class="form-control" placeholder="e.g. CXXXXXXXXXXXXXXX" value="{{ $settings['tiktok_pixel_id'] }}">
                            <div class="form-text small">Your TikTok Pixel ID from TikTok Ads Manager &gt; Assets &gt; Events.</div>
                        </div>
                        <div class="p-3 bg-light rounded-3 small text-muted">
                            <i class="fas fa-info-circle text-primary me-1"></i> When enabled, standard PageView and storefront interactions are automatically reported to your TikTok Ads Manager.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. WhatsApp Floating Live Chat Widget -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(37, 211, 102, 0.15); color: #25d366;">
                                <i class="fab fa-whatsapp fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">WhatsApp Live Chat Widget</h5>
                                <span class="text-muted small">Floating 1-click WhatsApp customer support</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5">
                            <input class="form-check-input" type="checkbox" role="switch" name="whatsapp_chat_enabled" value="1" id="whatsappSwitch" {{ $settings['whatsapp_chat_enabled'] == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">WhatsApp Number (with country code)</label>
                            <input type="text" name="whatsapp_number" class="form-control" placeholder="e.g. +8801700000000 or 8801700000000" value="{{ $settings['whatsapp_number'] }}">
                            <div class="form-text small">Bangladesh numbers format: <code>8801XXXXXXXXX</code> (without plus or spaces).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Default Message Preset</label>
                            <input type="text" name="whatsapp_default_message" class="form-control" placeholder="e.g. Hello SIDQ MART! I need assistance with an order." value="{{ $settings['whatsapp_default_message'] }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-dark">Floating Button Position</label>
                            <select name="whatsapp_widget_position" class="form-select form-select-sm">
                                <option value="bottom-right" {{ $settings['whatsapp_widget_position'] === 'bottom-right' ? 'selected' : '' }}>Bottom Right (ডানপাশে নিচে)</option>
                                <option value="bottom-left" {{ $settings['whatsapp_widget_position'] === 'bottom-left' ? 'selected' : '' }}>Bottom Left (বামপাশে নিচে)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Custom Code Injection (Header & Footer) -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-bottom pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(15, 23, 42, 0.08); color: #0f172a;">
                                <i class="fas fa-code fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Custom Code &amp; Script Injection</h5>
                                <span class="text-muted small">Inject raw HTML, CSS, JavaScript, verification meta tags, or chat services</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">
                                    <i class="fas fa-file-code me-1 text-primary"></i> Header Scripts (<code class="text-dark">&lt;head&gt;...&lt;/head&gt;</code>)
                                </label>
                                <textarea name="custom_header_scripts" class="form-control font-monospace small" rows="8" placeholder="<!-- Paste domain verification tags, custom CSS, or third-party tracking scripts here -->">{{ $settings['custom_header_scripts'] }}</textarea>
                                <div class="form-text small">Injected immediately before the closing <code>&lt;/head&gt;</code> tag on customer-facing storefront pages.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">
                                    <i class="fas fa-file-code me-1 text-success"></i> Footer Scripts (<code class="text-dark">before &lt;/body&gt;</code>)
                                </label>
                                <textarea name="custom_footer_scripts" class="form-control font-monospace small" rows="8" placeholder="<!-- Paste external live chats (Tawk.to, Crisp), remarketing scripts, or custom JS here -->">{{ $settings['custom_footer_scripts'] }}</textarea>
                                <div class="form-text small">Injected immediately before the closing <code>&lt;/body&gt;</code> tag on customer-facing storefront pages.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-5 text-end">
            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" style="background: var(--admin-primary); border-color: var(--admin-primary);">
                <i class="fas fa-save me-1"></i> Save All Integrations
            </button>
        </div>
    </form>
</div>
@endsection
