@extends('layouts.admin')

@section('title', 'ব্যানার সম্পাদনা')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> ব্যানার তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">ব্যানার সম্পাদনা (Edit Banner)</h3>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">ব্যানার ছবি পরিবর্তন করুন</label>
                    <div class="mb-2">
                        <img src="{{ $banner->image_url }}" alt="Banner" class="rounded border" style="width: 160px; height: 80px; object-fit: cover;">
                    </div>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">ব্যানার শিরোনাম (Title)</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $banner->title) }}">
                </div>

                <div class="mb-3">
                    <label for="subtitle" class="form-label fw-bold">সাব-টাইটেল (Subtitle)</label>
                    <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle) }}">
                </div>

                <div class="mb-3">
                    <label for="target_url" class="form-label fw-bold">টার্গেট লিঙ্ক (Target URL)</label>
                    <input type="text" name="target_url" id="target_url" class="form-control" value="{{ old('target_url', $banner->target_url) }}">
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-bold">ডিসপ্লে ক্রম (Sort Order)</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
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
