@extends('layouts.app')

@section('title', 'শপ - সকল পণ্য - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="shop-page-wrapper bg-light py-3 py-md-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>হোম</a></li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">সকল পণ্য (Shop)</li>
            </ol>
        </nav>

        <!-- Page Header Banner -->
        <div class="bg-white rounded-3 shadow-xs p-3 p-md-4 mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="fs-4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-store text-danger"></i> সকল পণ্য (Shop)
                </h1>
                <p class="text-muted small mb-0">
                    আমাদের বাছাইকৃত সেরা মানের গ্যাজেট ও লাইফস্টাইল পণ্য কালেকশন
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold fs-6">
                    মোট পণ্য: <span id="headerTotalBadge" class="text-danger fw-bold">{{ $products->total() }}</span> টি
                </span>
            </div>
        </div>

        <!-- Mobile Filter & Sort Bar (Visible on mobile/tablet < lg) -->
        <div class="d-lg-none bg-white p-2 rounded-3 border shadow-xs mb-3 d-flex align-items-center justify-content-between gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2 py-2" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterDrawer" aria-controls="mobileFilterDrawer">
                <i class="fas fa-filter"></i>
                <span class="fw-bold">ফিল্টার করুন</span>
                <span id="mobileActiveFilterCount" class="badge bg-danger rounded-pill d-none">0</span>
            </button>
            <div class="flex-grow-1">
                <select class="form-select form-select-sm fw-semibold" id="mobileSortSelect" onchange="handleSortChange(this.value)">
                    <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>নতুন পণ্য (Newest)</option>
                    <option value="price_low_high" {{ in_array($sort, ['price_low_high', 'price_asc']) ? 'selected' : '' }}>দাম: কম থেকে বেশি</option>
                    <option value="price_high_low" {{ in_array($sort, ['price_high_low', 'price_desc']) ? 'selected' : '' }}>দাম: বেশি থেকে কম</option>
                    <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>জনপ্রিয় পণ্য</option>
                    <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>নাম: ক - য়</option>
                </select>
            </div>
        </div>

        <div class="row g-3 g-lg-4">
            <!-- Desktop Sidebar Filter (Visible on desktop >= lg) -->
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-xs rounded-3 overflow-hidden sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="fas fa-sliders-h text-danger"></i> ফিল্টারসমূহ
                        </span>
                        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none small" onclick="resetAllFilters()">
                            <i class="fas fa-redo-alt me-1"></i> রিসেট
                        </button>
                    </div>

                    <div class="card-body p-3 shop-filter-body" style="max-height: calc(100vh - 160px); overflow-y: auto;">
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark small text-uppercase mb-2 d-flex justify-content-between align-items-center">
                                <span>ক্যাটেগরি</span>
                                <i class="fas fa-layer-group text-muted"></i>
                            </h6>
                            <div class="list-group list-group-flush border-0">
                                <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                                    <span class="d-flex align-items-center gap-2">
                                        <input type="radio" name="desktop_category" value="" class="form-check-input mt-0" {{ empty($selectedCategory) ? 'checked' : '' }} onchange="selectCategoryFilter('')">
                                        <span class="small fw-semibold {{ empty($selectedCategory) ? 'text-danger' : 'text-dark' }}">সকল ক্যাটেগরি</span>
                                    </span>
                                </label>
                                @foreach($categories as $cat)
                                <div class="category-filter-item">
                                    <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                                        <span class="d-flex align-items-center gap-2 text-truncate" style="max-width: 190px;">
                                            <input type="radio" name="desktop_category" value="{{ $cat->slug }}" class="form-check-input mt-0" {{ $selectedCategory == $cat->slug ? 'checked' : '' }} onchange="selectCategoryFilter('{{ $cat->slug }}')">
                                            <span class="small {{ $selectedCategory == $cat->slug ? 'text-danger fw-bold' : 'text-dark' }}" title="{{ $cat->name }}">{{ $cat->name }}</span>
                                        </span>
                                        <span class="badge bg-light text-muted border rounded-pill small" style="font-size: 11px;">
                                            {{ $cat->total_products_count ?? $cat->products_count }}
                                        </span>
                                    </label>
                                    @if($cat->children->isNotEmpty())
                                    <div class="ps-3 border-start ms-2 my-1">
                                        @foreach($cat->children as $child)
                                        <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                                            <span class="d-flex align-items-center gap-2 text-truncate" style="max-width: 170px;">
                                                <input type="radio" name="desktop_category" value="{{ $child->slug }}" class="form-check-input mt-0" {{ $selectedCategory == $child->slug ? 'checked' : '' }} onchange="selectCategoryFilter('{{ $child->slug }}')">
                                                <span class="small {{ $selectedCategory == $child->slug ? 'text-danger fw-bold' : 'text-muted' }}" title="{{ $child->name }}">{{ $child->name }}</span>
                                            </span>
                                            <span class="badge bg-light text-muted border rounded-pill small" style="font-size: 10px;">
                                                {{ $child->products_count }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-3 text-muted opacity-25">

                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark small text-uppercase mb-2 d-flex justify-content-between align-items-center">
                                <span>মূল্য পরিসীমা (Price)</span>
                                <i class="fas fa-tag text-muted"></i>
                            </h6>

                            <!-- Quick Price Chips -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(null, null)">সব দাম</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(0, 500)">৳০ - ৫০০</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(500, 1000)">৳৫০০ - ১০০০</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(1000, 2000)">৳১০০০ - ২০০০</button>
                            </div>

                            <!-- Min / Max inputs -->
                            <div class="row g-2 align-items-center">
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted px-1">৳</span>
                                        <input type="number" id="desktopMinPrice" class="form-control px-2" placeholder="মিনিমাম" value="{{ $minPrice ?? '' }}" min="0">
                                    </div>
                                </div>
                                <div class="col-2 text-center text-muted small">-</div>
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted px-1">৳</span>
                                        <input type="number" id="desktopMaxPrice" class="form-control px-2" placeholder="ম্যাক্সিমাম" value="{{ $maxPrice ?? '' }}" min="0">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger w-100 mt-2 py-1 fw-semibold" onclick="applyCustomPrice('desktop')">
                                <i class="fas fa-filter me-1"></i> ফিল্টার প্রয়োগ করুন
                            </button>
                        </div>

                        <hr class="my-3 text-muted opacity-25">

                        <!-- In Stock Availability Filter -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark small text-uppercase mb-2">স্টক অবস্থা</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="desktopInStock" {{ request()->boolean('in_stock') ? 'checked' : '' }} onchange="toggleInStockFilter(this.checked)">
                                <label class="form-check-label small text-dark cursor-pointer fw-semibold" for="desktopInStock">
                                    শুধুমাত্র ইন স্টক পণ্য
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Content Area -->
            <main class="col-lg-9">
                <!-- Desktop Controls Bar -->
                <div class="bg-white p-3 rounded-3 shadow-xs border mb-3 d-none d-lg-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        প্রদর্শিত হচ্ছে <strong id="showingCount" class="text-danger fw-bold">{{ $products->count() }}</strong> টি পণ্য 
                        (মোট <strong id="totalCount" class="text-dark">{{ $products->total() }}</strong> টির মধ্যে)
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label for="desktopSortSelect" class="small fw-semibold text-muted text-nowrap mb-0">সাজান:</label>
                        <select class="form-select form-select-sm fw-semibold" id="desktopSortSelect" style="width: 220px;" onchange="handleSortChange(this.value)">
                            <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>নতুন পণ্য (Newest)</option>
                            <option value="price_low_high" {{ in_array($sort, ['price_low_high', 'price_asc']) ? 'selected' : '' }}>দাম: কম থেকে বেশি (Low > High)</option>
                            <option value="price_high_low" {{ in_array($sort, ['price_high_low', 'price_desc']) ? 'selected' : '' }}>দাম: বেশি থেকে কম (High > Low)</option>
                            <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>জনপ্রিয় পণ্য (Popular)</option>
                            <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>নাম: ক - য় (Name A-Z)</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filter Tags Display -->
                <div id="activeFilterTagsBar" class="d-flex flex-wrap align-items-center gap-2 mb-3 d-none">
                    <span class="small text-muted fw-semibold">সক্রিয় ফিল্টার:</span>
                    <div id="activeFilterTagsList" class="d-flex flex-wrap gap-1"></div>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1 small text-decoration-none" onclick="resetAllFilters()">সব মুছুন</button>
                </div>

                <!-- Products Grid Container -->
                <div class="position-relative">
                    <!-- Loading Shimmer/Overlay -->
                    <div id="productsLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center d-none" style="z-index: 5; min-height: 250px; border-radius: 8px;">
                        <div class="text-center">
                            <div class="spinner-border text-danger" role="status" style="width: 2.5rem; height: 2.5rem;">
                                <span class="visually-hidden">লোড হচ্ছে...</span>
                            </div>
                            <p class="small text-muted fw-bold mt-2">পণ্য লোড হচ্ছে...</p>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-2 g-md-3" id="shopProductsContainer">
                        @foreach($products as $product)
                        <div class="col">
                            @include('frontend.partials.product-card', ['product' => $product])
                        </div>
                        @endforeach
                    </div>

                    <!-- Empty State (Hidden when products found) -->
                    <div id="shopEmptyState" class="text-center py-5 bg-white rounded-3 border {{ $products->count() == 0 ? '' : 'd-none' }}">
                        <div class="mb-3 text-muted">
                            <i class="fas fa-search fa-3x text-danger opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-dark">কোনো পণ্য পাওয়া যায়নি!</h5>
                        <p class="text-muted small mb-3">আপনার নির্বাচিত ফিল্টারে কোনো পণ্য মেলেনি। অন্য ক্যাটেগরি বা দামের ফিল্টার পরিবর্তন করে দেখুন।</p>
                        <button type="button" class="btn btn-danger btn-sm px-4 py-2 rounded-pill fw-bold" onclick="resetAllFilters()">
                            <i class="fas fa-undo me-1"></i> ফিল্টার রিসেট করুন
                        </button>
                    </div>
                </div>

                <!-- Load More Button Section (Instead of pagination) -->
                <div class="text-center mt-4 mb-3" id="loadMoreSection">
                    <button type="button" 
                            id="btnLoadMore" 
                            class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold shadow-xs {{ $products->hasMorePages() ? '' : 'd-none' }}" 
                            onclick="loadMoreProducts()">
                        <span class="normal-text d-inline-flex align-items-center gap-2">
                            <i class="fas fa-sync-alt"></i> আরও পণ্য দেখুন (Load More)
                        </span>
                        <span class="loading-text d-none align-items-center gap-2">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            লোড হচ্ছে...
                        </span>
                    </button>

                    <!-- End of Products Notice -->
                    <p id="endOfResultsMsg" class="text-muted small mt-2 mb-0 {{ !$products->hasMorePages() && $products->count() > 0 ? '' : 'd-none' }}">
                        <i class="fas fa-check-circle text-success me-1"></i> সব পণ্য প্রদর্শিত হয়েছে
                    </p>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Mobile Offcanvas Filter Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterDrawer" aria-labelledby="mobileFilterDrawerLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2" id="mobileFilterDrawerLabel">
            <i class="fas fa-sliders-h text-danger"></i> ফিল্টার ও সাজান
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <!-- Sort By for Mobile Drawer -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">সাজান (Sort By)</h6>
            <select class="form-select form-select-sm fw-semibold" id="drawerSortSelect" onchange="handleSortChange(this.value)">
                <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>নতুন পণ্য (Newest)</option>
                <option value="price_low_high" {{ in_array($sort, ['price_low_high', 'price_asc']) ? 'selected' : '' }}>দাম: কম থেকে বেশি (Low > High)</option>
                <option value="price_high_low" {{ in_array($sort, ['price_high_low', 'price_desc']) ? 'selected' : '' }}>দাম: বেশি থেকে কম (High > Low)</option>
                <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>জনপ্রিয় পণ্য (Popular)</option>
                <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>নাম: ক - য়</option>
            </select>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <!-- Category Filter -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">ক্যাটেগরি</h6>
            <div class="list-group list-group-flush border-0">
                <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                    <span class="d-flex align-items-center gap-2">
                        <input type="radio" name="mobile_category" value="" class="form-check-input mt-0" {{ empty($selectedCategory) ? 'checked' : '' }} onchange="selectCategoryFilter('')">
                        <span class="small fw-semibold {{ empty($selectedCategory) ? 'text-danger' : 'text-dark' }}">সকল ক্যাটেগরি</span>
                    </span>
                </label>
                @foreach($categories as $cat)
                <div class="category-filter-item">
                    <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                        <span class="d-flex align-items-center gap-2 text-truncate" style="max-width: 200px;">
                            <input type="radio" name="mobile_category" value="{{ $cat->slug }}" class="form-check-input mt-0" {{ $selectedCategory == $cat->slug ? 'checked' : '' }} onchange="selectCategoryFilter('{{ $cat->slug }}')">
                            <span class="small {{ $selectedCategory == $cat->slug ? 'text-danger fw-bold' : 'text-dark' }}">{{ $cat->name }}</span>
                        </span>
                        <span class="badge bg-light text-muted border rounded-pill small" style="font-size: 11px;">
                            {{ $cat->total_products_count ?? $cat->products_count }}
                        </span>
                    </label>
                    @if($cat->children->isNotEmpty())
                    <div class="ps-3 border-start ms-2 my-1">
                        @foreach($cat->children as $child)
                        <label class="list-group-item px-1 py-1 border-0 d-flex justify-content-between align-items-center cursor-pointer">
                            <span class="d-flex align-items-center gap-2 text-truncate" style="max-width: 180px;">
                                <input type="radio" name="mobile_category" value="{{ $child->slug }}" class="form-check-input mt-0" {{ $selectedCategory == $child->slug ? 'checked' : '' }} onchange="selectCategoryFilter('{{ $child->slug }}')">
                                <span class="small {{ $selectedCategory == $child->slug ? 'text-danger fw-bold' : 'text-muted' }}">{{ $child->name }}</span>
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill small" style="font-size: 10px;">
                                {{ $child->products_count }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <!-- Price Filter for Mobile Drawer -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">মূল্য পরিসীমা</h6>
            <div class="d-flex flex-wrap gap-1 mb-3">
                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(null, null)">সব দাম</button>
                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(0, 500)">৳০ - ৫০০</button>
                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(500, 1000)">৳৫০০ - ১০০০</button>
                <button type="button" class="btn btn-xs btn-outline-secondary price-preset-chip rounded-pill px-2 py-1 small" onclick="applyPresetPrice(1000, 2000)">৳১০০০ - ২০০০</button>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted px-1">৳</span>
                        <input type="number" id="mobileMinPrice" class="form-control px-2" placeholder="মিনিমাম" value="{{ $minPrice ?? '' }}" min="0">
                    </div>
                </div>
                <div class="col-2 text-center text-muted small">-</div>
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted px-1">৳</span>
                        <input type="number" id="mobileMaxPrice" class="form-control px-2" placeholder="ম্যাক্সিমাম" value="{{ $maxPrice ?? '' }}" min="0">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger w-100 mt-2 py-1 fw-semibold" onclick="applyCustomPrice('mobile')">
                <i class="fas fa-filter me-1"></i> ফিল্টার প্রয়োগ করুন
            </button>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <!-- In Stock Filter for Mobile Drawer -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">স্টক অবস্থা</h6>
            <div class="form-check form-switch">
                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="mobileInStock" {{ request()->boolean('in_stock') ? 'checked' : '' }} onchange="toggleInStockFilter(this.checked)">
                <label class="form-check-label small text-dark cursor-pointer fw-semibold" for="mobileInStock">
                    শুধুমাত্র ইন স্টক পণ্য
                </label>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1 py-2 fw-semibold" onclick="resetAllFilters()">
            <i class="fas fa-redo-alt me-1"></i> রিসেট
        </button>
        <button type="button" class="btn btn-danger btn-sm flex-grow-1 py-2 fw-bold" data-bs-dismiss="offcanvas">
            ফলাফল দেখুন
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .btn-xs { font-size: 11px; padding: 2px 8px; }
    .price-preset-chip.active {
        background-color: var(--color-brand-accent, #dc3545) !important;
        color: #fff !important;
        border-color: var(--color-brand-accent, #dc3545) !important;
    }
    .category-filter-item label:hover {
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    #btnLoadMore:hover {
        background-color: #dc3545;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    // State management for shop filtering and pagination
    let shopState = {
        category: '{{ $selectedCategory ?? '' }}',
        min_price: '{{ $minPrice ?? '' }}',
        max_price: '{{ $maxPrice ?? '' }}',
        in_stock: {{ request()->boolean('in_stock') ? 'true' : 'false' }},
        sort: '{{ $sort ?? 'latest' }}',
        currentPage: {{ $products->currentPage() }},
        hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
        isLoading: false
    };

    // Synchronize inputs across desktop & mobile drawer
    function syncInputs() {
        // Categories
        document.querySelectorAll('input[name="desktop_category"]').forEach(radio => {
            radio.checked = (radio.value === shopState.category);
        });
        document.querySelectorAll('input[name="mobile_category"]').forEach(radio => {
            radio.checked = (radio.value === shopState.category);
        });

        // Price
        const dMin = document.getElementById('desktopMinPrice');
        const mMin = document.getElementById('mobileMinPrice');
        if (dMin) dMin.value = shopState.min_price || '';
        if (mMin) mMin.value = shopState.min_price || '';

        const dMax = document.getElementById('desktopMaxPrice');
        const mMax = document.getElementById('mobileMaxPrice');
        if (dMax) dMax.value = shopState.max_price || '';
        if (mMax) mMax.value = shopState.max_price || '';

        // In Stock
        const dStock = document.getElementById('desktopInStock');
        const mStock = document.getElementById('mobileInStock');
        if (dStock) dStock.checked = shopState.in_stock;
        if (mStock) mStock.checked = shopState.in_stock;

        // Sort
        const dSort = document.getElementById('desktopSortSelect');
        const mSort = document.getElementById('mobileSortSelect');
        const drSort = document.getElementById('drawerSortSelect');
        if (dSort) dSort.value = shopState.sort;
        if (mSort) mSort.value = shopState.sort;
        if (drSort) drSort.value = shopState.sort;

        updateActiveFilterTags();
    }

    // Update active filter pills
    function updateActiveFilterTags() {
        const bar = document.getElementById('activeFilterTagsBar');
        const list = document.getElementById('activeFilterTagsList');
        const mobileBadge = document.getElementById('mobileActiveFilterCount');
        if (!bar || !list) return;

        list.innerHTML = '';
        let count = 0;

        if (shopState.category) {
            count++;
            const pill = document.createElement('span');
            pill.className = 'badge bg-white text-dark border rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 small';
            pill.innerHTML = `<span>ক্যাটেগরি: ${shopState.category}</span> <button type="button" class="btn-close ms-1" style="font-size: 8px;" onclick="selectCategoryFilter('')"></button>`;
            list.appendChild(pill);
        }

        if (shopState.min_price || shopState.max_price) {
            count++;
            const minText = shopState.min_price ? `৳${shopState.min_price}` : '৳০';
            const maxText = shopState.max_price ? `৳${shopState.max_price}` : 'উপরে';
            const pill = document.createElement('span');
            pill.className = 'badge bg-white text-dark border rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 small';
            pill.innerHTML = `<span>দাম: ${minText} - ${maxText}</span> <button type="button" class="btn-close ms-1" style="font-size: 8px;" onclick="applyPresetPrice(null, null)"></button>`;
            list.appendChild(pill);
        }

        if (shopState.in_stock) {
            count++;
            const pill = document.createElement('span');
            pill.className = 'badge bg-white text-dark border rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 small';
            pill.innerHTML = `<span>ইন স্টক</span> <button type="button" class="btn-close ms-1" style="font-size: 8px;" onclick="toggleInStockFilter(false)"></button>`;
            list.appendChild(pill);
        }

        if (count > 0) {
            bar.classList.remove('d-none');
            if (mobileBadge) {
                mobileBadge.textContent = count;
                mobileBadge.classList.remove('d-none');
            }
        } else {
            bar.classList.add('d-none');
            if (mobileBadge) {
                mobileBadge.classList.add('d-none');
            }
        }
    }

    // Build query URL
    function buildQueryString(page = 1) {
        const params = new URLSearchParams();
        if (shopState.category) params.set('category', shopState.category);
        if (shopState.min_price) params.set('min_price', shopState.min_price);
        if (shopState.max_price) params.set('max_price', shopState.max_price);
        if (shopState.in_stock) params.set('in_stock', '1');
        if (shopState.sort && shopState.sort !== 'latest') params.set('sort', shopState.sort);
        if (page > 1) params.set('page', page);
        return params.toString();
    }

    // Fetch and replace products for new filter selection
    function fetchFilteredProducts() {
        if (shopState.isLoading) return;
        shopState.isLoading = true;
        shopState.currentPage = 1;

        const overlay = document.getElementById('productsLoadingOverlay');
        if (overlay) overlay.classList.remove('d-none');

        const queryString = buildQueryString(1);
        const url = `{{ route('shop.index') }}${queryString ? '?' + queryString : ''}`;

        // Update browser URL without reloading
        window.history.pushState(null, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('shopProductsContainer');
            const emptyState = document.getElementById('shopEmptyState');
            const btnLoadMore = document.getElementById('btnLoadMore');
            const endMsg = document.getElementById('endOfResultsMsg');
            const showingCount = document.getElementById('showingCount');
            const totalCount = document.getElementById('totalCount');
            const headerBadge = document.getElementById('headerTotalBadge');

            container.innerHTML = data.html;

            if (data.total === 0) {
                emptyState.classList.remove('d-none');
                btnLoadMore.classList.add('d-none');
                endMsg.classList.add('d-none');
            } else {
                emptyState.classList.add('d-none');
                shopState.hasMore = data.hasMore;
                shopState.currentPage = data.currentPage;

                if (data.hasMore) {
                    btnLoadMore.classList.remove('d-none');
                    endMsg.classList.add('d-none');
                } else {
                    btnLoadMore.classList.add('d-none');
                    endMsg.classList.remove('d-none');
                }
            }

            if (showingCount) showingCount.textContent = data.count;
            if (totalCount) totalCount.textContent = data.total;
            if (headerBadge) headerBadge.textContent = data.total;

            syncInputs();
        })
        .catch(err => {
            console.error('Error fetching filtered products:', err);
        })
        .finally(() => {
            shopState.isLoading = false;
            if (overlay) overlay.classList.add('d-none');
        });
    }

    // Load More Products via AJAX button
    function loadMoreProducts() {
        if (shopState.isLoading || !shopState.hasMore) return;
        shopState.isLoading = true;

        const btn = document.getElementById('btnLoadMore');
        const normalText = btn.querySelector('.normal-text');
        const loadingText = btn.querySelector('.loading-text');

        if (normalText) normalText.classList.add('d-none');
        if (loadingText) {
            loadingText.classList.remove('d-none');
            loadingText.classList.add('d-inline-flex');
        }

        const nextPage = shopState.currentPage + 1;
        const queryString = buildQueryString(nextPage);
        const url = `{{ route('shop.index') }}?${queryString}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('shopProductsContainer');
            const endMsg = document.getElementById('endOfResultsMsg');
            const showingCount = document.getElementById('showingCount');

            // Append cards
            container.insertAdjacentHTML('beforeend', data.html);

            shopState.currentPage = data.currentPage;
            shopState.hasMore = data.hasMore;

            // Update showing count
            const currentRenderedCount = container.querySelectorAll('.col').length;
            if (showingCount) showingCount.textContent = currentRenderedCount;

            if (data.hasMore) {
                btn.classList.remove('d-none');
                endMsg.classList.add('d-none');
            } else {
                btn.classList.add('d-none');
                endMsg.classList.remove('d-none');
            }
        })
        .catch(err => {
            console.error('Error loading more products:', err);
        })
        .finally(() => {
            shopState.isLoading = false;
            if (normalText) normalText.classList.remove('d-none');
            if (loadingText) {
                loadingText.classList.add('d-none');
                loadingText.classList.remove('d-inline-flex');
            }
        });
    }

    // Filter event triggers
    function selectCategoryFilter(catSlug) {
        shopState.category = catSlug;
        fetchFilteredProducts();
    }

    function applyPresetPrice(min, max) {
        shopState.min_price = min !== null ? min : '';
        shopState.max_price = max !== null ? max : '';
        fetchFilteredProducts();
    }

    function applyCustomPrice(source) {
        const minInput = document.getElementById(source === 'mobile' ? 'mobileMinPrice' : 'desktopMinPrice');
        const maxInput = document.getElementById(source === 'mobile' ? 'mobileMaxPrice' : 'desktopMaxPrice');

        shopState.min_price = minInput && minInput.value ? minInput.value : '';
        shopState.max_price = maxInput && maxInput.value ? maxInput.value : '';
        fetchFilteredProducts();
    }

    function toggleInStockFilter(isChecked) {
        shopState.in_stock = isChecked;
        fetchFilteredProducts();
    }

    function handleSortChange(sortVal) {
        shopState.sort = sortVal;
        fetchFilteredProducts();
    }

    function resetAllFilters() {
        shopState.category = '';
        shopState.min_price = '';
        shopState.max_price = '';
        shopState.in_stock = false;
        shopState.sort = 'latest';
        fetchFilteredProducts();
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function () {
        const urlParams = new URLSearchParams(window.location.search);
        shopState.category = urlParams.get('category') || '';
        shopState.min_price = urlParams.get('min_price') || '';
        shopState.max_price = urlParams.get('max_price') || '';
        shopState.in_stock = urlParams.get('in_stock') === '1';
        shopState.sort = urlParams.get('sort') || 'latest';
        fetchFilteredProducts();
    });

    document.addEventListener('DOMContentLoaded', function () {
        syncInputs();
    });
</script>
@endpush
