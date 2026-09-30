@extends('layouts.admin')

@section('title', 'নতুন ব্যানার যুক্ত করুন')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> ব্যানার তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">নতুন স্লাইডার ব্যানার</h3>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">ব্যানার ছবি (Banner Image) <span class="text-danger">*</span></label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text small">প্রস্তাবিত মাপ: ১২০০ x ৪০০ পিক্সেল।</div>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">ব্যানার শিরোনাম (Title)</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="যেমন: মেগা ডিসকাউন্ট অফার...">
                </div>

                <div class="mb-3">
                    <label for="subtitle" class="form-label fw-bold">সাব-টাইটেল (Subtitle)</label>
                    <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ old('subtitle') }}">
                </div>

                <div class="mb-3">
                    <label for="target_url" class="form-label fw-bold">টার্গেট লিঙ্ক (Target URL)</label>
                    <input type="text" name="target_url" id="target_url" class="form-control" value="{{ old('target_url', '#') }}" placeholder="ক্লিক করলে যে পেজে যাবে">
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-bold">ডিসপ্লে ক্রম (Sort Order)</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label fw-bold" for="is_active">সক্রিয় (Active)</label>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
                    <i class="fas fa-save me-1"></i> ব্যানার সংরক্ষণ করুন
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
