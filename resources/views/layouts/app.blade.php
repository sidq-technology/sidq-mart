<!DOCTYPE html>
<!--
============================================================================================
 Platform     : SIDQ Commerce Engine (Production Enterprise Edition)
 Storefront   : {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }}
 Powered By   : SIDQ Technology (সিদিক টেকনোলজি)
 Architecture : Enterprise E-Commerce Solution Engineered by SIDQ Technology
 Copyright    : Core Software & Source Architecture © SIDQ Technology. All Rights Reserved.
============================================================================================
-->
<html lang="bn" data-engine="SIDQ-Commerce" data-powered-by="SIDQ Technology">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="generator" content="SIDQ Commerce Engine — Powered by SIDQ Technology">
    <meta name="author" content="SIDQ Technology">
    <meta name="designer" content="SIDQ Technology">
    <meta name="application-name" content="{{ \App\Models\Setting::get('site_name', 'SIDQ MART') }} — Powered by SIDQ Technology">
    
    <title>@yield('title', \App\Models\Setting::get('site_name', 'SIDQ MART')) | {{ \App\Models\Setting::get('site_slogan', 'Online Shopping In Bangladesh') }}</title>
    
    <meta name="description" content="{{ \App\Models\Setting::get('site_slogan', 'SIDQ MART Online Shopping In Bangladesh With Home Delivery') }}">
    <link rel="icon" href="{{ \App\Models\Setting::get('site_favicon', asset('favicon.png')) }}">

    <!-- Google Fonts: Rubik + Hind Siliguri (Bengali) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SIDQ Technology UI Tokens & Design System -->
    <link rel="stylesheet" href="{{ asset('css/sidq-ui.css') }}">

    <!-- Dynamic Brand Theme Tokens (Configured in Admin Settings) -->
    <style>
        :root {
            --color-brand-accent: {{ \App\Models\Setting::get('theme_primary_color', '#f13124') }};
            --color-brand-red: {{ \App\Models\Setting::get('theme_primary_color', '#f13124') }};
            --color-action-primary-bg: {{ \App\Models\Setting::get('theme_secondary_color', '#c9251a') }};
            --color-text-error: {{ \App\Models\Setting::get('theme_secondary_color', '#c9251a') }};
        }

        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
            width: 100% !important;
        }

        body.modal-open,
        body.offcanvas-open {
            padding-right: 0 !important;
        }
    </style>

    <!-- Marketing & Analytics Tracking Integrations (Engineered by SIDQ Technology) -->
    @if(\App\Models\Setting::get('gtm_enabled') == '1' && $gtmId = \App\Models\Setting::get('gtm_container_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    <!-- End Google Tag Manager -->
    @endif

    @if(\App\Models\Setting::get('ga4_enabled') == '1' && $ga4Id = \App\Models\Setting::get('ga4_measurement_id'))
    <!-- Google Analytics 4 (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $ga4Id }}');
    </script>
    @endif

    @if((\App\Models\Setting::get('meta_pixel_enabled', '1') == '1') && $pixelId = \App\Models\Setting::get('meta_pixel_id'))
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $pixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/>
    </noscript>
    @endif

    @if(\App\Models\Setting::get('tiktok_pixel_enabled') == '1' && $ttId = \App\Models\Setting::get('tiktok_pixel_id'))
    <!-- TikTok Pixel Code -->
    <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
      ttq.load('{{ $ttId }}');
      ttq.page();
    }(window, document, 'ttq');
    </script>
    @endif

    @if($customHeader = \App\Models\Setting::get('custom_header_scripts'))
    <!-- Custom Injected Header Scripts -->
    {!! $customHeader !!}
    @endif

    @stack('styles')
