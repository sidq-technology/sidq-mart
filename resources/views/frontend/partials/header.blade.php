@php
    $categories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
    $cartService = app(\App\Services\CartService::class);
    $cartCount = $cartService->getCount();
    $cartSubtotal = $cartService->getSubtotal();
    $siteLogo = \App\Models\Setting::get('site_logo');
    $siteName = \App\Models\Setting::get('site_name', 'SIDQ MART');
@endphp

<header class="sidq-header">
    <div class="container header-main">
        <div class="row align-items-center g-3">
            <!-- Mobile Menu Toggle & Brand Logo -->
            <div class="col-6 col-lg-3 d-flex align-items-center gap-2">
                <button class="btn btn-outline-dark d-lg-none py-1 px-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuDrawer" aria-label="Toggle Navigation">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <a href="{{ route('home') }}" class="header-logo">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}">
                    @else
                        <span class="fs-4 fw-bold text-danger">{{ $siteName }}</span>
                    @endif
                </a>
            </div>

            <!-- Search Bar -->
            <div class="col-12 col-lg-6 order-3 order-lg-2">
                <form action="{{ route('search') }}" method="GET" class="header-search-form" autocomplete="off">
                    <input type="text" name="q" id="mainSearchInput" class="form-control" placeholder="পণ্য খুঁজুন (Search anything...)" value="{{ request('q') }}" aria-label="Search">
                    <button type="submit" aria-label="Search Button">
                        <i class="fas fa-search"></i>
                    </button>
                    <!-- Live Search Dropdown -->
                    <div id="searchDropdownResults" class="search-results-dropdown"></div>
                </form>
            </div>

            <!-- Cart Widget, Account & Checkout Quick Link -->
            <div class="col-6 col-lg-3 order-2 order-lg-3 d-flex justify-content-end align-items-center gap-2 gap-sm-3">
                <!-- User Account Dropdown / Login Button -->
                @auth
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-1 py-1 px-2 border rounded-pill shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 26px; height: 26px; background: var(--color-brand-accent); font-size: 11px;">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="d-none d-xl-inline fw-semibold text-truncate" style="max-width: 90px;">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="font-size: 13px;">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ auth()->user()->email }}</div>
                            </div>
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('customer.dashboard') }}"><i class="fas fa-user-circle me-2 text-primary"></i> আমার প্রোফাইল</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('customer.orders') }}"><i class="fas fa-box-open me-2 text-success"></i> আমার অর্ডারসমূহ</a></li>
                        @if(auth()->user()->hasAdminAccess())
                        <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-danger"></i> অ্যাডমিন প্যানেল</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2">
                                    <i class="fas fa-sign-out-alt me-2"></i> লগআউট
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2 px-sm-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#customerAuthModal" style="font-size: 13px;">
                    <i class="fas fa-user-circle" style="color: var(--color-brand-accent); font-size: 16px;"></i>
                    <span class="d-none d-sm-inline fw-semibold">লগইন</span>
                </button>
                @endauth

                <!-- Cart Widget -->
                <div class="header-cart-widget" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" role="button" aria-label="View Cart">
                    <div class="header-cart-icon">
                        <i class="fas fa-shopping-basket"></i>
                        <span class="header-cart-badge cart-count-badge">{{ $cartCount }}</span>
                    </div>
                    <div class="d-none d-sm-block text-start lh-1">
                        <div class="text-muted" style="font-size: 11px;">মোট টাকা</div>
                        <strong class="cart-subtotal-val" style="font-size: 14px;">{{ $cartSubtotal }} ৳</strong>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-primary-sidq d-none d-sm-inline-flex py-2 px-3" style="min-height: 40px; font-size: 13px;">
                    অর্ডার করুন
                </a>
            </div>
        </div>
    </div>

    <!-- Desktop Category Navigation Bar -->
    <nav class="sidq-navbar d-none d-lg-block">
        <div class="container">
            <ul class="sidq-nav-list">
                <li class="sidq-nav-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i> হোম</a>
                </li>
                @foreach($categories->take(8) as $cat)
                <li class="sidq-nav-item position-relative dropdown">
                    <a href="{{ route('product.category', $cat->slug) }}" class="{{ $cat->children->count() ? 'dropdown-toggle' : '' }}" @if($cat->children->count()) data-bs-toggle="dropdown" @endif>
                        {{ $cat->name }}
                    </a>
                    @if($cat->children->count())
                    <ul class="dropdown-menu shadow-sm">
                        @foreach($cat->children as $child)
                        <li><a class="dropdown-item py-2" href="{{ route('product.category', $child->slug) }}">{{ $child->name }}</a></li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>

<!-- Mobile Navigation Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenuDrawer" aria-labelledby="mobileMenuDrawerLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-danger" id="mobileMenuDrawerLabel">
            {{ $siteName }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Mobile User Account / Login Bar -->
        <div class="p-3 border-bottom" style="background: #f8fffa;">
            @auth
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background: var(--color-brand-accent);">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="fw-bold small text-dark">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 11px;">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 12px;">প্রোফাইল</a>
            </div>
            @else
            <button type="button" class="btn btn-primary-sidq w-100 py-2 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#customerAuthModal">
                <i class="fas fa-sign-in-alt"></i> লগইন / রেজিস্টার করুন
            </button>
            @endauth
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="{{ route('home') }}" class="d-flex align-items-center py-2 text-dark">
                    <i class="fas fa-home me-2 text-danger"></i> হোম পেজ
                </a>
            </li>
            @foreach($categories as $cat)
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center py-1">
                    <a href="{{ route('product.category', $cat->slug) }}" class="text-dark">
                        {{ $cat->name }}
                    </a>
                    @if($cat->children->count())
                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#cat-collapse-{{ $cat->id }}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    @endif
                </div>
                @if($cat->children->count())
                <div class="collapse ps-3" id="cat-collapse-{{ $cat->id }}">
                    <ul class="list-unstyled pt-1">
                        @foreach($cat->children as $child)
                        <li class="py-1">
                            <a href="{{ route('product.category', $child->slug) }}" class="text-muted text-decoration-none" style="font-size: 13px;">
                                &bull; {{ $child->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
        <div class="p-3 border-top mt-3">
            <a href="{{ route('checkout') }}" class="btn btn-primary-sidq w-100 mb-2">
                <i class="fas fa-shopping-bag me-1"></i> সরাসরি চেকআউট
            </a>
        </div>
    </div>
</div>
