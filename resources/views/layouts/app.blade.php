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
    </style>

    @if(\App\Models\Setting::get('meta_pixel_id'))
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
        fbq('init', '{{ \App\Models\Setting::get("meta_pixel_id") }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ \App\Models\Setting::get('meta_pixel_id') }}&ev=PageView&noscript=1"/>
    </noscript>
    @endif

    @stack('styles')
</head>
<body>
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

    @stack('scripts')
</body>
</html>
