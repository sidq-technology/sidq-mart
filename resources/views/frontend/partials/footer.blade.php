@php
    $siteName = \App\Models\Setting::get('site_name', 'SIDQ MART');
    $siteLogo = \App\Models\Setting::get('site_logo');
    $phone = \App\Models\Setting::get('contact_phone', '01711223344');
    $email = \App\Models\Setting::get('contact_email', 'support@sidqmart.com');
    $address = \App\Models\Setting::get('contact_address', 'Dhaka, Bangladesh');
    $about = \App\Models\Setting::get('footer_about', 'SIDQ MART হলো বাংলাদেশের একটি নির্ভরযোগ্য অনলাইন শপিং প্ল্যাটফর্ম। আমরা সাশ্রয়ী মূল্যে সর্বোচ্চ মানের গ্যাজেট, কিচেন এবং গৃহস্থালি পণ্য সরবরাহ করি।');
    $categories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->take(6)->get();
@endphp

<!-- Trust & Features Intro Section (SIDQ Technology UI Component) -->
<section class="intro-part">
    <div class="container">
        <div class="row g-3 g-md-4">
            <div class="col-6 col-lg-3">
                <div class="intro-wrap">
                    <div class="intro-icon">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="intro-content">
                        <h5>হাই-কোয়ালিটি পণ্য</h5>
                        <p>Enjoy top quality items for less</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="intro-wrap">
                    <div class="intro-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="intro-content">
                        <h5>24/7 লাইভ চ্যাট</h5>
                        <p>Get instant assistance whenever you need it</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="intro-wrap">
                    <div class="intro-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="intro-content">
                        <h5>এক্সপ্রেস শিপিং</h5>
                        <p>Fast & reliable delivery options</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="intro-wrap">
                    <div class="intro-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="intro-content">
                        <h5>সিকিউর পেমেন্ট</h5>
                        <p>Multiple safe payment methods</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section (Powered by SIDQ Technology) -->
<footer class="sidq-footer" data-powered-by="SIDQ Technology">
    <div class="container">
        <div class="row g-4">
            <!-- Brand & About -->
            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="max-height: 48px;">
                    @else
                        <h4 class="fw-bold mb-0 text-dark">{{ $siteName }}</h4>
                    @endif
                </a>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    {{ $about }}
                </p>
            </div>

            <!-- Contact Us -->
            <div class="col-12 col-sm-6 col-xl-3">
                <h5>Contact Us</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <div class="me-2 text-danger fs-5"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <span class="d-block text-muted" style="font-size: 11px;">হটলাইন নম্বর</span>
                            <a href="tel:{{ $phone }}" class="text-decoration-none fw-bold text-dark fs-6">{{ $phone }}</a>
                        </div>
                    </li>
                    @if($email)
                    <li class="mb-3 d-flex align-items-center">
                        <div class="me-2 text-danger fs-5"><i class="fas fa-envelope"></i></div>
                        <div>
                            <span class="d-block text-muted" style="font-size: 11px;">ইমেইল সাপোর্ট</span>
                            <a href="mailto:{{ $email }}" class="text-decoration-none text-dark">{{ $email }}</a>
                        </div>
                    </li>
                    @endif
                    @if($address)
                    <li class="mb-2 d-flex align-items-start">
                        <div class="me-2 text-danger fs-5 mt-1"><i class="fas fa-map-marker-alt"></i></div>
                        <span class="text-muted">{{ $address }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-sm-6 col-xl-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-decoration-none">হোম পেজ</a></li>
                    <li class="mb-2"><a href="{{ route('checkout') }}" class="text-decoration-none">সরাসরি অর্ডার (Checkout)</a></li>
                    <li class="mb-2"><a href="{{ route('cart.index') }}" class="text-decoration-none">শপিং কার্ট</a></li>
                    @foreach($categories->take(3) as $cat)
                    <li class="mb-2"><a href="{{ route('product.category', $cat->slug) }}" class="text-decoration-none">{{ $cat->name }}</a></li>
                    @endforeach
                    <li class="mb-2"><a href="{{ route('admin.login') }}" class="text-decoration-none">অ্যাডমিন প্যানেল</a></li>
                </ul>
            </div>

            <!-- Our Social Page -->
            <div class="col-12 col-sm-6 col-xl-3 mt-3 mt-sm-0">
                <h5>Our Social Page</h5>
                <div class="d-flex flex-wrap gap-2 pt-1 mb-3">
                    <a href="https://facebook.com" target="_blank" class="footer-social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://instagram.com" target="_blank" class="footer-social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://twitter.com" target="_blank" class="footer-social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://youtube.com" target="_blank" class="footer-social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="mt-2">
                    <span class="badge bg-danger py-2 px-3 fw-normal text-wrap text-start" style="font-size: 13px; line-height: 1.4; display: inline-flex; align-items: center; max-width: 100%; white-space: normal;">
                        <i class="fas fa-truck me-2 flex-shrink-0"></i> <span>ক্যাশ অন ডেলিভারি সারা বাংলাদেশে</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright & Powered By Strip -->
    <div class="footer-bottom-bar text-center">
        <div class="container">
            <span class="d-inline-block small text-wrap" style="max-width: 100%; word-break: break-word;">&copy; {{ date('Y') }} {{ $siteName }}. সর্বস্বত্ব সংরক্ষিত। | Powered by <strong>SIDQ Technology</strong></span>
        </div>
    </div>
</footer>
