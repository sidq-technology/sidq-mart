@extends('layouts.admin')

@section('title', 'পণ্য সম্পাদনা: ' . $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> পণ্যের তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">পণ্য সম্পাদনা (Edit Product)</h3>
    </div>
</div>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Main Form Left -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">পণ্যের নাম (Product Title) <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-bold">সংক্ষিপ্ত বিবরণ (Short Description)</label>
                    <textarea name="short_description" id="short_description" rows="3" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">বিস্তারিত বিবরণ (Full Description)</label>
                    <textarea name="description" id="description" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
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
                            <input type="number" name="regular_price" id="regular_price" class="form-control" value="{{ old('regular_price', $product->regular_price) }}" min="0" step="1" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="sale_price" class="form-label fw-bold">অফার মূল্য (Sale Price)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="sale_price" id="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="1">
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="stock_quantity" class="form-label fw-bold">স্টক পরিমাণ (Stock) <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="sku" class="form-label fw-bold">SKU / পণ্য কোড</label>
                        <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                </div>
            </div>

            <!-- Product Variations (Colors & Sizes) -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">কালার ও সাইজ ভেরিয়েশন (Product Variations)</h5>
                        <p class="text-muted small mb-0">পণ্যটিতে ভিন্ন ভিন্ন কালার বা সাইজ থাকলে এটি চালু করুন। ফ্রন্টএন্ডে স্বয়ংক্রিয়ভাবে অপশন দেখাবে।</p>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" name="has_variants" id="has_variants" value="1" {{ old('has_variants', $product->has_variants) ? 'checked' : '' }} onchange="toggleVariantsSection()">
                    </div>
                </div>

                <div id="variantsContainer" class="{{ old('has_variants', $product->has_variants) ? '' : 'd-none' }}">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-magic text-danger me-1"></i> দ্রুত ভেরিয়েন্ট তৈরির টুল (Quick Generator)</h6>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-bold">কালারসমূহ (কমা দিয়ে লিখুন):</label>
                                <input type="text" id="generatorColors" class="form-control form-control-sm" placeholder="যেমন: লাল, কালো, নীল, সাদা">
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-bold">সাইজ নির্বাচন / লিখুন:</label>
                                <div class="d-flex flex-wrap gap-1 mb-1" id="sizePresetBadges">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('Free Size')">Free Size</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('S')">S</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('M')">M</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('L')">L</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('XL')">XL</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="addPresetSize('XXL')">XXL</button>
                                </div>
                                <input type="text" id="generatorSizes" class="form-control form-control-sm" placeholder="যেমন: S, M, L, XL">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm w-100 fw-bold" onclick="generateVariantMatrix()">
                                    <i class="fas fa-bolt me-1"></i> তৈরি করুন
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-sm" id="variantTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 130px;">কালার (Color)</th>
                                    <th style="min-width: 60px;">কালার কোড</th>
                                    <th style="min-width: 100px;">সাইজ (Size)</th>
                                    <th style="min-width: 110px;">মূল্য (৳)</th>
                                    <th style="min-width: 90px;">স্টক *</th>
                                    <th style="min-width: 140px;">ছবি (Image)</th>
                                    <th style="width: 40px; text-align: center;">মুছুন</th>
                                </tr>
                            </thead>
                            <tbody id="variantTableBody">
                                @foreach($product->allVariants as $idx => $v)
                                <tr class="variant-row">
                                    <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $v->id }}">
                                    <td>
                                        <input type="text" name="variants[{{ $idx }}][color]" class="form-control form-control-sm" value="{{ $v->color }}" placeholder="যেমন: লাল">
                                    </td>
                                    <td class="text-center">
                                        <input type="color" name="variants[{{ $idx }}][color_code]" class="form-control form-control-color form-control-sm mx-auto" value="{{ $v->color_code ?: '#dc2626' }}" title="কালার প্রিভিউ">
                                    </td>
                                    <td>
                                        <input type="text" name="variants[{{ $idx }}][size]" class="form-control form-control-sm" value="{{ $v->size }}" placeholder="যেমন: XL">
                                    </td>
                                    <td>
                                        <input type="number" name="variants[{{ $idx }}][price]" class="form-control form-control-sm" value="{{ $v->price }}" placeholder="আলাদা মূল্য" min="0" step="1">
                                    </td>
                                    <td>
                                        <input type="number" name="variants[{{ $idx }}][stock_quantity]" class="form-control form-control-sm" value="{{ $v->stock_quantity }}" min="0" required>
                                    </td>
                                    <td>
                                        @if($v->image_path)
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <img src="{{ $v->image_url }}" alt="Variant Image" class="rounded border" style="width: 28px; height: 28px; object-fit: cover;">
                                            <span class="small text-muted" style="font-size: 11px;">বর্তমান ছবি</span>
                                        </div>
                                        @endif
                                        <input type="file" name="variants[{{ $idx }}][image]" class="form-control form-control-sm" accept="image/*">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeVariantRow(this)" title="মুছুন">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addEmptyVariantRow()">
                            <i class="fas fa-plus me-1"></i> আরো একটি ভেরিয়েন্ট সারি যোগ করুন
                        </button>
                        <span class="small text-muted">টিপস: মূল্য ফাঁকা রাখলে পণ্যের মূল মূল্য প্রযোজ্য হবে।</span>
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
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_flash_sale" id="is_flash_sale" value="1" {{ old('is_flash_sale', $product->is_flash_sale) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger" for="is_flash_sale">⚡ ফ্ল্যাশ সেলে প্রদর্শন করুন</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="is_featured">⭐ ফিচার্ড পণ্য করুন</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="is_active">সক্রিয় (Active)</label>
                </div>
            </div>

            <!-- Media Upload Box -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark">পণ্যের ছবি (Images)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="window.SIDQMediaPicker.open('primary', false)">
                        <i class="fas fa-photo-film me-1"></i> মিডিয়া লাইব্রেরি
                    </button>
                </div>

                <!-- 1. Primary Image Section -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label fw-bold d-flex align-items-center justify-content-between mb-2">
                        <span>প্রধান ছবি (Primary Image)</span>
                    </label>

                    @if($product->primary_image)
                    <div class="mb-3 p-2 bg-light border rounded-3 d-flex align-items-center gap-2">
                        <img src="{{ $product->primary_image_url }}" alt="Primary Image" class="rounded border" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.png') }}'" referrerpolicy="no-referrer">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">বর্তমান প্রধান ছবি:</small>
                            <span class="fw-semibold text-dark small text-truncate d-block" style="max-width: 180px;">{{ basename($product->primary_image) }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="small fw-semibold text-muted mb-2">ছবি পরিবর্তন করতে নিচের যেকোনো একটি অপশন ব্যবহার করুন:</div>

                    <!-- Selection Methods Pills -->
                    <ul class="nav nav-pills nav-fill mb-2 p-1 bg-light rounded-3" id="primaryImgTabs" role="tablist" style="font-size: 12px;">
                        <li class="nav-item">
                            <button class="nav-link active py-1" id="p-file-tab" data-bs-toggle="pill" data-bs-target="#p-file-pane" type="button">
                                <i class="fas fa-upload me-1"></i> ফাইল আপলোড
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1" id="p-url-tab" data-bs-toggle="pill" data-bs-target="#p-url-pane" type="button">
                                <i class="fas fa-link me-1"></i> ইমেজ URL
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1 text-primary fw-bold" type="button" onclick="window.SIDQMediaPicker.open('primary', false)">
                                <i class="fas fa-images me-1"></i> মিডিয়া লাইব্রেরি
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- File Upload Pane -->
                        <div class="tab-pane fade show active" id="p-file-pane">
                            <div class="p-3 text-center border rounded-3 bg-white position-relative drop-zone-hover" id="primaryImageDropZone" style="border: 2px dashed #94a3b8 !important; cursor: pointer; transition: all 0.2s ease;">
                                <input type="file" name="primary_image" id="primary_image" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*" style="cursor: pointer; z-index: 5;">
                                <div style="pointer-events: none;">
                                    <i class="fas fa-cloud-arrow-up text-primary fs-3 mb-1 d-block"></i>
                                    <div class="fw-bold small text-dark">ছবি ড্রপ করুন বা ব্রাউজ করুন</div>
                                    <div class="text-muted" style="font-size: 11px;">ড্র্যাগ & ড্রপ অথবা ক্লিপবোর্ড থেকে পেস্ট (Ctrl + V)</div>
                                </div>
                            </div>
                        </div>
                        <!-- URL Input Pane -->
                        <div class="tab-pane fade" id="p-url-pane">
                            <div class="input-group">
                                <input type="url" name="primary_image_url" id="primary_image_url" class="form-control" placeholder="https://example.com/product-image.jpg">
                                <button class="btn btn-outline-secondary" type="button" id="btnPreviewPrimaryUrl">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text small" style="font-size: 11px;">অনলাইনের যেকোনো ইমেজের লিংক দিলে সেটি সরাসরি সেভ হবে।</div>
                        </div>
                    </div>

                    <!-- Hidden Media Library Selection Input -->
                    <input type="hidden" name="primary_image_media" id="primaryImageMediaInput">

                    <!-- Selected Primary Image Preview Box -->
                    <div id="primaryImagePreviewBox" class="mt-2 p-2 bg-light border rounded-3 d-none align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img id="primaryImagePreviewImg" src="" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <small class="fw-bold text-dark d-block text-truncate" id="primaryImagePreviewName" style="max-width: 170px;">New Image</small>
                                <span class="badge bg-success-subtle text-success" style="font-size: 10px;">নতুন ছবি সিলেক্টেড</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill" onclick="clearPrimarySelection()" style="font-size: 11px;">
                            <i class="fas fa-times me-1"></i> সরান
                        </button>
                    </div>
                </div>

                <!-- 2. Gallery Images Section -->
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0">গ্যালারি ছবি (Gallery Images)</label>
                        <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill fw-semibold" style="font-size: 11px;" onclick="window.SIDQMediaPicker.open('gallery', true)">
                            <i class="fas fa-photo-film me-1"></i> মিডিয়া থেকে যোগ করুন
                        </button>
                    </div>

                    <!-- Gallery Options Tabs -->
                    <ul class="nav nav-pills nav-fill mb-2 p-1 bg-light rounded-3" id="galleryImgTabs" role="tablist" style="font-size: 12px;">
                        <li class="nav-item">
                            <button class="nav-link active py-1" id="g-file-tab" data-bs-toggle="pill" data-bs-target="#g-file-pane" type="button">
                                <i class="fas fa-upload me-1"></i> ফাইল আপলোড
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1" id="g-url-tab" data-bs-toggle="pill" data-bs-target="#g-url-pane" type="button">
                                <i class="fas fa-link me-1"></i> ইমেজ URLs
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mb-3">
                        <div class="tab-pane fade show active" id="g-file-pane">
                            <div class="p-3 text-center border rounded-3 bg-white position-relative drop-zone-hover" id="galleryImagesDropZone" style="border: 2px dashed #94a3b8 !important; cursor: pointer; transition: all 0.2s ease;">
                                <input type="file" name="gallery_images[]" id="gallery_images" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*" multiple style="cursor: pointer; z-index: 5;">
                                <div style="pointer-events: none;">
                                    <i class="fas fa-images text-primary fs-3 mb-1 d-block"></i>
                                    <div class="fw-bold small text-dark">একাধিক ছবি ড্রপ করুন বা ব্রাউজ করুন</div>
                                    <div class="text-muted" style="font-size: 11px;">ড্র্যাগ & ড্রপ অথবা ক্লিপবোর্ড থেকে পেস্ট (Ctrl + V)</div>
                                </div>
                            </div>
                            <div class="form-text small mt-1" style="font-size: 11px;">ডিভাইস থেকে অতিরিক্ত গ্যালারি ছবি আপলোড করুন।</div>
                        </div>
                        <div class="tab-pane fade" id="g-url-pane">
                            <textarea name="gallery_image_urls" id="gallery_image_urls" class="form-control" rows="2" placeholder="প্রতি লাইনে ১টি করে Image URL দিন...&#10;https://example.com/img1.jpg&#10;https://example.com/img2.jpg"></textarea>
                            <div class="form-text small" style="font-size: 11px;">একাধিক ইমেজ লিংক প্রতি লাইনে অথবা কমা দিয়ে লিখুন।</div>
                        </div>
                    </div>

                    <!-- Selected Gallery Media Chips Container -->
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px;">মিডিয়া থেকে নতুন নির্বাচিত ছবিসমূহ:</small>
                        <div id="selectedGalleryMediaContainer" class="d-flex flex-wrap gap-2 min-vh-10 p-2 bg-light border rounded-3">
                            <span class="text-muted small align-self-center empty-gallery-note" id="emptyGalleryNote" style="font-size: 11px;">কোনো মিডিয়া ছবি সিলেক্ট করা নেই। উপরের 'মিডিয়া থেকে যোগ করুন' বাটনে ক্লিক করুন।</span>
                        </div>
                    </div>

                    <!-- Existing Gallery Images -->
                    @if($product->galleryImages->count() > 0)
                    <div>
                        <label class="form-label small fw-bold text-dark">বর্তমান গ্যালারি ছবিসমূহ (Current Gallery):</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->galleryImages as $img)
                            <div class="position-relative border rounded p-1 bg-white shadow-xs" id="gallery-img-{{ $img->id }}" style="width: 70px; height: 70px;">
                                <img src="{{ $img->image_url }}" alt="Gallery" class="w-100 h-100 rounded" style="object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.png') }}'" referrerpolicy="no-referrer">
                                <button type="button" 
                                        class="btn btn-danger btn-xs position-absolute top-0 end-0 p-0 rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width: 18px; height: 18px; transform: translate(30%, -30%);" 
                                        onclick="deleteGalleryImage({{ $img->id }})" 
                                        title="ছবি মুছুন">
                                    <i class="fas fa-times" style="font-size: 9px;"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-3 fw-bold fs-6">
                <i class="fas fa-save me-1"></i> আপডেট সংরক্ষণ করুন
            </button>
        </div>
    </div>