</head>
<body>
    @if(\App\Models\Setting::get('gtm_enabled') == '1' && $gtmId = \App\Models\Setting::get('gtm_container_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    <!-- Skip to main content for Accessibility (WCAG 2.4.1) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Announcement Bar -->
    @if($notice = \App\Models\Setting::get('notice_text'))
    <div class="top-bar-notice">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="fas fa-bullhorn me-1"></i> {{ $notice }}</span>
            <span class="d-none d-md-inline">
                <i class="fas fa-phone-alt me-1"></i> হটলাইন: <a href="tel:{{ \App\Models\Setting::get('contact_phone') }}" class="text-white text-decoration-none">{{ \App\Models\Setting::get('contact_phone') }}</a>
            </span>
        </div>
    </div>
    @endif

    <!-- Header Section -->
    @include('frontend.partials.header')

    <!-- Flash Notifications -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer Section -->
    @include('frontend.partials.footer')

    <!-- Floating WhatsApp / Contact Button -->
    @if($phone = \App\Models\Setting::get('contact_phone'))
    <a href="https://wa.me/88{{ preg_replace('/[^0-9]/', '', $phone) }}" target="_blank" class="floating-contact-btn" title="Contact on WhatsApp" aria-label="WhatsApp Us">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

    <!-- Offcanvas Cart Drawer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="cartDrawerLabel">
                <i class="fas fa-shopping-basket text-danger me-2"></i> শপিং কার্ট
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column" id="cartDrawerBody">
            <div class="text-center py-5">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Cart & Search JS -->
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Function to update Cart Widget Counters & Drawer
        function updateCartUI(count, subtotal) {
            document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = count);
            document.querySelectorAll('.cart-subtotal-val').forEach(el => el.textContent = `${subtotal} ৳`);
        }

        // Add to Cart via AJAX
        function addToCart(productId, quantity = 1) {
            fetch("{{ route('cart.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCartUI(data.count, data.subtotal);
                    showToast(data.message || 'কার্টে যোগ করা হয়েছে!');
                    loadCartDrawer();
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById('cartDrawer'));
                    offcanvas.show();
                }
            })
            .catch(err => console.error("Error adding to cart:", err));
        }

        // Load Cart Drawer Content
        function loadCartDrawer() {
            fetch("{{ route('cart.drawer') }}")
            .then(res => res.json())
            .then(data => {
                updateCartUI(data.count, data.subtotal);
                const body = document.getElementById('cartDrawerBody');
                if (!data.items || data.items.length === 0) {
                    body.innerHTML = `
                        <div class="text-center py-5 my-auto">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h5>আপনার কার্ট বর্তমানে খালি!</h5>
                            <p class="text-muted">পণ্য কেনার জন্য আমাদের স্টোর ব্রাউজ করুন।</p>
                            <a href="{{ route('home') }}" class="btn btn-primary-sidq mt-2">শপিং করুন</a>
                        </div>
                    `;
                    return;
                }

                let itemsHtml = '<div class="flex-grow-1 overflow-auto pe-1">';
                data.items.forEach(item => {
                    itemsHtml += `
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <img src="${item.image}" alt="${item.name}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="flex-grow-1 me-2">
                                <h6 class="mb-1 text-truncate" style="max-width: 170px; font-size: 13px;">${item.name}</h6>
                                <div class="text-danger fw-bold" style="font-size: 13px;">${item.unit_price} ৳ x ${item.quantity} = ${item.total_price} ৳</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.product_id})" title="Remove item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                });
                itemsHtml += '</div>';

                itemsHtml += `
                    <div class="border-top pt-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold">সর্বমোট মূল্য:</span>
                            <span class="fw-bold fs-5 text-danger">${data.subtotal} ৳</span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('checkout') }}" class="btn btn-primary-sidq py-2">
                                <i class="fas fa-check-circle me-1"></i> সরাসরি অর্ডার করুন (Checkout)
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-secondary-sidq py-2">
                                কার্ট পেজ দেখুন
                            </a>
                        </div>
                    </div>
                `;

                body.innerHTML = itemsHtml;
            });
        }

        function removeFromCart(productId) {
            fetch("{{ route('cart.remove') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                updateCartUI(data.count, data.subtotal);
                loadCartDrawer();
            });
        }

        // Live Search Autocomplete
        const searchInput = document.getElementById('mainSearchInput');
        const searchDropdown = document.getElementById('searchDropdownResults');

        if (searchInput && searchDropdown) {
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                if (query.length < 2) {
                    searchDropdown.style.display = 'none';
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`{{ route('search') }}?q=${encodeURIComponent(query)}`, {
                        headers: { "X-Requested-With": "XMLHttpRequest" }
                    })
                    .then(res => res.json())
                    .then(data => {
                        searchDropdown.innerHTML = data.html;
                        searchDropdown.style.display = 'block';
                    });
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });
        }

        // Simple Toast alert helper
        function showToast(message) {
            const toastEl = document.createElement('div');
            toastEl.className = 'position-fixed bottom-0 start-50 translate-middle-x p-3';
            toastEl.style.zIndex = '9999';
            toastEl.innerHTML = `
                <div class="toast align-items-center text-white bg-dark border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="fas fa-check-circle text-success me-2"></i> ${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(toastEl);
            setTimeout(() => toastEl.remove(), 2500);
        }

        // Setup Cart Drawer listener
        document.getElementById('cartDrawer').addEventListener('show.bs.offcanvas', loadCartDrawer);

        // SIDQ Technology Developer Console Signature
        console.log(
            '%c Powered by SIDQ Technology %c Enterprise E-Commerce Platform ',
            'background: #f13124; color: #ffffff; font-weight: 700; padding: 5px 10px; border-radius: 4px 0 0 4px;',
            'background: #1a1a1a; color: #ffffff; font-weight: 500; padding: 5px 10px; border-radius: 0 4px 4px 0;'
        );
    </script>

    <!-- Customer Authentication Modal Popup -->
    @include('customer.auth.auth-modal')

    <!-- WhatsApp Floating Live Chat Widget (Engineered by SIDQ Technology) -->
    @if(\App\Models\Setting::get('whatsapp_chat_enabled') == '1' && $waNumber = \App\Models\Setting::get('whatsapp_number'))
    @php
        $cleanWa = preg_replace('/[^0-9]/', '', $waNumber);
        $waMsg = urlencode(\App\Models\Setting::get('whatsapp_default_message', 'Hello SIDQ MART! I need assistance with an order.'));
        $waPos = \App\Models\Setting::get('whatsapp_widget_position', 'bottom-right');
    @endphp
    <a href="https://wa.me/{{ $cleanWa }}?text={{ $waMsg }}" target="_blank" rel="noopener noreferrer" 
       class="position-fixed d-flex align-items-center justify-content-center text-white text-decoration-none shadow"
       style="{{ $waPos === 'bottom-left' ? 'left: 20px;' : 'right: 20px;' }} bottom: 85px; z-index: 1040; width: 56px; height: 56px; border-radius: 50%; background: #25d366; transition: transform 0.2s ease, box-shadow 0.2s ease;"
       onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';"
       title="Chat with SIDQ MART on WhatsApp">
        <i class="fab fa-whatsapp" style="font-size: 32px;"></i>
    </a>
    @endif

    @if($customFooter = \App\Models\Setting::get('custom_footer_scripts'))
    <!-- Custom Injected Footer Scripts -->
    {!! $customFooter !!}
    @endif

    @stack('scripts')
</body>
</html>
