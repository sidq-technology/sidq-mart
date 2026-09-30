@extends('layouts.admin')

@section('title', 'ক্যাটেগরি সম্পাদনা: ' . $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> ক্যাটেগরি তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">ক্যাটেগরি সম্পাদনা (Edit Category)</h3>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">ক্যাটেগরির নাম <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label fw-bold">প্যারেন্ট ক্যাটেগরি (Parent Category)</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">-- মূল ক্যাটেগরি (Root) --</option>
                        @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">ক্যাটেগরি ছবি পরিবর্তন করুন</label>
                    @if($category->image)
                    <div class="mb-2">
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                    </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-bold">ডিসপ্লে ক্রম (Sort Order)</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_top" id="is_top" value="1" {{ old('is_top', $category->is_top) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger" for="is_top">টপ ক্যাটেগরি হিসেবে হোমপেজে প্রদর্শন করুন</label>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
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