</form>

@push('styles')
<!-- Summernote BS5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
.note-editor.note-frame {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    overflow: hidden;
}
.note-editor .note-toolbar {
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 8px !important;
}
.note-editor .note-statusbar {
    background-color: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
}
.note-btn {
    border-radius: 4px !important;
}
</style>
@endpush

@push('scripts')
<!-- jQuery & Summernote BS5 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script>
$(document).ready(function() {
    $('#description').summernote({
        placeholder: 'পণ্যের সুযোগ সুবিধা, বৈশিষ্ট্য, ছবি, তালিকা (Bullets / Numbers) এবং বিস্তারিত বিবরণ লিখুন...',
        tabsize: 2,
        height: 350,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    uploadSummernoteImage(files[i], this);
                }
            }
        }
    });

    function uploadSummernoteImage(file, editor) {
        const data = new FormData();
        data.append('image', file);
        data.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('admin.products.upload-description-image') }}",
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response && response.url) {
                    $(editor).summernote('insertImage', response.url, function($image) {
                        $image.addClass('img-fluid rounded my-2');
                    });
                }
            },
            error: function() {
                // Fallback to Base64
                const reader = new FileReader();
                reader.onloadend = function() {
                    $(editor).summernote('insertImage', reader.result);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Variant Matrix Management
let variantIndex = {{ $product->allVariants->count() }};

function toggleVariantsSection() {
    const isChecked = document.getElementById('has_variants').checked;
    const container = document.getElementById('variantsContainer');
    if (isChecked) {
        container.classList.remove('d-none');
        if (document.querySelectorAll('#variantTableBody tr').length === 0) {
            addEmptyVariantRow();
        }
    } else {
        container.classList.add('d-none');
    }
}

function addPresetSize(size) {
    const input = document.getElementById('generatorSizes');
    let current = input.value.split(',').map(s => s.trim()).filter(Boolean);
    if (!current.includes(size)) {
        current.push(size);
        input.value = current.join(', ');
    }
}

function generateVariantMatrix() {
    const colorsRaw = document.getElementById('generatorColors').value;
    const sizesRaw = document.getElementById('generatorSizes').value;

    const colors = colorsRaw.split(',').map(s => s.trim()).filter(Boolean);
    const sizes = sizesRaw.split(',').map(s => s.trim()).filter(Boolean);

    if (colors.length === 0 && sizes.length === 0) {
        alert('অনুগ্রহ করে অন্তত একটি কালার অথবা সাইজ লিখুন।');
        return;
    }

    const defaultStock = document.getElementById('stock_quantity').value || 10;
    const defaultPrice = document.getElementById('sale_price').value || document.getElementById('regular_price').value || '';

    const colorHexMap = {
        'লাল': '#ef4444', 'red': '#ef4444',
        'কালো': '#1e293b', 'black': '#1e293b',
        'নীল': '#3b82f6', 'blue': '#3b82f6',
        'সাদা': '#ffffff', 'white': '#ffffff',
        'সবুজ': '#22c55e', 'green': '#22c55e',
        'হলুদ': '#eab308', 'yellow': '#eab308',
        'গোলাপি': '#ec4899', 'pink': '#ec4899',
        'কমলা': '#f97316', 'orange': '#f97316',
        'বেগুনি': '#a855f7', 'purple': '#a855f7',
        'ধূসর': '#64748b', 'grey': '#64748b', 'gray': '#64748b'
    };

    if (colors.length > 0 && sizes.length > 0) {
        colors.forEach(col => {
            const hex = colorHexMap[col.toLowerCase()] || '#dc2626';
            sizes.forEach(sz => {
                createVariantRowHtml(col, hex, sz, defaultPrice, defaultStock);
            });
        });
    } else if (colors.length > 0) {
        colors.forEach(col => {
            const hex = colorHexMap[col.toLowerCase()] || '#dc2626';
            createVariantRowHtml(col, hex, '', defaultPrice, defaultStock);
        });
    } else if (sizes.length > 0) {
        sizes.forEach(sz => {
            createVariantRowHtml('', '#dc2626', sz, defaultPrice, defaultStock);
        });
    }
}

function addEmptyVariantRow() {
    createVariantRowHtml('', '#dc2626', '', '', 10);
}

function createVariantRowHtml(color = '', colorCode = '#dc2626', size = '', price = '', stock = 10) {
    const tbody = document.getElementById('variantTableBody');
    const idx = variantIndex++;
    const tr = document.createElement('tr');
    tr.className = 'variant-row';
    tr.innerHTML = `
        <td>
            <input type="text" name="variants[${idx}][color]" class="form-control form-control-sm" value="${color}" placeholder="যেমন: লাল">
        </td>
        <td class="text-center">
            <input type="color" name="variants[${idx}][color_code]" class="form-control form-control-color form-control-sm mx-auto" value="${colorCode}" title="কালার প্রিভিউ">
        </td>
        <td>
            <input type="text" name="variants[${idx}][size]" class="form-control form-control-sm" value="${size}" placeholder="যেমন: XL">
        </td>
        <td>
            <input type="number" name="variants[${idx}][price]" class="form-control form-control-sm" value="${price}" placeholder="আলাদা মূল্য" min="0" step="1">
        </td>
        <td>
            <input type="number" name="variants[${idx}][stock_quantity]" class="form-control form-control-sm" value="${stock}" min="0" required>
        </td>
        <td>
            <input type="file" name="variants[${idx}][image]" class="form-control form-control-sm" accept="image/*">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeVariantRow(this)" title="মুছুন">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeVariantRow(btn) {
    const row = btn.closest('tr');
    if (row) {
        row.remove();
    }
}

// Media Selection Helpers
function clearPrimarySelection() {
    const mediaInput = document.getElementById('primaryImageMediaInput');
    const previewBox = document.getElementById('primaryImagePreviewBox');
    const urlInput = document.getElementById('primary_image_url');
    const fileInput = document.getElementById('primary_image');
    if (mediaInput) mediaInput.value = '';
    if (urlInput) urlInput.value = '';
    if (fileInput) fileInput.value = '';
    if (previewBox) {
        previewBox.classList.add('d-none');
        previewBox.classList.remove('d-flex');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const urlInput = document.getElementById('primary_image_url');
    const previewBtn = document.getElementById('btnPreviewPrimaryUrl');
    const fileInput = document.getElementById('primary_image');
    const mediaInput = document.getElementById('primaryImageMediaInput');

    function updateUrlPreview() {
        const val = urlInput ? urlInput.value.trim() : '';
        if (val) {
            // When entering URL, clear file and media library selections
            if (mediaInput) mediaInput.value = '';
            if (fileInput) fileInput.value = '';

            const previewBox = document.getElementById('primaryImagePreviewBox');
            const previewImg = document.getElementById('primaryImagePreviewImg');
            const previewName = document.getElementById('primaryImagePreviewName');

            if (previewImg) {
                previewImg.src = val;
                previewImg.onerror = function() {
                    this.src = '{{ asset('images/placeholder-product.png') }}';
                };
            }
            if (previewName) {
                previewName.textContent = 'URL: ' + (val.length > 25 ? val.substring(0, 25) + '...' : val);
            }
            if (previewBox) {
                previewBox.classList.remove('d-none');
                previewBox.classList.add('d-flex');
            }
        }
    }

    if (previewBtn) previewBtn.addEventListener('click', updateUrlPreview);
    if (urlInput) {
        let timer = null;
        urlInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(updateUrlPreview, 350);
        });
        urlInput.addEventListener('blur', updateUrlPreview);
    }

    function handlePrimaryFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        if (urlInput) urlInput.value = '';
        if (mediaInput) mediaInput.value = '';

        // Switch to file tab
        document.getElementById('p-file-tab')?.click();

        // Assign to file input using DataTransfer
        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        } catch (err) {
            console.warn('DataTransfer assignment:', err);
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const previewBox = document.getElementById('primaryImagePreviewBox');
            const previewImg = document.getElementById('primaryImagePreviewImg');
            const previewName = document.getElementById('primaryImagePreviewName');

            if (previewImg) previewImg.src = e.target.result;
            if (previewName) previewName.textContent = file.name || 'Pasted Image';
            if (previewBox) {
                previewBox.classList.remove('d-none');
                previewBox.classList.add('d-flex');
            }
        };
        reader.readAsDataURL(file);
    }

    // Direct local file change
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handlePrimaryFile(this.files[0]);
            }
        });
    }

    // Drag and Drop Helpers
    function setupDropZone(zoneEl, onDropFiles) {
        if (!zoneEl) return;
        ['dragenter', 'dragover'].forEach(name => {
            zoneEl.addEventListener(name, function (e) {
                e.preventDefault();
                e.stopPropagation();
                zoneEl.style.borderColor = '#2563eb';
                zoneEl.style.backgroundColor = '#eff6ff';
                zoneEl.style.transform = 'scale(1.01)';
            });
        });
        ['dragleave', 'drop'].forEach(name => {
            zoneEl.addEventListener(name, function (e) {
                e.preventDefault();
                e.stopPropagation();
                zoneEl.style.borderColor = '#94a3b8';
                zoneEl.style.backgroundColor = '#ffffff';
                zoneEl.style.transform = 'scale(1)';
            });
        });
        zoneEl.addEventListener('drop', function (e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                onDropFiles(dt.files);
            }
        });
    }

    // Setup Drag and Drop on Primary Dropzone
    setupDropZone(document.getElementById('primaryImageDropZone'), function (files) {
        if (files[0]) handlePrimaryFile(files[0]);
    });

    // Setup Drag and Drop on Gallery Dropzone
    setupDropZone(document.getElementById('galleryImagesDropZone'), function (files) {
        const galleryInput = document.getElementById('gallery_images');
        if (galleryInput && files.length > 0) {
            try {
                const dt = new DataTransfer();
                if (galleryInput.files) {
                    Array.from(galleryInput.files).forEach(f => dt.items.add(f));
                }
                Array.from(files).forEach(f => {
                    if (f.type.startsWith('image/')) dt.items.add(f);
                });
                galleryInput.files = dt.files;
            } catch (err) {
                console.warn('Gallery DataTransfer:', err);
            }
            renderLocalGalleryPreviews(files);
        }
    });

    // Render local preview chips for gallery dropped/chosen files
    function renderLocalGalleryPreviews(files) {
        const cont = document.getElementById('selectedGalleryMediaContainer');
        if (!cont) return;
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const chip = document.createElement('div');
                chip.className = 'position-relative border rounded p-1 bg-white shadow-xs gallery-media-chip';
                chip.style.width = '75px';
                chip.style.height = '75px';
                chip.innerHTML = `
                    <img src="${e.target.result}" class="w-100 h-100 rounded" style="object-fit: cover;">
                    <span class="badge bg-primary position-absolute bottom-0 start-50 translate-middle-x px-1" style="font-size: 8px;">নতুন ফাইল</span>
                `;
                cont.appendChild(chip);
            };
            reader.readAsDataURL(file);
        });
    }

    // Universal Clipboard Paste Listener (Ctrl + V)
    document.addEventListener('paste', function (e) {
        const activeEl = document.activeElement;
        const isTextInput = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA') && activeEl.id !== 'primary_image_url';

        const items = e.clipboardData?.items;
        if (!items) return;

        let imageFile = null;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                imageFile = items[i].getAsFile();
                break;
            }
        }

        if (imageFile) {
            e.preventDefault();
            if (activeEl && (activeEl.closest('#g-file-pane') || activeEl.closest('#galleryImagesDropZone'))) {
                const galleryInput = document.getElementById('gallery_images');
                if (galleryInput) {
                    const dt = new DataTransfer();
                    if (galleryInput.files) {
                        Array.from(galleryInput.files).forEach(f => dt.items.add(f));
                    }
                    dt.items.add(imageFile);
                    galleryInput.files = dt.files;
                    renderLocalGalleryPreviews([imageFile]);
                }
            } else {
                handlePrimaryFile(imageFile);
            }
            return;
        }

        // If user pasted text URL
        const pastedText = e.clipboardData?.getData('text/plain')?.trim();
        if (pastedText && /^(https?:)?\/\/[^\s]+(\.(jpg|jpeg|png|webp|gif|svg|avif)|\?)/i.test(pastedText)) {
            if (!isTextInput || activeEl.id === 'primary_image_url') {
                document.getElementById('p-url-tab')?.click();
                if (urlInput) {
                    urlInput.value = pastedText;
                    updateUrlPreview();
                }
            }
        }
    });

    // Gallery File Input change listener
    const galleryInput = document.getElementById('gallery_images');
    if (galleryInput) {
        galleryInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                renderLocalGalleryPreviews(this.files);
            }
        });
    }

    // Watch gallery chips container to toggle empty note
    const container = document.getElementById('selectedGalleryMediaContainer');
    if (container) {
        const observer = new MutationObserver(() => {
            const chips = container.querySelectorAll('.gallery-media-chip');
            const emptyNote = document.getElementById('emptyGalleryNote');
            if (emptyNote) {
                if (chips.length > 0) {
                    emptyNote.classList.add('d-none');
                } else {
                    emptyNote.classList.remove('d-none');
                }
            }
        });
        observer.observe(container, { childList: true });
    }
});
</script>
@endpush
@endsection
