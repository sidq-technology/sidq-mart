@extends('layouts.admin')

@section('title', 'কুপন সম্পাদনা: ' . $coupon->code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> কুপন তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">কুপন সম্পাদনা (Edit Coupon)</h3>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="code" class="form-label fw-bold">কুপন কোড (Coupon Code) <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $coupon->code) }}" required>
                    @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="type" class="form-label fw-bold">ডিসকাউন্ট ধরন <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>নির্দিষ্ট টাকা (Fixed TK)</option>
                            <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>শতাংশ ছাড় (Percentage %)</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="value" class="form-label fw-bold">ছাড়ের পরিমাণ <span class="text-danger">*</span></label>
                        <input type="number" name="value" id="value" class="form-control" value="{{ old('value', $coupon->value) }}" min="1" step="0.01" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="min_spend" class="form-label fw-bold">ন্যূনতম খরচ (Min Spend)</label>
                        <input type="number" name="min_spend" id="min_spend" class="form-control" value="{{ old('min_spend', $coupon->min_spend) }}" min="0">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="expiry_date" class="form-label fw-bold">মেয়াদ উত্তীর্ণের তারিখ</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ old('expiry_date', $coupon->expiry_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="is_active">সক্রিয় (Active)</label>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
                    <i class="fas fa-save me-1"></i> আপডেট সংরক্ষণ করুন
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
