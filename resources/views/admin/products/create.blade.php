@extends('layouts.admin')

@section('title', 'নতুন পণ্য যুক্ত করুন - Add Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> পণ্যের তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">নতুন পণ্য যুক্ত করুন (Add New Product)</h3>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Main Form Left -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">পণ্যের নাম (Product Title) <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="যেমন: Breakfast Boiled Egg Mold..." required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-bold">সংক্ষিপ্ত বিবরণ (Short Description)</label>
                    <textarea name="short_description" id="short_description" rows="3" class="form-control" placeholder="পণ্যের সংক্ষেপ বিবরণ লিখুন...">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">বিস্তারিত বিবরণ (Full Description)</label>
                    <textarea name="description" id="description" rows="6" class="form-control" placeholder="পণ্যের সুযোগ সুবিধা, বৈশিষ্ট্য ইত্যাদি বিস্তারিত লিখুন...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Price and Stock Box -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">মূল্য ও স্টক (Pricing & Stock)</h5>
                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <label for="regular_price" class="form-label fw-bold">নিয়মিত মূল্য (Regular Price) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="regular_price" id="regular_price" class="form-control" value="{{ old('regular_price') }}" min="0" step="1" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="sale_price" class="form-label fw-bold">অফার মূল্য (Sale Price)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="sale_price" id="sale_price" class="form-control" value="{{ old('sale_price') }}" min="0" step="1">
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="stock_quantity" class="form-label fw-bold">স্টক পরিমাণ (Stock) <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" value="{{ old('stock_quantity', 50) }}" min="0" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="sku" class="form-label fw-bold">SKU / পণ্য কোড</label>
                        <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku') }}" placeholder="ফাঁকা রাখলে স্বয়ংক্রিয় তৈরি হবে">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-12 col-lg-4">
            <!-- Category & Status -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="mb-3">
                    <label for="category_id" class="form-label fw-bold">ক্যাটেগরি (Category) <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- ক্যাটেগরি নির্বাচন করুন --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_flash_sale" id="is_flash_sale" value="1" {{ old('is_flash_sale') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger" for="is_flash_sale">⚡ ফ্ল্যাশ সেলে প্রদর্শন করুন</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="is_featured">⭐ ফিচার্ড পণ্য করুন</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label fw-bold" for="is_active">সক্রিয় (Active)</label>
                </div>
            </div>

            <!-- Media Upload Box -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">পণ্যের ছবি (Images)</h5>

                <div class="mb-3">
                    <label for="primary_image" class="form-label fw-bold">প্রধান ছবি (Primary Image)</label>
                    <input type="file" name="primary_image" id="primary_image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="gallery_images" class="form-label fw-bold">গ্যালারি ছবি (Gallery Images)</label>
                    <input type="file" name="gallery_images[]" id="gallery_images" class="form-control" accept="image/*" multiple>
                    <div class="form-text small">একাধিক ছবি একসাথে নির্বাচন করতে পারেন।</div>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-3 fw-bold fs-6">
                <i class="fas fa-save me-1"></i> পণ্য সংরক্ষণ করুন
            </button>
        </div>
    </div>
</form>
@endsection
