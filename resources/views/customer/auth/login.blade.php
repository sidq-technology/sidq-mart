@extends('layouts.app')

@section('title', 'লগইন ও রেজিস্ট্রেশন - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="py-5" style="background: #f8fffa; min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                        <div class="d-inline-flex p-3 rounded-circle text-white mb-2" style="background: var(--color-brand-accent);">
                            <i class="fas fa-user fs-4"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">গ্রাহক অ্যাকাউন্ট (Customer Portal)</h4>
                        <p class="text-muted small">আপনার অ্যাকাউন্টে লগইন করুন অথবা নতুন অ্যাকাউন্ট তৈরি করুন।</p>

                        <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 mt-3" id="pageAuthTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold py-2 rounded-2" id="p-login-tab" data-bs-toggle="pill" data-bs-target="#page-login-pane" type="button" role="tab">
                                    <i class="fas fa-sign-in-alt me-1"></i> লগইন
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-2 rounded-2" id="p-register-tab" data-bs-toggle="pill" data-bs-target="#page-register-pane" type="button" role="tab">
                                    <i class="fas fa-user-plus me-1"></i> রেজিস্টার
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="pageAuthTabContent">
                            <!-- 1. Login Pane -->
                            <div class="tab-pane fade show active" id="page-login-pane" role="tabpanel">
                                <form action="{{ route('login.submit') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ইমেইল অথবা মোবাইল নম্বর <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                                            <input type="text" name="login" class="form-control" placeholder="017XXXXXXXX বা example@domain.com" value="{{ old('login') }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">পাসওয়ার্ড <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="pageRememberMe" checked>
                                            <label class="form-check-label small text-muted" for="pageRememberMe">লগইন মনে রাখুন</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary-sidq w-100 py-3 fw-bold">
                                        <i class="fas fa-sign-in-alt me-1"></i> লগইন করুন
                                    </button>
                                </form>
                            </div>

                            <!-- 2. Register Pane -->
                            <div class="tab-pane fade" id="page-register-pane" role="tabpanel">
                                <form action="{{ route('register.submit') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">আপনার পূর্ণ নাম <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="যেমন: মো: সাইফুল ইসলাম" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ইমেইল ঠিকানা <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" placeholder="example@domain.com" value="{{ old('email') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">মোবাইল নম্বর (ঐচ্ছিক)</label>
                                        <input type="text" name="phone" class="form-control" placeholder="017XXXXXXXX" value="{{ old('phone') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">পাসওয়ার্ড তৈরি করুন <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" placeholder="কমপক্ষে ৬ অক্ষরের পাসওয়ার্ড" minlength="6" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary-sidq w-100 py-3 fw-bold">
                                        <i class="fas fa-user-check me-1"></i> নতুন অ্যাকাউন্ট তৈরি করুন
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 py-3 text-center">
                        <small class="text-muted">
                            Powered by <strong class="text-dark">SIDQ Technology</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
