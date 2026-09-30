@extends('layouts.admin')

@section('title', 'সাইট ও পেমেন্ট সেটিংস - Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">সাইট ও পেমেন্ট সেটিংস (Store Settings)</h3>
        <p class="text-muted small mb-0">ওয়েবসাইটের নাম, লোগো, ডেলিভারি চার্জ এবং চেকআউট পেজের পেমেন্ট তথ্য পরিবর্তন করুন।</p>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- 1. General Store Identity & Branding -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-store text-danger me-2"></i> স্টোর পরিচিতি ও লোগো (Branding)
                </h5>

                <div class="mb-3">
                    <label for="site_name" class="form-label fw-bold">ওয়েবসাইটের নাম (Website Name) <span class="text-danger">*</span></label>
                    <input type="text" name="site_name" id="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? 'SIDQ MART') }}" required>
                </div>

                <div class="mb-3">
                    <label for="site_slogan" class="form-label fw-bold">স্লোগান / ট্যাগলাইন (Slogan)</label>
                    <input type="text" name="site_slogan" id="site_slogan" class="form-control" value="{{ old('site_slogan', $settings['site_slogan'] ?? '') }}">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="site_logo" class="form-label fw-bold">হেডার লোগো (Header Logo)</label>
                        @if(!empty($settings['site_logo']))
                        <div class="mb-2 p-2 bg-light border rounded text-center">
                            <img src="{{ $settings['site_logo'] }}" alt="Logo" style="max-height: 44px; max-width: 100%; object-fit: contain;">
                        </div>
                        @endif
                        <input type="file" name="site_logo" id="site_logo" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-sm-6">
                        <label for="site_favicon" class="form-label fw-bold">ফ্যাভিকন (Favicon)</label>
                        @if(!empty($settings['site_favicon']))
                        <div class="mb-2 p-2 bg-light border rounded text-center">
                            <img src="{{ $settings['site_favicon'] }}" alt="Favicon" style="width: 32px; height: 32px; object-fit: contain;">
                        </div>
                        @endif
                        <input type="file" name="site_favicon" id="site_favicon" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notice_text" class="form-label fw-bold">টপ নোটিশ বার (Top Announcement Text)</label>
                    <input type="text" name="notice_text" id="notice_text" class="form-control" value="{{ old('notice_text', $settings['notice_text'] ?? '') }}">
                </div>

                <div class="mb-3">
                    <label for="footer_about" class="form-label fw-bold">ফুটার পরিচিতি বার্তা (Footer About Text)</label>
                    <textarea name="footer_about" id="footer_about" rows="3" class="form-control">{{ old('footer_about', $settings['footer_about'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Contact Information & Delivery Charges -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-truck text-danger me-2"></i> ডেলিভারি ও শিপিং চার্জ
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="delivery_inside_dhaka" class="form-label fw-bold">ঢাকার ভিতরে চার্জ (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="delivery_inside_dhaka" id="delivery_inside_dhaka" class="form-control" value="{{ old('delivery_inside_dhaka', $settings['delivery_inside_dhaka'] ?? 70) }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="delivery_outside_dhaka" class="form-label fw-bold">ঢাকার বাহিরে চার্জ (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="delivery_outside_dhaka" id="delivery_outside_dhaka" class="form-control" value="{{ old('delivery_outside_dhaka', $settings['delivery_outside_dhaka'] ?? 130) }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="free_delivery_threshold" class="form-label fw-bold">ফ্রি ডেলিভারি ন্যূনতম কেনাকাটা (৳)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="free_delivery_threshold" id="free_delivery_threshold" class="form-control" value="{{ old('free_delivery_threshold', $settings['free_delivery_threshold'] ?? 3000) }}" min="0">
                        </div>
                        <div class="form-text small">এই পরিমাণের বেশি অর্ডার হলে ডেলিভারি স্বয়ংক্রিয়ভাবে ফ্রি হবে। বন্ধ রাখতে 0 দিন।</div>
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-phone-alt text-danger me-2"></i> যোগাযোগ ও কাস্টমার কেয়ার
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label for="contact_phone" class="form-label fw-bold">হটলাইন / মোবাইল নম্বর</label>
                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="whatsapp_number" class="form-label fw-bold">হোয়াটসঅ্যাপ নম্বর</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="contact_email" class="form-label fw-bold">ইমেইল ঠিকানা</label>
                        <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="contact_address" class="form-label fw-bold">ঠিকানা (Address)</label>
                        <input type="text" name="contact_address" id="contact_address" class="form-control" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dynamic Checkout Page Payment Information -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-wallet text-danger me-2"></i> চেকআউট পেজের পেমেন্ট পদ্ধতি ও নির্দেশনা (Checkout Payment Info)
                </h5>

                <div class="row g-4">
                    <!-- Cash on Delivery Configuration -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ ($settings['cod_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-success" for="cod_enabled">ক্যাশ অন ডেলিভারি (COD)</label>
                            </div>
                            <div class="mb-2">
                                <label for="cod_instructions" class="form-label small fw-bold">চেকআউট পেজে প্রদর্শিত নির্দেশনা:</label>
                                <textarea name="cod_instructions" id="cod_instructions" rows="3" class="form-control form-control-sm">{{ old('cod_instructions', $settings['cod_instructions'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- bKash Configuration -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="bkash_enabled" id="bkash_enabled" value="1" {{ ($settings['bkash_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="bkash_enabled">বিকাশ (bKash Payment)</label>
                            </div>
                            <div class="mb-2">
                                <label for="bkash_number" class="form-label small fw-bold">বিকাশ অ্যাকাউন্ট নম্বর:</label>
                                <input type="text" name="bkash_number" id="bkash_number" class="form-control form-control-sm" value="{{ old('bkash_number', $settings['bkash_number'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label for="bkash_type" class="form-label small fw-bold">অ্যাকাউন্টের ধরন:</label>
                                <input type="text" name="bkash_type" id="bkash_type" class="form-control form-control-sm" value="{{ old('bkash_type', $settings['bkash_type'] ?? 'Personal (Send Money)') }}">
                            </div>
                            <div class="mb-2">
                                <label for="bkash_instructions" class="form-label small fw-bold">গ্রাহকের জন্য নির্দেশনা:</label>
                                <textarea name="bkash_instructions" id="bkash_instructions" rows="2" class="form-control form-control-sm">{{ old('bkash_instructions', $settings['bkash_instructions'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Nagad Configuration -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="nagad_enabled" id="nagad_enabled" value="1" {{ ($settings['nagad_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-warning" for="nagad_enabled">নগদ (Nagad Payment)</label>
                            </div>
                            <div class="mb-2">
                                <label for="nagad_number" class="form-label small fw-bold">নগদ অ্যাকাউন্ট নম্বর:</label>
                                <input type="text" name="nagad_number" id="nagad_number" class="form-control form-control-sm" value="{{ old('nagad_number', $settings['nagad_number'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label for="nagad_type" class="form-label small fw-bold">অ্যাকাউন্টের ধরন:</label>
                                <input type="text" name="nagad_type" id="nagad_type" class="form-control form-control-sm" value="{{ old('nagad_type', $settings['nagad_type'] ?? 'Personal (Send Money)') }}">
                            </div>
                            <div class="mb-2">
                                <label for="nagad_instructions" class="form-label small fw-bold">গ্রাহকের জন্য নির্দেশনা:</label>
                                <textarea name="nagad_instructions" id="nagad_instructions" rows="2" class="form-control form-control-sm">{{ old('nagad_instructions', $settings['nagad_instructions'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Theme & Brand Color Customization -->
        <div class="col-12">
            <div class="card admin-surface-card p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-palette me-2" style="color: var(--admin-primary);"></i> থিম ও ব্র্যান্ড কালার কাস্টমাইজেশন (Theme & Primary Color)
                </h5>
                <p class="text-muted small mb-3">
                    ওয়েবসাইট এবং অ্যাডমিন প্যানেলের প্রাইমারি ব্র্যান্ড কালার, বাটন কালার এবং ব্যাকগ্রাউন্ড থিম এক ক্লিকেই পরিবর্তন করুন।
                </p>

                <div class="row g-4 align-items-center">
                    <!-- Primary Brand Color -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">প্রাইমারি ব্র্যান্ড কালার (Primary Color)</label>
                        @php $primaryColor = old('theme_primary_color', $settings['theme_primary_color'] ?? '#f13124'); @endphp
                        <div class="input-group">
                            <input type="color" id="picker_primary" class="form-control form-control-color" value="{{ $primaryColor }}" title="প্রাইমারি কালার নির্বাচন করুন" style="max-width: 56px;">
                            <input type="text" name="theme_primary_color" id="theme_primary_color" class="form-control fw-bold font-monospace" value="{{ $primaryColor }}" placeholder="#f13124">
                        </div>
                        <div class="form-text small">নেভবার, ব্যাজ, প্রাইস ও মূল ব্র্যান্ড হাইলাইট কালার।</div>
                    </div>

                    <!-- Secondary / Action Button Color -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">অ্যাকশন বাটন ও হোভার কালার (Secondary Color)</label>
                        @php $secondaryColor = old('theme_secondary_color', $settings['theme_secondary_color'] ?? '#c9251a'); @endphp
                        <div class="input-group">
                            <input type="color" id="picker_secondary" class="form-control form-control-color" value="{{ $secondaryColor }}" title="সেকেন্ডারি কালার নির্বাচন করুন" style="max-width: 56px;">
                            <input type="text" name="theme_secondary_color" id="theme_secondary_color" class="form-control fw-bold font-monospace" value="{{ $secondaryColor }}" placeholder="#c9251a">
                        </div>
                        <div class="form-text small">অর্ডার বাটন গ্র্যাডিয়েন্ট এবং বাটন হোভার কালার।</div>
                    </div>

                    <!-- Admin Background Tint -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">অ্যাডমিন প্যানেল ব্যাকগ্রাউন্ড থিম (Admin Mint Tint)</label>
                        @php $adminBgTint = old('admin_bg_tint', $settings['admin_bg_tint'] ?? '#f8fffa'); @endphp
                        <select name="admin_bg_tint" id="admin_bg_tint" class="form-select">
                            <option value="#f8fffa" {{ $adminBgTint === '#f8fffa' ? 'selected' : '' }}>🌿 কিউট প্যাস্টেল মিন্ট (#f8fffa — ডিফল্ট)</option>
                            <option value="#f0fdf4" {{ $adminBgTint === '#f0fdf4' ? 'selected' : '' }}>🍃 ফ্রেশ এমারেল্ড মিন্ট (#f0fdf4)</option>
                            <option value="#f0f9ff" {{ $adminBgTint === '#f0f9ff' ? 'selected' : '' }}>💎 আইস স্কাই ব্লু (#f0f9ff)</option>
                            <option value="#faf5ff" {{ $adminBgTint === '#faf5ff' ? 'selected' : '' }}>🌸 সফট ল্যাভেন্ডার (#faf5ff)</option>
                            <option value="#fffbeb" {{ $adminBgTint === '#fffbeb' ? 'selected' : '' }}>☀️ ওয়ার্ম ক্রিম (#fffbeb)</option>
                            <option value="#f8fafc" {{ $adminBgTint === '#f8fafc' ? 'selected' : '' }}>⚪ ক্লাসিক স্লেট (#f8fafc)</option>
                        </select>
                        <div class="form-text small">অ্যাডমিন প্যানেলের ব্যাকগ্রাউন্ড কালার টোন।</div>
                    </div>

                    <!-- Popular Presets & Live Preview -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 border" style="background: var(--admin-mint-bg);">
                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-lg-7">
                                    <span class="small fw-bold text-dark d-block mb-2">এক ক্লিকে জনপ্রিয় ব্র্যান্ড কালার প্রিসেট (Quick Color Presets):</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#f13124" data-secondary="#c9251a">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#f13124;"></span> SIDQ Red
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#059669" data-secondary="#047857">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#059669;"></span> Emerald Green
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#2563eb" data-secondary="#1d4ed8">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#2563eb;"></span> Royal Blue
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#7c3aed" data-secondary="#6d28d9">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#7c3aed;"></span> Modern Violet
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#ea580c" data-secondary="#c2410c">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#ea580c;"></span> Vibrant Orange
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#e11d48" data-secondary="#be123c">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#e11d48;"></span> Rose Crimson
                                        </button>
                                        <button type="button" class="btn btn-sm border rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 bg-white shadow-xs preset-color-btn" data-primary="#0891b2" data-secondary="#0e7490">
                                            <span class="rounded-circle d-inline-block" style="width:14px;height:14px;background:#0891b2;"></span> Ocean Teal
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <span class="badge mb-1" id="preview_badge" style="background: {{ $primaryColor }};">-25% OFF</span>
                                            <div class="fw-bold small text-dark">লাইভ প্রিভিউ (Preview)</div>
                                            <div class="fw-bold" id="preview_price" style="color: {{ $primaryColor }};">৳ ১,২৫০</div>
                                        </div>
                                        <button type="button" id="preview_btn" class="btn text-white fw-bold px-3 py-2 border-0 rounded-2" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
                                            <i class="fas fa-shopping-bag me-1"></i> অর্ডার করুন
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Marketing & Pixels -->
        <div class="col-12">
            <div class="card admin-surface-card p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-line me-2" style="color: var(--admin-primary);"></i> মার্কেটিং ও ট্র্যাকিং পিক্সেল (Analytics & Meta Pixel)
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="meta_pixel_id" class="form-label fw-bold">ফেসবুক / মেটা পিক্সেল আইডি (Meta Pixel ID)</label>
                        <input type="text" name="meta_pixel_id" id="meta_pixel_id" class="form-control" value="{{ old('meta_pixel_id', $settings['meta_pixel_id'] ?? '') }}" placeholder="যেমন: 1014535146791355">
                        <div class="form-text small">পিক্সেল আইডি দিলে স্বয়ংক্রিয়ভাবে স্টোরের প্রতিটি পেজে ভিউ ও ইভেন্ট ট্র্যাক হবে।</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-admin-primary btn-lg px-5 py-3 fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> সকল সেটিংস সংরক্ষণ করুন
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pickerPrimary = document.getElementById('picker_primary');
        const inputPrimary = document.getElementById('theme_primary_color');
        const pickerSecondary = document.getElementById('picker_secondary');
        const inputSecondary = document.getElementById('theme_secondary_color');

        const previewBadge = document.getElementById('preview_badge');
        const previewPrice = document.getElementById('preview_price');
        const previewBtn = document.getElementById('preview_btn');

        function updateLivePreview() {
            const p = inputPrimary.value || '#f13124';
            const s = inputSecondary.value || '#c9251a';
            previewBadge.style.background = p;
            previewPrice.style.color = p;
            previewBtn.style.background = `linear-gradient(135deg, ${p} 0%, ${s} 100%)`;
        }

        pickerPrimary.addEventListener('input', function () {
            inputPrimary.value = this.value;
            updateLivePreview();
        });
        inputPrimary.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                pickerPrimary.value = this.value;
            }
            updateLivePreview();
        });

        pickerSecondary.addEventListener('input', function () {
            inputSecondary.value = this.value;
            updateLivePreview();
        });
        inputSecondary.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                pickerSecondary.value = this.value;
            }
            updateLivePreview();
        });

        document.querySelectorAll('.preset-color-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const p = this.dataset.primary;
                const s = this.dataset.secondary;
                inputPrimary.value = p;
                pickerPrimary.value = p;
                inputSecondary.value = s;
                pickerSecondary.value = s;
                updateLivePreview();
            });
        });
    });
</script>
@endpush
