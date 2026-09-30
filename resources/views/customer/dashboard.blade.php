@extends('layouts.app')

@section('title', 'আমার অ্যাকাউন্ট - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="py-5" style="background: #f8fffa; min-height: 80vh;">
    <div class="container">
        <!-- Top Profile Welcome Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 58px; height: 58px; background: var(--color-brand-accent);">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">স্বাগতম, {{ $user->name }}!</h4>
                        <div class="text-muted small">
                            <i class="far fa-envelope me-1"></i> {{ $user->email }}
                            @if($user->phone)
                                <span class="mx-2">•</span> <i class="fas fa-phone-alt me-1"></i> {{ $user->phone }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-box-open me-1"></i> সকল অর্ডার ({{ $stats['total_orders'] }})
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                            <i class="fas fa-sign-out-alt me-1"></i> লগআউট
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <span class="text-muted small">মোট অর্ডার (Orders)</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ $stats['total_orders'] }}</h3>
                    <small class="text-muted">সর্বমোট দেওয়া অর্ডার</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <span class="text-muted small">প্রক্রিয়াধীন (Pending)</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1">{{ $stats['pending_orders'] }}</h3>
                    <small class="text-warning">ডেলিভারির অপেক্ষায়</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <span class="text-muted small">সফল ডেলিভারি (Delivered)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1">{{ $stats['delivered_orders'] }}</h3>
                    <small class="text-success">সম্পন্ন অর্ডার</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                    <span class="text-muted small">মোট কেনাকাটা (Total Spent)</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">৳{{ number_format($stats['total_spent'], 0) }}</h3>
                    <small class="text-success">ডেলিভারিকৃত অর্ডারে</small>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Orders Section -->
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-shopping-bag me-2 text-danger"></i> সাম্প্রতিক অর্ডারসমূহ
                        </h5>
                        <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold">সবগুলো দেখুন &rarr;</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">অর্ডার নম্বর</th>
                                        <th>তারিখ</th>
                                        <th>মোট টাকা</th>
                                        <th>স্ট্যাটাস</th>
                                        <th class="text-end pe-3">অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="fw-bold text-decoration-none" style="color: var(--color-brand-accent);">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="fw-bold">৳{{ number_format($order->grand_total, 0) }}</td>
                                        <td>
                                            <span class="badge {{ $order->status_badge_class }}">{{ $order->order_status }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-light border text-primary">
                                                <i class="fas fa-eye me-1"></i> বিস্তারিত
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            আপনি এখনও কোনো অর্ডার করেননি। <a href="{{ route('home') }}" class="fw-bold text-danger">শপিং শুরু করুন</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile & Address Update Form -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-user-edit me-2 text-danger"></i> প্রোফাইল ও ডেলিভারি তথ্য
                    </h5>
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">আপনার নাম</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ইমেইল ঠিকানা</label>
                            <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">মোবাইল নম্বর</label>
                            <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $user->phone) }}" placeholder="017XXXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ডিফল্ট ডেলিভারি ঠিকানা</label>
                            <textarea name="address" rows="2" class="form-control form-control-sm" placeholder="বাসা/হোল্ডিং নম্বর, রোড, এলাকা">{{ old('address', $user->address) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">নতুন পাসওয়ার্ড (ঐচ্ছিক)</label>
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="পরিবর্তন না করলে ফাঁকা রাখুন">
                        </div>
                        <button type="submit" class="btn btn-primary-sidq w-100 py-2 fw-bold">
                            <i class="fas fa-save me-1"></i> তথ্য সংরক্ষণ করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
