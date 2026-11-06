<!DOCTYPE html>
<!--
============================================================================================
 Platform     : SIDQ Commerce Engine — Modern Administration Console
 Powered By   : SIDQ Technology (সিদিক টেকনোলজি)
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

    <title>@yield('title', 'Admin Panel') | {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }} — Powered by SIDQ Technology</title>
    <link rel="icon" href="{{ \App\Models\Setting::get('site_favicon', asset('favicon.png')) }}">

    <!-- Preload Bornomala Bengali Font (Instant Load, No FOUT/Swap) -->
    <link rel="preload" href="{{ asset('fonts/bornomala/bornomala-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/bornomala/bornomala-bold.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Bornomala Bengali Font Local Stylesheet -->
    <style>
        @font-face {
            font-family: 'Bornomala';
            src: url('{{ asset('fonts/bornomala/bornomala-regular.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: block;
        }
        @font-face {
            font-family: 'Bornomala';
            src: url('{{ asset('fonts/bornomala/bornomala-bold.woff2') }}') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: block;
        }
    </style>

    <!-- Google Fonts: Outfit + Hind Siliguri + Rubik -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @php
        $primaryThemeColor = \App\Models\Setting::get('theme_primary_color', '#f13124');
        $secondaryThemeColor = \App\Models\Setting::get('theme_secondary_color', '#c9251a');
        $adminBgTint = \App\Models\Setting::get('admin_bg_tint', '#f8fffa');
    @endphp

    <style>
        :root {
            --admin-primary: {{ $primaryThemeColor }};
            --admin-secondary: {{ $secondaryThemeColor }};
            --admin-sidebar-bg: #ffffff;
            --admin-sidebar-surface: #f8fafc;
            --admin-sidebar-text: #0f172a;
            --admin-sidebar-border: #e2e8f0;
            --admin-mint-bg: {{ $adminBgTint }};
            --admin-mint-subtle: #f0faf4;
            --admin-mint-border: #e2f0e8;
            --admin-card-bg: #ffffff;
            --admin-text-main: #090d16;
            --admin-text-muted: #475569;
            --admin-card-radius: 4px;
        }

        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        body {
            font-family: 'Outfit', 'Bornomala', 'Hind Siliguri', sans-serif;
            font-size: 16px;
            background-color: var(--admin-mint-bg);
            background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.03) 0px, transparent 50%);
            color: var(--admin-text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* High-Contrast Typography */
        .text-muted {
            color: #475569 !important;
        }
        .small, small {
            font-size: 0.925rem;
        }

        /* Sidebar Styling - Distinct Light White Theme with Solid Black Text */
        .admin-sidebar {
            width: 270px;
            background-color: var(--admin-sidebar-bg);
            min-height: 100vh;
            color: var(--admin-sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid var(--admin-sidebar-border);
            box-shadow: 4px 0 20px rgba(15, 23, 42, 0.03);
        }

        .admin-sidebar-brand {
            padding: 20px 24px;
            font-size: 18px;
            font-weight: 700;
            color: #000000;
            border-bottom: 1px solid var(--admin-sidebar-border);
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.3px;
            background: #ffffff;
        }

        .admin-sidebar-brand .brand-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 12px rgba(241, 49, 36, 0.2);
        }

        .admin-nav {
            list-style: none;
            padding: 16px 14px;
            margin: 0;
        }

        .admin-nav-item {
            margin-bottom: 5px;
        }

        .admin-nav-item a {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #0f172a;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            gap: 13px;
            border-radius: 10px;
            transition: all 0.18s ease;
        }

        .admin-nav-item a i {
            width: 20px;
            text-align: center;
            font-size: 16px;
            color: #1e293b;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .admin-nav-item a:hover {
            background-color: #f1f5f9;
            color: #000000;
            transform: translateX(3px);
        }

        .admin-nav-item a:hover i {
            color: var(--admin-primary);
        }

        .admin-nav-item.active a {
            background: #f1f5f9;
            color: var(--admin-primary);
            font-weight: 700;
            border-left: 4px solid var(--admin-primary);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .admin-nav-item.active a i {
            color: var(--admin-primary);
        }

        /* Hover Submenu for Finance & Marketing */
        .admin-nav-dropdown {
            position: relative;
        }

        .admin-submenu {
            list-style: none;
            padding: 4px 0 6px 16px;
            margin: 0;
            display: none;
            transition: all 0.2s ease;
        }

        .admin-nav-dropdown:hover .admin-submenu,
        .admin-nav-dropdown.open .admin-submenu {
            display: block;
        }

        .admin-nav-dropdown:hover .nav-arrow,
        .admin-nav-dropdown.open .nav-arrow {
            transform: rotate(180deg);
        }

        .admin-submenu li a {
            display: flex;
            align-items: center;
            padding: 8px 14px;
            font-size: 14.5px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            border-radius: 8px;
            border-left: none !important;
            transition: all 0.15s ease;
            gap: 10px;
        }

        .admin-submenu li a:hover {
            background: #f1f5f9;
            color: #0f172a;
            transform: translateX(3px);
        }

        .admin-submenu li.active a {
            color: var(--admin-primary);
            font-weight: 700;
            background: rgba(241, 49, 36, 0.08);
        }

        .admin-main {
            margin-left: 270px;
            width: calc(100% - 270px);
            max-width: calc(100% - 270px);
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Glassmorphic Modern Header */
        .admin-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            height: 64px;
            border-bottom: 1px solid var(--admin-mint-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 990;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .admin-content {
            padding: 24px 28px;
            flex-grow: 1;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Modernized Surface Cards & Metric Cards (Lightweight & Crisp Radius) */
        .card,
        .admin-surface-card {
            background: #ffffff;
            border-radius: var(--admin-card-radius, 4px) !important;
            border: 1px solid #eef2f6 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card-header {
            border-top-left-radius: calc(var(--admin-card-radius, 4px) - 1px) !important;
            border-top-right-radius: calc(var(--admin-card-radius, 4px) - 1px) !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .card-footer {
            border-bottom-left-radius: calc(var(--admin-card-radius, 4px) - 1px) !important;
            border-bottom-right-radius: calc(var(--admin-card-radius, 4px) - 1px) !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .metric-card {
            background: #ffffff;
            border-radius: var(--admin-card-radius, 4px) !important;
            padding: 16px 18px;
            border: 1px solid #eef2f6 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
            border-color: #cbd5e1 !important;
        }

        .metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Buttons & Accent styling */
        .btn-admin-primary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-admin-primary:hover {
            opacity: 0.94;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* User Avatar Badge */
        .user-avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
        }

        .avatar-admin {
            background: rgba(16, 185, 129, 0.14);
            color: #047857;
            border: 2px solid #a7f3d0;
        }

        .avatar-customer {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
            border: 2px solid #bfdbfe;
        }

        .table thead th {
            background-color: var(--admin-mint-bg) !important;
            color: #475569;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid var(--admin-mint-border) !important;
            padding: 14px 16px;
        }

        .table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover td {
            background-color: rgba(248, 255, 250, 0.6);
        }

        /* Mobile Sidebar Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 1035;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px !important;
                max-width: 82vw !important;
                border-top-right-radius: 18px;
                border-bottom-right-radius: 18px;
            }
            .admin-sidebar.show {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(15, 23, 42, 0.25);
            }
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .admin-header {
                padding: 0 16px;
            }
            .admin-content {
                padding: 16px 14px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar d-flex flex-column" id="adminSidebar">
        <div class="admin-sidebar-brand">
            <span class="brand-badge">
                <i class="fas fa-shopping-bag"></i>
            </span>
            <div class="d-flex flex-column">
                <span class="fw-bold text-dark text-truncate" style="max-width: 170px;">{{ \App\Models\Setting::get('site_name', 'SIDQ MART') }}</span>
                <span class="text-muted fw-normal" style="font-size: 11px;">Admin Console</span>
            </div>
        </div>

        <ul class="admin-nav flex-grow-1">
            <li class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->canDo('orders.view'))
            <li class="admin-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                    @php $pendingCount = \App\Models\Order::where('order_status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="badge rounded-pill ms-auto" style="background: var(--admin-primary); font-size: 11px;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="admin-nav-item {{ request()->routeIs('admin.failed-orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.failed-orders.index') }}">
                    <i class="fas fa-exclamation-circle text-danger"></i>
                    <span>Failed Orders</span>
                    @php 
                        $failedOrderCount = \App\Models\FailedOrder::where('is_recovered', false)->count(); 
                    @endphp
                    @if($failedOrderCount > 0)
                    <span class="badge rounded-pill ms-auto bg-danger" style="font-size: 11px;">{{ $failedOrderCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            @if(auth()->user()->canDo('products.view'))
            <li class="admin-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->canDo('categories.manage'))
            <li class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->canDo('finance.view') || auth()->user()->canDo('settings.manage'))
            <li class="admin-nav-item admin-nav-dropdown {{ (request()->routeIs('admin.finance.*') || request()->routeIs('admin.marketing.*')) ? 'active open' : '' }}">
                <a href="{{ route('admin.finance.index') }}" class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-wallet"></i>
                        <span>Finance</span>
                    </div>
                    <i class="fas fa-chevron-down nav-arrow small text-muted" style="font-size: 11px;"></i>
                </a>
                <ul class="admin-submenu">
                    <li class="{{ request()->routeIs('admin.finance.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.finance.index') }}">
                            <i class="fas fa-chart-line me-2"></i>
                            <span>Finance Overview</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.marketing.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.marketing.sms') }}">
                            <i class="fas fa-paper-plane me-2 text-danger"></i>
                            <span>SMS &amp; Marketing</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(auth()->user()->canDo('banners.manage'))
            <li class="admin-nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <a href="{{ route('admin.banners.index') }}">
                    <i class="fas fa-images"></i>
                    <span>Banners</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->canDo('coupons.manage'))
            <li class="admin-nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <a href="{{ route('admin.coupons.index') }}">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Coupons</span>
                </a>
            </li>
            @endif

            <!-- Users Management Link -->
            @if(auth()->user()->canDo('users.manage') || auth()->user()->canDo('users.view'))
            <li class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Users &amp; Roles</span>
                </a>
            </li>
            @endif

            <!-- Integrations & Tracking Scripts -->
            @if(auth()->user()->canDo('integrations.manage'))
            <li class="admin-nav-item {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                <a href="{{ route('admin.integrations.index') }}">
                    <i class="fas fa-plug"></i>
                    <span>Integrations</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->canDo('settings.manage'))
            <li class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="admin-nav-item {{ request()->routeIs('admin.security.*') ? 'active' : '' }}">
                <a href="{{ route('admin.security.index') }}">
                    <i class="fas fa-shield-alt text-success"></i>
                    <span>Security</span>
                </a>
            </li>
            @endif

            <li class="border-top my-3" style="border-color: #e2e8f0 !important;"></li>

            <li class="admin-nav-item">
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt text-primary"></i>
                    <span>Visit Store</span>
                </a>
            </li>

            <li class="admin-nav-item {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                <a href="{{ route('admin.support.index') }}">
                    <i class="fas fa-headset text-danger"></i>
                    <span>Help &amp; Support</span>
                </a>
            </li>
        </ul>

        <div class="p-3 border-top text-center small" style="border-color: #e2e8f0 !important; font-size: 11px; color: #1e293b; background: #ffffff;">
            Engineered &amp; Powered by <br><strong class="text-black fw-bold">SIDQ Technology</strong>
        </div>
    </aside>

    <!-- Main Section -->
    <div class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none border" id="sidebarToggle" type="button" aria-label="Toggle Navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="fw-medium text-muted small d-none d-sm-block">
                    <i class="far fa-calendar-alt me-1 text-success"></i> {{ date('l, d F Y') }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-1">
                    <i class="fas fa-store"></i> লাইভ স্টোর
                </a>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border shadow-2xs" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fs-5" style="color: var(--admin-primary);"></i>
                        <div class="text-start lh-1 d-none d-sm-block">
                            <span class="fw-semibold d-block" style="font-size: 13px;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="text-muted" style="font-size: 10px;">{{ auth()->user()->role_title ?? 'Admin' }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold small text-dark">{{ auth()->user()->name ?? 'Administrator' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ auth()->user()->email ?? '' }}</div>
                                <span class="badge mt-1" style="background: var(--admin-mint-subtle); color: var(--admin-primary); font-size: 10px;">{{ auth()->user()->role_title ?? '' }}</span>
                            </div>
                        </li>
                        @if(auth()->user()->canDo('users.manage') || auth()->user()->canDo('users.view'))
                        <li><a class="dropdown-item py-2" href="{{ route('admin.users.index') }}"><i class="fas fa-users-cog me-2 text-muted"></i> ইউজার ম্যানেজমেন্ট</a></li>
                        @endif
                        @if(auth()->user()->canDo('settings.manage'))
                        <li><a class="dropdown-item py-2" href="{{ route('admin.settings.index') }}"><i class="fas fa-sliders-h me-2 text-muted"></i> সাইট সেটিংস</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2">
                                    <i class="fas fa-sign-out-alt me-2"></i> লগআউট
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        @if(auth()->check() && auth()->user()->isDemoAdmin())
        <div class="alert alert-warning border-0 rounded-0 mb-0 py-2 px-3 text-center fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2" style="background: #fffbeb; color: #b45309; font-size: 13px; border-bottom: 1px solid #fde68a !important;">
            <i class="fas fa-shield-alt text-warning fs-5"></i>
            <span><strong>ডেমো মোড (Read-Only Preview):</strong> আপনি অ্যাডমিন প্যানেলের সকল তথ্য ও ফিচার ঘুরে দেখতে পারবেন। কোনো ডাটা তৈরি, পরিবর্তন বা মুছে ফেলা যাবে না।</span>
        </div>
        @endif

        <!-- Content -->
        <div class="admin-content">
            <!-- Flash messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); color: #991b1b;">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>

        <footer class="py-3 px-4 border-top text-center text-muted small" style="background: rgba(255, 255, 255, 0.6); border-color: var(--admin-mint-border) !important;">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }} — Engineered &amp; Powered by <a href="https://sidqtech.com" target="_blank" rel="noopener noreferrer" class="text-dark fw-bold text-decoration-none">SIDQ Technology</a>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            function openSidebar() {
                sidebar.classList.add('show');
                if (backdrop) backdrop.classList.add('show');
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
            }

            if (toggle && sidebar) {
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (sidebar.classList.contains('show')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            // Universal IP Geolocation Lookup Modal Handler
            const ipModalEl = document.getElementById('ipLocationModal');
            if (ipModalEl) {
                const ipBsModal = new bootstrap.Modal(ipModalEl);
                const loader = document.getElementById('ipModalLoader');
                const content = document.getElementById('ipModalContent');
                const errBox = document.getElementById('ipModalError');
                const ipDisplay = document.getElementById('ipModalIp');
                const locDisplay = document.getElementById('ipModalLocation');
                const countryDisplay = document.getElementById('ipModalCountry');
                const ispDisplay = document.getElementById('ipModalIsp');
                const mediumDisplay = document.getElementById('ipModalMedium');
                const badgeDisplay = document.getElementById('ipModalSecurityBadge');
                const localTip = document.getElementById('ipModalLocalTip');
                const mapLink = document.getElementById('ipModalMapLink');
                const demoBtn = document.getElementById('ipModalDemoBtn');

                let currentQueryIp = '';

                // Handle nested modal backdrops
                ipModalEl.addEventListener('show.bs.modal', function () {
                    setTimeout(() => {
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        if (backdrops.length > 1) {
                            backdrops[backdrops.length - 1].style.zIndex = '1060';
                        }
                    }, 15);
                });

                ipModalEl.addEventListener('hidden.bs.modal', function () {
                    if (document.querySelector('.modal.show')) {
                        document.body.classList.add('modal-open');
                    }
                });

                function loadIpInfo(ip, isDemo = false) {
                    currentQueryIp = ip;
                    loader.classList.remove('d-none');
                    content.classList.add('d-none');
                    errBox.classList.add('d-none');
                    localTip.classList.add('d-none');
                    mapLink.classList.add('d-none');

                    const url = '{{ route('admin.orders.ip-lookup') }}?ip=' + encodeURIComponent(ip) + (isDemo ? '&demo=1' : '');

                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        loader.classList.add('d-none');
                        if (data.success) {
                            content.classList.remove('d-none');
                            ipDisplay.textContent = data.ip;
                            locDisplay.textContent = data.location || '—';
                            countryDisplay.textContent = data.country + (data.country_code ? ' (' + data.country_code + ')' : '');
                            ispDisplay.textContent = data.isp || '—';

                            if (data.is_local) {
                                badgeDisplay.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle py-1 px-2';
                                badgeDisplay.textContent = 'লোকাল নেটওয়ার্ক';
                                mediumDisplay.textContent = 'লোকাল লুপব্যাক';
                                localTip.classList.remove('d-none');
                            } else {
                                if (data.is_proxy) {
                                    badgeDisplay.className = 'badge bg-danger text-white py-1 px-2';
                                    badgeDisplay.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Proxy / VPN';
                                } else {
                                    badgeDisplay.className = 'badge bg-success-subtle text-success border border-success-subtle py-1 px-2';
                                    badgeDisplay.innerHTML = '<i class="fas fa-check-circle me-1"></i> রিয়েল কানেকশন';
                                }

                                mediumDisplay.textContent = data.is_mobile ? '📱 মোবাইল ডাটা (Cellular)' : '🌐 ব্রডব্যান্ড / ওয়াইফাই (Broadband)';
                            }

                            if (data.map_url) {
                                mapLink.href = data.map_url;
                                mapLink.classList.remove('d-none');
                            }
                        } else {
                            errBox.textContent = data.message || 'আইপি তথ্য পাওয়া যায়নি।';
                            errBox.classList.remove('d-none');
                        }
                    })
                    .catch(err => {
                        loader.classList.add('d-none');
                        errBox.textContent = 'রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message;
                        errBox.classList.remove('d-none');
                    });
                }

                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.btn-track-ip');
                    if (btn) {
                        const ip = btn.getAttribute('data-ip') || '127.0.0.1';
                        ipBsModal.show();
                        loadIpInfo(ip, false);
                    }
                });

                if (demoBtn) {
                    demoBtn.addEventListener('click', function () {
                        loadIpInfo(currentQueryIp, true);
                    });
                }
            }

            // Global One-Click Text Copy Handler
            document.addEventListener('click', function (e) {
                const copyBtn = e.target.closest('.btn-copy-text');
                if (!copyBtn) return;
                e.preventDefault();

                const textToCopy = (copyBtn.getAttribute('data-copy') || '').trim();
                if (!textToCopy) return;

                const originalHtml = copyBtn.innerHTML;

                const onCopySuccess = function () {
                    copyBtn.innerHTML = '<i class="fas fa-check text-success"></i>';
                    copyBtn.classList.remove('btn-outline-secondary');
                    copyBtn.classList.add('btn-outline-success');
                    setTimeout(function () {
                        copyBtn.innerHTML = originalHtml;
                        copyBtn.classList.remove('btn-outline-success');
                        copyBtn.classList.add('btn-outline-secondary');
                    }, 1500);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy).then(onCopySuccess).catch(function () {
                        fallbackCopyText(textToCopy, onCopySuccess);
                    });
                } else {
                    fallbackCopyText(textToCopy, onCopySuccess);
                }
            });

            function fallbackCopyText(text, cb) {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                ta.style.top = '0';
                ta.setAttribute('readonly', '');
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                    if (cb) cb();
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
                document.body.removeChild(ta);
            }
        });
    </script>

    <!-- Global IP Geolocation Modal -->
    <div class="modal fade" id="ipLocationModal" tabindex="-1" aria-labelledby="ipLocationModalLabel" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Modal Header -->
                <div class="modal-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.12); color: #2563eb; font-size: 16px;">
                            <i class="fas fa-location-crosshairs"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0 fs-6" id="ipLocationModalLabel">আইপি ট্র্যাকিং ও লোকেশন</h6>
                            <span class="text-muted small" style="font-size: 11.5px;">ভৌগোলিক অবস্থান ও ইন্টারনেট প্রোভাইডার</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <!-- Loading Spinner State -->
                    <div id="ipModalLoader" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                        <div class="small text-muted mt-2">আইপি তথ্য অনুসন্ধান করা হচ্ছে...</div>
                    </div>

                    <!-- Error State -->
                    <div id="ipModalError" class="alert alert-danger py-2 px-3 small d-none mb-0"></div>

                    <!-- Content State -->
                    <div id="ipModalContent" class="d-none">
                        <!-- IP Address Banner -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div>
                                <span class="text-muted small d-block" style="font-size: 11px;">টার্গেট আইপি অ্যাড্রেস:</span>
                                <span class="font-monospace fw-bold text-dark fs-5" id="ipModalIp">127.0.0.1</span>
                            </div>
                            <span id="ipModalSecurityBadge" class="badge py-1 px-2 fw-semibold"></span>
                        </div>

                        <!-- Details Grid -->
                        <div class="d-flex flex-column gap-2 mb-3">
                            <!-- Location -->
                            <div class="d-flex align-items-start justify-content-between p-2 rounded-2 bg-light border">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fas fa-map-marker-alt text-danger" style="width: 16px;"></i>
                                    <span>লোকেশন (City/Region):</span>
                                </div>
                                <span class="fw-bold text-dark small text-end" id="ipModalLocation">—</span>
                            </div>

                            <!-- Country -->
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fas fa-globe text-primary" style="width: 16px;"></i>
                                    <span>দেশ (Country):</span>
                                </div>
                                <span class="fw-bold text-dark small text-end" id="ipModalCountry">—</span>
                            </div>

                            <!-- ISP -->
                            <div class="d-flex align-items-start justify-content-between p-2 rounded-2 bg-light border">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fas fa-network-wired text-info" style="width: 16px;"></i>
                                    <span>ISP / অপারেটর:</span>
                                </div>
                                <span class="fw-bold text-dark small text-end text-truncate" style="max-width: 55%;" id="ipModalIsp">—</span>
                            </div>

                            <!-- Connection Medium -->
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fas fa-wifi text-secondary" style="width: 16px;"></i>
                                    <span>কানেকশন মাধ্যম:</span>
                                </div>
                                <span class="fw-bold text-dark small text-end" id="ipModalMedium">ব্রডব্যান্ড / ফিক্সড লাইন</span>
                            </div>
                        </div>

                        <!-- Localhost Tip with Demo Switcher -->
                        <div id="ipModalLocalTip" class="alert alert-info py-2 px-3 small d-none mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-info-circle text-info mt-1"></i>
                                <div>
                                    <strong>লোকালহোস্ট টেস্ট আইপি:</strong> এটি আপনার লোকাল ডেভেলপমেন্ট আইপি।
                                    <div class="mt-1">
                                        <button type="button" class="btn btn-xs btn-primary fw-bold py-1 px-2 rounded-pill" id="ipModalDemoBtn">
                                            <i class="fas fa-eye me-1"></i> বাংলাদেশ আইপিতে ডেমো রেজাল্ট দেখুন
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Maps Link Button -->
                        <a id="ipModalMapLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-3 d-none">
                            <i class="fas fa-map-marked-alt text-danger"></i>
                            <span>গুগল ম্যাপে ভৌগোলিক অবস্থান দেখুন</span>
                            <i class="fas fa-arrow-up-right-from-square small text-muted"></i>
                        </a>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light py-2 px-4 border-top d-flex justify-content-between">
                    <span class="text-muted small" style="font-size: 11px;">
                        <i class="fas fa-shield-halved text-success me-1"></i> লাইভ আইপি জিও-ডিটেকশন
                    </span>
                    <button type="button" class="btn btn-sm btn-secondary px-4 fw-semibold rounded-3" data-bs-dismiss="modal">বন্ধ করুন</button>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
