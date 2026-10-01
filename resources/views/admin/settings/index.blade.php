@extends('layouts.admin')

@section('title', 'সাইট ও সিস্টেম সেটিংস - Store Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
    @csrf

    <!-- Top Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-white text-dark border px-2 py-1 small fw-semibold">
                    <i class="fas fa-sliders-h text-primary me-1"></i> Core Configuration
                </span>
                <span class="text-muted small">One-time Setup &amp; Customization</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">সাইট ও সিস্টেম সেটিংস (Store Settings)</h1>
            <p class="text-muted small mb-0">ওয়েবসাইটের ব্র্যান্ডিং, ডেলিভারি চার্জ, কন্টাক্ট তথ্য এবং চেকআউট পেমেন্ট গেটওয়ে পরিচালনা করুন।</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Expand / Collapse All Controls -->
            <button type="button" class="btn btn-sm btn-white bg-white border text-dark fw-semibold px-3 py-2 shadow-xs rounded-3 d-inline-flex align-items-center gap-1" id="btnToggleAll">
                <i class="fas fa-expand-alt text-secondary" id="toggleAllIcon"></i>
                <span id="toggleAllText">সবগুলো খুলুন (Expand All)</span>
            </button>

            <!-- Save Button Top -->
            <button type="submit" class="btn btn-sm btn-admin-primary px-4 py-2 fw-bold shadow-xs rounded-3 d-inline-flex align-items-center gap-2">
                <i class="fas fa-save"></i>
                <span>সেটিংস সেভ করুন</span>
            </button>
        </div>
    </div>

    <!-- Collapsible Settings Accordion Container -->
    <div class="d-flex flex-column gap-3 mb-4" id="settingsSections">

        <!-- ==========================================
             1. STORE IDENTITY & BRANDING
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secIdentity" 
                 aria-expanded="true"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.12); color: #dc2626; font-size: 18px;">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">১. স্টোর পরিচিতি, ব্র্যান্ডিং ও লোগো (Store Identity &amp; Branding)</h6>
                        <span class="text-muted small">ওয়েবসাইটের নাম, স্লোগান, হেডার লোগো, ফ্যাভিকন ও টপ অ্যানাউন্সমেন্ট</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Essential</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secIdentity" class="collapse show">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="site_name" class="form-label small fw-bold text-dark mb-1">ওয়েবসাইটের নাম (Website Name) <span class="text-danger">*</span></label>
                            <input type="text" name="site_name" id="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? 'SIDQ MART') }}" required>
                            <div class="form-text small">হেডার, ফুটার এবং ইনভয়েসে প্রদর্শিত মূল ব্র্যান্ড নাম।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="site_slogan" class="form-label small fw-bold text-dark mb-1">স্লোগান / ট্যাগলাইন (Slogan)</label>
                            <input type="text" name="site_slogan" id="site_slogan" class="form-control" value="{{ old('site_slogan', $settings['site_slogan'] ?? '') }}" placeholder="যেমন: Online Shopping In Bangladesh">
                            <div class="form-text small">এসইও মেটা এবং ব্রাউজার ট্যাবে প্রদর্শিত হয়।</div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="site_logo" class="form-label small fw-bold text-dark mb-1">ওয়েবসাইট লোগো (Header Logo)</label>
                            @if(!empty($settings['site_logo']))
                            <div class="mb-2 p-2 bg-light border rounded text-center" style="min-height: 50px;">
                                <img src="{{ $settings['site_logo'] }}" alt="Logo" style="max-height: 40px; max-width: 100%; object-fit: contain;">
                            </div>
                            @endif
                            <input type="file" name="site_logo" id="site_logo" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text small">পিএনজি বা ওয়েবপি (সুপারিশ: ২০০x৫০ পিক্সেল)।</div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="site_favicon" class="form-label small fw-bold text-dark mb-1">ওয়েবসাইট ফ্যাভিকন (Favicon)</label>
                            @if(!empty($settings['site_favicon']))
                            <div class="mb-2 p-2 bg-light border rounded text-center" style="min-height: 50px;">
                                <img src="{{ $settings['site_favicon'] }}" alt="Favicon" style="width: 32px; height: 32px; object-fit: contain;">
                            </div>
                            @endif
                            <input type="file" name="site_favicon" id="site_favicon" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text small">ব্রাউজার ট্যাবের ছোট আইকন (৩২x৩২ পিক্সেল)।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="currency_symbol" class="form-label small fw-bold text-dark mb-1">মুদ্রা প্রতীক (Currency Symbol)</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" style="max-width: 120px;">
                            <div class="form-text small">পণ্য মূল্য ও চালানে ব্যবহৃত প্রতীক (যেমন: ৳ বা TK)।</div>
                        </div>

                        <div class="col-12">
                            <label for="notice_text" class="form-label small fw-bold text-dark mb-1">টপ নোটিশ বার বার্তা (Top Announcement Bar)</label>
                            <input type="text" name="notice_text" id="notice_text" class="form-control" value="{{ old('notice_text', $settings['notice_text'] ?? '') }}" placeholder="যেমন: সারা বাংলাদেশে ক্যাশ অন হোম ডেলিভারি সুবিধা!">
                            <div class="form-text small">ওয়েবসাইটের একদম উপরে সরু ব্যানারে অফার বা নোটিশ প্রদর্শনের জন্য। ফাঁকা রাখলে হাইড থাকবে।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="footer_about" class="form-label small fw-bold text-dark mb-1">ফুটার পরিচিতি বার্তা (Footer About Text)</label>
                            <textarea name="footer_about" id="footer_about" rows="3" class="form-control" placeholder="আপনার স্টোর সম্পর্কে সংক্ষিপ্ত ২-৩ লাইনের বিবরণ...">{{ old('footer_about', $settings['footer_about'] ?? '') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="copyright_text" class="form-label small fw-bold text-dark mb-1">কপিরাইট বার্তা (Footer Copyright Text)</label>
                            <textarea name="copyright_text" id="copyright_text" rows="3" class="form-control" placeholder="যেমন: All Rights Reserved.">{{ old('copyright_text', $settings['copyright_text'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             2. DELIVERY & SHIPPING CONFIGURATION
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secDelivery" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.12); color: #2563eb; font-size: 18px;">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">২. ডেলিভারি, শিপিং ও চালান সেটিংস (Shipping &amp; Delivery Rates)</h6>
                        <span class="text-muted small">ঢাকার ভিতরে/বাইরে চার্জ, ফ্রি ডেলিভারি শর্ত ও চালানের শর্তাবলী</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Logistics</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secDelivery" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="delivery_inside_dhaka" class="form-label small fw-bold text-dark mb-1">ঢাকার ভিতরে চার্জ (৳) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" name="delivery_inside_dhaka" id="delivery_inside_dhaka" class="form-control" value="{{ old('delivery_inside_dhaka', $settings['delivery_inside_dhaka'] ?? 70) }}" min="0" required>
                            </div>
                            <div class="form-text small">চেকআউট পেজে ঢাকার ভিতরে অটোমেটিক যোগ হবে।</div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="delivery_outside_dhaka" class="form-label small fw-bold text-dark mb-1">ঢাকার বাহিরে চার্জ (৳) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" name="delivery_outside_dhaka" id="delivery_outside_dhaka" class="form-control" value="{{ old('delivery_outside_dhaka', $settings['delivery_outside_dhaka'] ?? 130) }}" min="0" required>
                            </div>
                            <div class="form-text small">চেকআউট পেজে ঢাকার বাইরে অটোমেটিক যোগ হবে।</div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="free_delivery_threshold" class="form-label small fw-bold text-dark mb-1">ফ্রি ডেলিভারি ন্যূনতম কেনাকাটা (৳)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" name="free_delivery_threshold" id="free_delivery_threshold" class="form-control" value="{{ old('free_delivery_threshold', $settings['free_delivery_threshold'] ?? 3000) }}" min="0">
                            </div>
                            <div class="form-text small">এই পরিমাণের বেশি অর্ডারে শিপিং ফ্রি হবে। বন্ধ রাখতে 0 দিন।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="delivery_time_inside" class="form-label small fw-bold text-dark mb-1">আনুমানিক ডেলিভারি সময় (ঢাকার ভিতরে)</label>
                            <input type="text" name="delivery_time_inside" id="delivery_time_inside" class="form-control" value="{{ old('delivery_time_inside', $settings['delivery_time_inside'] ?? '২৪ থেকে ৪৮ ঘণ্টার মধ্যে') }}" placeholder="যেমন: ২৪ থেকে ৪৮ ঘণ্টার মধ্যে">
                            <div class="form-text small">প্রোডাক্ট পেজ বা চেকআউটে গ্রাহকদের আনুমানিক সময় জানানোর জন্য।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="delivery_time_outside" class="form-label small fw-bold text-dark mb-1">আনুমানিক ডেলিভারি সময় (ঢাকার বাহিরে)</label>
                            <input type="text" name="delivery_time_outside" id="delivery_time_outside" class="form-control" value="{{ old('delivery_time_outside', $settings['delivery_time_outside'] ?? '২ থেকে ৩ কার্যদিবস') }}" placeholder="যেমন: ২ থেকে ৩ কার্যদিবস">
                            <div class="form-text small">কুরিয়ারের মাধ্যমে ডেলিভারি সম্পন্ন হওয়ার সম্ভাব্য সময়।</div>
                        </div>

                        <div class="col-12">
                            <label for="invoice_footer_note" class="form-label small fw-bold text-dark mb-1">চালান / ইনভয়েসের ফুটার নোট (Invoice Terms &amp; Conditions)</label>
                            <textarea name="invoice_footer_note" id="invoice_footer_note" rows="2" class="form-control" placeholder="যেমন: পণ্য গ্রহণের সময় ডেলিভারি ম্যানের সামনে চেক করে নিন। কোনো ত্রুটি থাকলে সাথে সাথে রিটার্ন করুন।">{{ old('invoice_footer_note', $settings['invoice_footer_note'] ?? 'আমাদের সাথে কেনাকাটা করার জন্য ধন্যবাদ। কোনো প্রয়োজনে আমাদের কাস্টমার কেয়ারে যোগাযোগ করুন।') }}</textarea>
                            <div class="form-text small">ইনভয়েস প্রিন্ট করার সময় নিচের অংশে এই নোটটি প্রদর্শিত হবে।</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             3. CONTACT, HELPLINE & SOCIAL
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secContact" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 18px;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">৩. যোগাযোগ, হেল্পলাইন ও সোশ্যাল লিংক (Customer Care &amp; Social Links)</h6>
                        <span class="text-muted small">হটলাইন, অফিসিয়াল WhatsApp, সাপোর্ট ইমেইল, ঠিকানা ও সোশ্যাল প্রোফাইল</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Support</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secContact" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="contact_phone" class="form-label small fw-bold text-dark mb-1">হটলাইন / মোবাইল নম্বর <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone-alt"></i></span>
                                <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" placeholder="01XXXXXXXXX">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="whatsapp_number" class="form-label small fw-bold text-dark mb-1">অফিসিয়াল WhatsApp নম্বর</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="fab fa-whatsapp"></i></span>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}" placeholder="01XXXXXXXXX">
                            </div>
                            <div class="form-text small">ওয়েবসাইটের ফ্লোটিং হোয়াটসঅ্যাপ বাটনে কাজ করবে।</div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="contact_email" class="form-label small fw-bold text-dark mb-1">সাপোর্ট ইমেইল ঠিকানা</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="far fa-envelope"></i></span>
                                <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" placeholder="support@domain.com">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="order_notification_email" class="form-label small fw-bold text-dark mb-1">অর্ডার নোটিফিকেশন ইমেইল</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-bell"></i></span>
                                <input type="email" name="order_notification_email" id="order_notification_email" class="form-control" value="{{ old('order_notification_email', $settings['order_notification_email'] ?? '') }}" placeholder="orders@domain.com">
                            </div>
                            <div class="form-text small">নতুন অর্ডার আসলে এই ইমেইলে অ্যালার্ট যাবে।</div>
                        </div>

                        <div class="col-12">
                            <label for="contact_address" class="form-label small fw-bold text-dark mb-1">অফিস / শোরুমের পূর্ণাঙ্গ ঠিকানা (Address)</label>
                            <input type="text" name="contact_address" id="contact_address" class="form-control" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}" placeholder="দোকান/অফিস নম্বর, রোড, এরিয়া, জেলা...">
                        </div>

                        <!-- Social Media URL Inputs -->
                        <div class="col-12 col-md-4">
                            <label for="facebook_url" class="form-label small fw-bold text-dark mb-1">ফেসবুক পেজ (Facebook Page URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="fab fa-facebook-f"></i></span>
                                <input type="url" name="facebook_url" id="facebook_url" class="form-control" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" placeholder="https://facebook.com/yourpage">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="instagram_url" class="form-label small fw-bold text-dark mb-1">ইনস্টাগ্রাম (Instagram URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-danger"><i class="fab fa-instagram"></i></span>
                                <input type="url" name="instagram_url" id="instagram_url" class="form-control" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" placeholder="https://instagram.com/yourprofile">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="youtube_url" class="form-label small fw-bold text-dark mb-1">ইউটিউব চ্যানেল (YouTube URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-danger"><i class="fab fa-youtube"></i></span>
                                <input type="url" name="youtube_url" id="youtube_url" class="form-control" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" placeholder="https://youtube.com/@yourchannel">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. CHECKOUT PAYMENT GATEWAYS
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secPayment" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 18px;">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">৪. চেকআউট পেজের পেমেন্ট পদ্ধতি ও অ্যাকাউন্ট (Payment Gateways)</h6>
                        <span class="text-muted small">ক্যাশ অন ডেলিভারি, বিকাশ, নগদ ও রকেট পেমেন্ট চালু/বন্ধ এবং নম্বর</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Checkout</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secPayment" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="row g-4">
                        <!-- 1. COD -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ ($settings['cod_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-success" for="cod_enabled">ক্যাশ অন ডেলিভারি (COD)</label>
                                </div>
                                <div class="mb-2 flex-grow-1">
                                    <label for="cod_instructions" class="form-label small fw-bold">গ্রাহকের জন্য নির্দেশনা:</label>
                                    <textarea name="cod_instructions" id="cod_instructions" rows="4" class="form-control form-control-sm" placeholder="পণ্য হাতে পেয়ে চেক করে ডেলিভারি ম্যানের কাছে মূল্য পরিশোধ করুন।">{{ old('cod_instructions', $settings['cod_instructions'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 2. bKash -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="bkash_enabled" id="bkash_enabled" value="1" {{ ($settings['bkash_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" style="color: #e2136e;" for="bkash_enabled">বিকাশ (bKash Payment)</label>
                                </div>
                                <div class="mb-2">
                                    <label for="bkash_number" class="form-label small fw-bold">বিকাশ নম্বর:</label>
                                    <input type="text" name="bkash_number" id="bkash_number" class="form-control form-control-sm" value="{{ old('bkash_number', $settings['bkash_number'] ?? '') }}" placeholder="01XXXXXXXXX">
                                </div>
                                <div class="mb-2">
                                    <label for="bkash_type" class="form-label small fw-bold">অ্যাকাউন্টের ধরন:</label>
                                    <input type="text" name="bkash_type" id="bkash_type" class="form-control form-control-sm" value="{{ old('bkash_type', $settings['bkash_type'] ?? 'Personal (Send Money)') }}" placeholder="Personal / Merchant">
                                </div>
                                <div class="mb-2 flex-grow-1">
                                    <label for="bkash_instructions" class="form-label small fw-bold">নির্দেশনা:</label>
                                    <textarea name="bkash_instructions" id="bkash_instructions" rows="2" class="form-control form-control-sm">{{ old('bkash_instructions', $settings['bkash_instructions'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Nagad -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="nagad_enabled" id="nagad_enabled" value="1" {{ ($settings['nagad_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" style="color: #f7941d;" for="nagad_enabled">নগদ (Nagad Payment)</label>
                                </div>
                                <div class="mb-2">
                                    <label for="nagad_number" class="form-label small fw-bold">নগদ নম্বর:</label>
                                    <input type="text" name="nagad_number" id="nagad_number" class="form-control form-control-sm" value="{{ old('nagad_number', $settings['nagad_number'] ?? '') }}" placeholder="01XXXXXXXXX">
                                </div>
                                <div class="mb-2">
                                    <label for="nagad_type" class="form-label small fw-bold">অ্যাকাউন্টের ধরন:</label>
                                    <input type="text" name="nagad_type" id="nagad_type" class="form-control form-control-sm" value="{{ old('nagad_type', $settings['nagad_type'] ?? 'Personal (Send Money)') }}" placeholder="Personal / Merchant">
                                </div>
                                <div class="mb-2 flex-grow-1">
                                    <label for="nagad_instructions" class="form-label small fw-bold">নির্দেশনা:</label>
                                    <textarea name="nagad_instructions" id="nagad_instructions" rows="2" class="form-control form-control-sm">{{ old('nagad_instructions', $settings['nagad_instructions'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Rocket -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="rocket_enabled" id="rocket_enabled" value="1" {{ ($settings['rocket_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" style="color: #8b318d;" for="rocket_enabled">রকেট (Rocket Payment)</label>
                                </div>
                                <div class="mb-2">
                                    <label for="rocket_number" class="form-label small fw-bold">রকেট নম্বর:</label>
                                    <input type="text" name="rocket_number" id="rocket_number" class="form-control form-control-sm" value="{{ old('rocket_number', $settings['rocket_number'] ?? '') }}" placeholder="01XXXXXXXXX-X">
                                </div>
                                <div class="mb-2">
                                    <label for="rocket_type" class="form-label small fw-bold">অ্যাকাউন্টের ধরন:</label>
                                    <input type="text" name="rocket_type" id="rocket_type" class="form-control form-control-sm" value="{{ old('rocket_type', $settings['rocket_type'] ?? 'Personal (Send Money)') }}">
                                </div>
                                <div class="mb-2 flex-grow-1">
                                    <label for="rocket_instructions" class="form-label small fw-bold">নির্দেশনা:</label>
                                    <textarea name="rocket_instructions" id="rocket_instructions" rows="2" class="form-control form-control-sm">{{ old('rocket_instructions', $settings['rocket_instructions'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             5. THEME & BRAND COLOR STYLING
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secTheme" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(139, 92, 246, 0.12); color: #7c3aed; font-size: 18px;">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">৫. থিম, ব্র্যান্ড কালার ও ব্যাকগ্রাউন্ড টোন (Theme &amp; Brand Styling)</h6>
                        <span class="text-muted small">প্রাইমারি কালার, বাটন কালার, প্রিসেট প্যালেট এবং অ্যাডমিন প্যানেলের ব্যাকগ্রাউন্ড</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Design</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secTheme" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="row g-4 align-items-center mb-4">
                        <!-- Primary Brand Color -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">প্রাইমারি ব্র্যান্ড কালার (Primary Color)</label>
                            @php $primaryColor = old('theme_primary_color', $settings['theme_primary_color'] ?? '#f13124'); @endphp
                            <div class="input-group">
                                <input type="color" id="picker_primary" class="form-control form-control-color" value="{{ $primaryColor }}" title="কালার নির্বাচন করুন" style="max-width: 56px;">
                                <input type="text" name="theme_primary_color" id="theme_primary_color" class="form-control fw-bold font-monospace" value="{{ $primaryColor }}" placeholder="#f13124">
                            </div>
                            <div class="form-text small">নেভবার, ব্যাজ, টেক্সট ও প্রধান ব্র্যান্ড কালার।</div>
                        </div>

                        <!-- Secondary / Action Button Color -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">অ্যাকশন বাটন ও হোভার কালার (Secondary Color)</label>
                            @php $secondaryColor = old('theme_secondary_color', $settings['theme_secondary_color'] ?? '#c9251a'); @endphp
                            <div class="input-group">
                                <input type="color" id="picker_secondary" class="form-control form-control-color" value="{{ $secondaryColor }}" title="সেকেন্ডারি কালার নির্বাচন করুন" style="max-width: 56px;">
                                <input type="text" name="theme_secondary_color" id="theme_secondary_color" class="form-control fw-bold font-monospace" value="{{ $secondaryColor }}" placeholder="#c9251a">
                            </div>
                            <div class="form-text small">অর্ডার বাটন গ্র্যাডিয়েন্ট এবং হোভার অ্যাকসেন্ট।</div>
                        </div>

                        <!-- Admin Background Tint -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">অ্যাডমিন প্যানেল ব্যাকগ্রাউন্ড থিম (Admin Mint Tint)</label>
                            @php $adminBgTint = old('admin_bg_tint', $settings['admin_bg_tint'] ?? '#f8fffa'); @endphp
                            <select name="admin_bg_tint" id="admin_bg_tint" class="form-select">
                                <option value="#f8fffa" {{ $adminBgTint === '#f8fffa' ? 'selected' : '' }}>🌿 কিউট প্যাস্টেল মিন্ট (#f8fffa — ডিফল্ট)</option>
                                <option value="#f0fdf4" {{ $adminBgTint === '#f0fdf4' ? 'selected' : '' }}>🍃 ফ্রেশ এমারেল্ড মিন্ট (#f0fdf4)</option>
                                <option value="#f0f9ff" {{ $adminBgTint === '#f0f9ff' ? 'selected' : '' }}>💎 আইস স্কাই ব্লু (#f0f9ff)</option>
                                <option value="#faf5ff" {{ $adminBgTint === '#faf5ff' ? 'selected' : '' }}>🌸 সফট ল্যাভেন্ডার (#faf5ff)</option>
                                <option value="#fffbeb" {{ $adminBgTint === '#fffbeb' ? 'selected' : '' }}>☀️ ওয়ার্ম ক্রিম (#fffbeb)</option>
                                <option value="#f8fafc" {{ $adminBgTint === '#f8fafc' ? 'selected' : '' }}>⚪ ক্লাসিক স্লেট (#f8fafc)</option>
                            </select>
                            <div class="form-text small">অ্যাডমিন কনসোলের সফট ব্যাকগ্রাউন্ড হিউ।</div>
                        </div>
                    </div>

                    <!-- Presets & Preview Strip -->
                    <div class="p-3 rounded-3 border" style="background: var(--admin-mint-bg);">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-7">
                                <span class="small fw-bold text-dark d-block mb-2">জনপ্রিয় প্রিসেট প্যালেটসমূহ (Click to Apply):</span>
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
                                        <div class="fw-bold small text-dark">লাইভ প্রিভিউ (Live Preview)</div>
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

        <!-- ==========================================
             6. MARKETING & PIXELS
        =========================================== -->
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secMarketing" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.12); color: #1d4ed8; font-size: 18px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">৬. মার্কেটিং ও ট্র্যাকিং পিক্সেল (Analytics &amp; Meta Pixel)</h6>
                        <span class="text-muted small">ফেসবুক / মেটা পিক্সেল আইডি এবং মার্কেটিং অটোমেশন</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border d-none d-md-inline-block small">Tracking</span>
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secMarketing" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-3 border">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="fab fa-facebook text-primary me-1"></i> ফেসবুক / মেটা পিক্সেল ট্র্যাকিং চালু রাখুন
                            </div>
                            <div class="text-muted small">চালু থাকলে স্টোরফ্রন্টের প্রতিটি পেজে PageView, ViewContent, AddToCart, InitiateCheckout ও Purchase ইভেন্ট স্বয়ংক্রিয়ভাবে ট্র্যাক হবে।</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0 ms-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="meta_pixel_enabled" id="meta_pixel_enabled" value="1" {{ ($settings['meta_pixel_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="meta_pixel_id" class="form-label small fw-bold text-dark mb-1">ফেসবুক / মেটা পিক্সেল আইডি (Meta Pixel ID)</label>
                            <input type="text" name="meta_pixel_id" id="meta_pixel_id" class="form-control" value="{{ old('meta_pixel_id', $settings['meta_pixel_id'] ?? '') }}" placeholder="যেমন: 1014535146791355">
                            <div class="form-text small">পিক্সেল আইডি দিলে স্টোরের প্রতিটি পেজে ভিউ ও ইভেন্ট অটোমেটিক ট্র্যাক হবে।</div>
                        </div>

                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="p-3 bg-light border rounded-3 w-100">
                                <div class="fw-bold text-dark small mb-1"><i class="fas fa-plug text-primary me-1"></i> গুগল ট্যাগ ম্যানেজার ও টিকটক পিক্সেল লাগবে?</div>
                                <p class="text-muted small mb-2" style="font-size: 12px;">GTM, Google Analytics 4, TikTok Pixel ও কাস্টম স্ক্রিপ্ট যুক্ত করতে ইন্টিগ্রেশন পেজ ব্যবহার করুন।</p>
                                <a href="{{ route('admin.integrations.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="fas fa-external-link-alt me-1"></i> ইন্টিগ্রেশন সেটিংসে যান
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- ==========================================
             7. POST-PURCHASE UPSELL (THANK YOU PAGE)
        =========================================== -->
        <input type="hidden" name="upsell_settings_submitted" value="1">
        <div class="card border bg-white rounded-3 shadow-xs overflow-hidden">
            <div class="card-header bg-white p-3 p-sm-4 settings-collapse-header d-flex align-items-center justify-content-between"
                 data-bs-toggle="collapse" 
                 data-bs-target="#secUpsell" 
                 aria-expanded="false"
                 role="button">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 18px;">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">৭. পোস্ট-পারচেজ ১-ক্লিক আপসেল (Post-Purchase Upsell)</h6>
                        <span class="text-muted small">থ্যাঙ্ক ইউ পেজে ১-ক্লিকে অতিরিক্ত প্রোডাক্ট অর্ডারে যুক্ত করার বিশেষ সুবিধা</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(($settings['upsell_enabled'] ?? '0') == '1')
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1 small">সক্রিয় (Active)</span>
                    @else
                        <span class="badge bg-light text-muted border px-2 py-1 small">নিষ্ক্রিয় (Disabled)</span>
                    @endif
                    <i class="fas fa-chevron-down text-muted accordion-arrow"></i>
                </div>
            </div>

            <div id="secUpsell" class="collapse">
                <div class="card-body p-3 p-sm-4 border-top">
                    <!-- Toggle switch -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4 border">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="fas fa-bolt text-warning me-1"></i> পোস্ট-পারচেজ আপসেল ফিচার চালু রাখুন
                            </div>
                            <div class="text-muted small">চালু থাকলে কাস্টমার অর্ডার সফল হওয়ার পর থ্যাঙ্ক ইউ পেজে ১-ক্লিকে অতিরিক্ত প্রোডাক্ট পার্সেলে যুক্ত করতে পারবেন। বন্ধ থাকলে শুধু সাধারণ অর্ডার কনফার্মেশন দেখাবে।</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0 ms-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="upsell_enabled" id="upsell_enabled" value="1" {{ ($settings['upsell_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="upsell_badge_text" class="form-label small fw-bold text-dark mb-1">আপসেল অফার ব্যাজ / টাইপ টেক্সট (Offer Badge Text)</label>
                            <input type="text" name="upsell_badge_text" id="upsell_badge_text" class="form-control" value="{{ old('upsell_badge_text', $settings['upsell_badge_text'] ?? 'স্পেশাল অফার — ০৳ অতিরিক্ত ডেলিভারি চার্জ') }}" placeholder="যেমন: স্পেশাল অফার — ০৳ অতিরিক্ত ডেলিভারি চার্জ">
                            <div class="form-text small">থ্যাঙ্ক ইউ পেজের আপসেল বক্সের শীর্ষে ছোট পিল ব্যাজে প্রদর্শিত অফারের টাইপ বা সংক্ষিপ্ত অফার টেক্সট (খালি রাখলে ব্যাজটি লুকানো থাকবে)।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="upsell_heading" class="form-label small fw-bold text-dark mb-1">আপসেল সেকশন হেডিং (Title)</label>
                            <input type="text" name="upsell_heading" id="upsell_heading" class="form-control" value="{{ old('upsell_heading', $settings['upsell_heading'] ?? '🎉 আপনার জন্য স্পেশাল অফার! একই ডেলিভারিতে যুক্ত করুন') }}" placeholder="যেমন: 🎉 আপনার জন্য স্পেশাল অফার!">
                            <div class="form-text small">থ্যাঙ্ক ইউ পেজে আপসেল প্রোডাক্টের উপরে বড় শিরোনাম।</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="upsell_subtitle" class="form-label small fw-bold text-dark mb-1">আপসেল সাব-টাইটেল (Subtitle / Offer Text)</label>
                            <input type="text" name="upsell_subtitle" id="upsell_subtitle" class="form-control" value="{{ old('upsell_subtitle', $settings['upsell_subtitle'] ?? 'অতিরিক্ত কোনো ডেলিভারি চার্জ ছাড়াই ১ ক্লিকে আপনার পার্সেলে যোগ করুন') }}" placeholder="যেমন: একই ডেলিভারি চার্জে আরও প্রোডাক্ট নিন">
                            <div class="form-text small">অফারের বিস্তারিত বা অতিরিক্ত ডেলিভারি ফ্রি সংক্রান্ত তথ্য।</div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label small fw-bold text-dark mb-0">
                                    নির্দিষ্ট আপসেল প্রোডাক্ট সিলেক্ট করুন (সর্বোচ্চ ৩টি প্রোডাক্ট)
                                </label>
                                <span class="badge bg-secondary-subtle text-secondary small fw-medium" id="upsellCountBadge">
                                    {{ count($selectedUpsellProductIds) }}/৩ সিলেক্টেড
                                </span>
                            </div>

                            <div class="alert alert-light border small text-muted mb-3 py-2 px-3">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                <strong>স্মার্ট ক্যাটাগরি ফলব্যাক:</strong> আপনি যদি এখানে কোনো নির্দিষ্ট প্রোডাক্ট সিলেক্ট না করেন (অথবা কাস্টমার ইতিমধ্যে এই প্রোডাক্টটি অর্ডার করে থাকে), তাহলে সিস্টেম স্বয়ংক্রিয়ভাবে কাস্টমারের অর্ডারের ক্যাটাগরি থেকে ৩টি প্রাসঙ্গিক (Related) প্রোডাক্ট থ্যাঙ্ক ইউ পেজে দেখাবে।
                            </div>

                            <!-- Product selection list with search and max 3 limit -->
                            <div class="border rounded-3 p-3 bg-white" style="max-height: 280px; overflow-y: auto;">
                                <input type="text" id="upsellProductSearch" class="form-control form-control-sm mb-3" placeholder="🔍 প্রোডাক্ট খুঁজুন...">
                                <div class="row g-2" id="upsellProductsList">
                                    @forelse($allProducts as $prod)
                                        <div class="col-12 col-md-6 upsell-product-item" data-name="{{ strtolower($prod->name) }}">
                                            <label class="d-flex align-items-center gap-2 p-2 rounded border bg-light cursor-pointer mb-0 h-100 hover-shadow-sm">
                                                <input class="form-check-input upsell-checkbox mt-0" type="checkbox" name="upsell_product_ids[]" value="{{ $prod->id }}" {{ in_array($prod->id, $selectedUpsellProductIds) ? 'checked' : '' }}>
                                                <img src="{{ $prod->primary_image_url }}" alt="" class="rounded" style="width: 38px; height: 38px; object-fit: cover;">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="text-truncate fw-semibold small text-dark">{{ $prod->name }}</div>
                                                    <div class="small text-muted">{{ number_format($prod->final_price, 0) }} ৳</div>
                                                </div>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small p-2">কোনো প্রোডাক্ট পাওয়া যায়নি।</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Save Action Bar -->
    <div class="card border bg-white rounded-3 shadow-xs p-3 p-sm-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
        <div class="text-muted small">
            <i class="fas fa-shield-alt text-success me-1"></i> পরিবর্তন করার পর অবশ্যই নিচের সেভ বাটনে ক্লিক করুন।
        </div>
        <button type="submit" class="btn btn-admin-primary btn-lg px-5 py-2 fw-bold shadow-sm">
            <i class="fas fa-save me-2"></i> সকল সেটিংস সংরক্ষণ করুন
        </button>
    </div>
</form>
@endsection

@push('styles')
<style>
    .settings-collapse-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
    }
    .settings-collapse-header:hover {
        background-color: #f8fafc !important;
    }
    .accordion-arrow {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
    }
    .settings-collapse-header:not(.collapsed) .accordion-arrow {
        transform: rotate(180deg);
        color: var(--admin-primary) !important;
    }
    .settings-collapse-header.collapsed .accordion-arrow {
        transform: rotate(0deg);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Expand / Collapse All Sections Logic
        const btnToggleAll = document.getElementById('btnToggleAll');
        const toggleAllIcon = document.getElementById('toggleAllIcon');
        const toggleAllText = document.getElementById('toggleAllText');
        const collapseElements = document.querySelectorAll('#settingsSections .collapse');
        const collapseHeaders = document.querySelectorAll('.settings-collapse-header');

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

        // 2. Theme & Brand Color Live Picker
        const pickerPrimary = document.getElementById('picker_primary');
        const inputPrimary = document.getElementById('theme_primary_color');
        const pickerSecondary = document.getElementById('picker_secondary');
        const inputSecondary = document.getElementById('theme_secondary_color');

        const previewBadge = document.getElementById('preview_badge');
        const previewPrice = document.getElementById('preview_price');
        const previewBtn = document.getElementById('preview_btn');

        function updateLivePreview() {
            const p = inputPrimary ? inputPrimary.value || '#f13124' : '#f13124';
            const s = inputSecondary ? inputSecondary.value || '#c9251a' : '#c9251a';
            if (previewBadge) previewBadge.style.background = p;
            if (previewPrice) previewPrice.style.color = p;
            if (previewBtn) previewBtn.style.background = `linear-gradient(135deg, ${p} 0%, ${s} 100%)`;
        }

        if (pickerPrimary && inputPrimary) {
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
        }

        if (pickerSecondary && inputSecondary) {
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
        }

        document.querySelectorAll('.preset-color-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const p = this.dataset.primary;
                const s = this.dataset.secondary;
                if (inputPrimary && pickerPrimary) {
                    inputPrimary.value = p;
                    pickerPrimary.value = p;
                }
                if (inputSecondary && pickerSecondary) {
                    inputSecondary.value = s;
                    pickerSecondary.value = s;
                }
                updateLivePreview();
            });
        });

        // 3. Upsell Products Selector (Limit to 3 and Search)
        const upsellCheckboxes = document.querySelectorAll('.upsell-checkbox');
        const upsellCountBadge = document.getElementById('upsellCountBadge');
        const upsellProductSearch = document.getElementById('upsellProductSearch');
        const upsellItems = document.querySelectorAll('.upsell-product-item');

        function updateUpsellBadge() {
            const checkedCount = document.querySelectorAll('.upsell-checkbox:checked').length;
            if (upsellCountBadge) {
                upsellCountBadge.textContent = `${checkedCount}/৩ সিলেক্টেড`;
                if (checkedCount >= 3) {
                    upsellCountBadge.className = 'badge bg-warning text-dark small fw-medium';
                } else if (checkedCount > 0) {
                    upsellCountBadge.className = 'badge bg-success text-white small fw-medium';
                } else {
                    upsellCountBadge.className = 'badge bg-secondary-subtle text-secondary small fw-medium';
                }
            }
        }

        upsellCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const checked = document.querySelectorAll('.upsell-checkbox:checked');
                if (checked.length > 3) {
                    this.checked = false;
                    alert('আপনি সর্বোচ্চ ৩টি আপসেল প্রোডাক্ট নির্বাচন করতে পারবেন।');
                }
                updateUpsellBadge();
            });
        });

        if (upsellProductSearch) {
            upsellProductSearch.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                upsellItems.forEach(item => {
                    const name = item.dataset.name || '';
                    if (!term || name.includes(term)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            });
        }
    });
</script>
@endpush
