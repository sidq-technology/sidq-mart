@extends('layouts.app')

@section('title', \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-3">
    <!-- 1. Hero Banner Carousel -->
    @if($banners->count() > 0)
    <div id="heroBannerCarousel" class="carousel slide rounded-3 overflow-hidden shadow-sm mb-4" data-bs-ride="carousel">
        @if($banners->count() > 1)
        <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
            <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif

        <div class="carousel-inner">
            @foreach($banners as $index => $banner)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <a href="{{ $banner->target_url ?: '#' }}">
                    <img src="{{ $banner->image_url }}" class="d-block w-100" alt="{{ $banner->title ?: 'Promotion Banner' }}" style="max-height: 420px; object-fit: cover;">
                </a>
            </div>
            @endforeach
        </div>

        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        @endif
    </div>
    @endif

    <!-- 2. Top Categories Scroller (SIDQ Technology UI) -->
    @if($topCategories->count() > 0)
    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-header-title">
                শীর্ষ ক্যাটেগরি (Top Categories)
            </h4>
        </div>
        <div class="cat-scroll-wrapper">
            @foreach($topCategories as $cat)
            <div class="cat-scroll-item">
                <a href="{{ route('product.category', $cat->slug) }}" class="text-decoration-none">
                    <div class="cat-thumb-box h-100 p-2 text-center bg-white rounded-3 border">
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="rounded-circle mb-2" style="width: 72px; height: 72px; object-fit: cover;" loading="lazy">
                        <h6 class="text-truncate small mb-0 fw-bold text-dark">{{ $cat->name }}</h6>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 3. Flash Sale Section (Dense Responsive Grid) -->
    @if($flashSales->count() > 0)
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-header-title">
                <i class="fas fa-bolt text-danger"></i> ফ্ল্যাশ সেল (Flash Sale)
            </h4>
            <span class="badge bg-danger px-3 py-2 fw-bold text-white small">
                সীমিত সময়ের অফার
            </span>
        </div>
        
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
            @foreach($flashSales as $product)
            <div class="col">
                @include('frontend.partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 4. Featured Products Section -->
    @if($featuredProducts->count() > 0)
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-header-title">
                <i class="fas fa-fire text-danger"></i> জনপ্রিয় প্রোডাক্টস (Featured Products)
            </h4>
        </div>
        
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
            @foreach($featuredProducts as $product)
            <div class="col">
                @include('frontend.partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 5. All Products Grid -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-header-title">
                <i class="fas fa-boxes text-danger"></i> সকল পণ্য (All Products)
            </h4>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
            @foreach($allProducts as $product)
            <div class="col">
                @include('frontend.partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $allProducts->links('pagination::bootstrap-5') }}
        </div>
    </section>
</div>
@endsection
