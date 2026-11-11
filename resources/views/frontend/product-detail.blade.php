@extends('layouts.app')

@section('title', $product->name . ' - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
@php
    $allGalleryImages = collect([$product->primary_image_url]);
    foreach($product->galleryImages as $gImg) {
        if ($gImg->image_url && !in_array($gImg->image_url, $allGalleryImages->toArray())) {
            $allGalleryImages->push($gImg->image_url);
        }
    }

    $supportPhone = \App\Models\Setting::get('whatsapp_number') ?: \App\Models\Setting::get('contact_phone', '01711223344');
    $displayPhone = trim($supportPhone);
    $callPhone = preg_replace('/[^\d+]/', '', $displayPhone);

    $waPhone = preg_replace('/[^\d]/', '', $displayPhone);
    if (str_starts_with($waPhone, '01')) {
        $waPhone = '88' . $waPhone;
    } elseif (!str_starts_with($waPhone, '8801') && str_starts_with($waPhone, '1') && strlen($waPhone) === 10) {
        $waPhone = '880' . $waPhone;
    }
    $waDefaultMsg = urlencode("হ্যালো, আমি এই পণ্যটি সম্পর্কে জানতে / অর্ডার করতে চাই:\n*{$product->name}*\nমূল্য: ৳" . number_format($product->final_price, 0) . "\nলিংক: " . url()->current());
    $waUrl = "https://wa.me/{$waPhone}?text={$waDefaultMsg}";
@endphp

<div class="container pdp-page-container mt-1 mt-md-4 mb-4 px-2 px-sm-3">
    <div class="row g-2 g-md-4">
        <!-- 1. Product Images Gallery -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm p-1.5 p-sm-3 rounded-3 position-relative pdp-gallery-card">
                
                <!-- Main Image Container with Slide / Drag Support (No Zoom) -->
                <div class="position-relative text-center mb-2 mb-md-3 main-image-container rounded-3 overflow-hidden select-none" 
                     id="pdpMainImageWrapper"
                     style="background: #fafafa; border: 1px solid #f1f5f9; aspect-ratio: 1 / 1; width: 100%; display: flex; align-items: center; justify-content: center; cursor: grab; user-select: none; -webkit-user-select: none; touch-action: pan-y;">
                    
                    <img id="mainProductImg" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                         class="img-fluid rounded-3" 
                         draggable="false"
                         style="max-height: 100%; max-width: 100%; width: 100%; height: 100%; object-fit: contain; transition: opacity 0.2s ease; pointer-events: none; user-select: none;">

                    @if($allGalleryImages->count() > 1)
                    <!-- Prev / Next Navigation Arrows -->
                    <button type="button" class="pdp-carousel-nav pdp-carousel-prev" onclick="slidePdpImage(-1, event)" aria-label="পূর্ববর্তী ছবি">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="pdp-carousel-nav pdp-carousel-next" onclick="slidePdpImage(1, event)" aria-label="পরবর্তী ছবি">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <!-- Image Counter Badge -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 px-2 py-1 rounded-pill bg-dark bg-opacity-75 text-white small" style="font-size: 11px; z-index: 2; pointer-events: none;">
                        <span id="pdpImageCounterText">1 / {{ $allGalleryImages->count() }}</span>
                    </div>
                    @endif
                </div>

                <!-- Thumbnails Strip (Only if multiple unique images exist) -->
                @if($allGalleryImages->count() > 1)
                <div class="d-flex gap-2 overflow-auto pb-2 align-items-center" id="pdpThumbnailStrip">
                    @foreach($allGalleryImages as $idx => $imgUrl)
                    <div class="thumb-item position-relative rounded-2 overflow-hidden border p-1 {{ $loop->first ? 'active' : '' }}" 
                         style="width: 72px; height: 72px; flex-shrink: 0; cursor: pointer; transition: all 0.2s ease; background: #fff;"
                         onclick="changeMainImage('{{ $imgUrl }}', {{ $idx }}, this)">
                        <img src="{{ $imgUrl }}" alt="Thumb {{ $idx + 1 }}" class="w-100 h-100 object-fit-cover rounded-1">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- 2. Product Information & Purchase Block -->
        <div class="col-12 col-md-6">
            <div class="p-2">
                <h1 class="fs-4 fw-bold text-dark mb-3 lh-base">{{ $product->name }}</h1>

                <!-- Price Area -->
                <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-baseline gap-3 flex-wrap">
                    <span class="fs-2 fw-bold text-danger tabular-nums" id="pdpPriceDisplay">Tk {{ number_format($product->current_price, 0) }}</span>
                    @if($product->regular_price && $product->regular_price > $product->current_price)
                    @php
                        $savings = $product->regular_price - $product->current_price;
                    @endphp
                    <del class="text-muted fs-5 tabular-nums" id="pdpRegularPriceDisplay">Tk {{ number_format($product->regular_price, 0) }}</del>
                    @if(\App\Models\Setting::get('show_savings_badge', '0') === '1')
                    <span class="badge fs-6 px-2 py-1 fw-bold text-white shadow-2xs" id="pdpSavingsBadge" style="background-color: #16a34a;">{{ number_format($savings, 0) }} টাকা সেইভ</span>
                    @endif
                    @endif
                </div>

                <!-- Stock Status -->
                @if(\App\Models\Setting::get('show_stock_badge', '0') === '1')
                <div class="mb-3">
                    @if($product->stock_quantity > 0)
                    <span class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded-2 bg-white border fw-semibold shadow-2xs" id="pdpStockBadge" style="color: #e05260; border-color: #fca5a5 !important; font-size: 13px;">
                        <i class="fas fa-fire me-1" style="color: #ef4444;"></i>
                        <span id="pdpStockText">{{ $product->stock_quantity }} টি আইটেম আছে মাত্র</span>
                    </span>
                    @else
                    <span class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded-2 bg-white border fw-semibold text-muted" id="pdpStockBadge" style="font-size: 13px;">
                        <i class="fas fa-times-circle me-1"></i> <span id="pdpStockText">স্টক শেষ (Out of Stock)</span>
                    </span>
                    @endif
                </div>
                @endif

                @if($product->short_description)
                <div class="mb-4 pdp-short-description">
                    {!! nl2br(e($product->short_description)) !!}
                </div>
                @endif

                @php
                    $hasVariants = $product->has_variants && $product->variants->isNotEmpty();
                    $availableColors = $hasVariants ? $product->variants->whereNotNull('color')->where('color', '!=', '')->unique('color')->values() : collect();
                    $availableSizes = $hasVariants ? $product->variants->whereNotNull('size')->where('size', '!=', '')->unique('size')->values() : collect();
                    $variantsData = $hasVariants ? $product->variants->map(function($v) {
                        return [
                            'id' => $v->id,
                            'color' => $v->color,
                            'size' => $v->size,
                            'price' => $v->price ? (float)$v->price : null,
                            'effective_price' => $v->effective_price,
                            'stock_quantity' => (int)$v->stock_quantity,
                            'image_url' => $v->image_url,
                        ];
                    })->values() : collect();
                @endphp

                <!-- Conditional Variation Selectors (Colors & Sizes) -->
                @if($hasVariants && ($availableColors->isNotEmpty() || $availableSizes->isNotEmpty()))
                <div class="product-variations-box p-3 bg-light rounded-3 mb-4 border" id="pdpVariationsBox">
                    @if($availableColors->isNotEmpty())
                    <div class="{{ $availableSizes->isNotEmpty() ? 'mb-3' : 'mb-0' }}">
                        <div class="d-flex flex-wrap gap-2" id="colorSwatchesContainer">
                            @foreach($availableColors as $c)
                            @php
                                $cImg = $c->image_url ?: '';
                            @endphp
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm color-swatch-chip rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2" 
                                    data-color="{{ $c->color }}" 
                                    data-image="{{ $cImg }}"
                                    onclick="selectColor('{{ $c->color }}', this)">
                                @if($c->color_code)
                                <span class="color-dot rounded-circle border" style="width: 14px; height: 14px; background-color: {{ $c->color_code }}; display: inline-block;"></span>
                                @endif
                                <span class="color-name">{{ $c->color }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($availableSizes->isNotEmpty())
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-dark d-block mb-2">
                            <span>সাইজ নির্বাচন করুন: <strong id="selectedSizeDisplay" class="text-danger"></strong></span>
                        </label>
                        <div class="d-flex flex-wrap gap-2" id="sizeChipsContainer">
                            @foreach($availableSizes as $s)
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm size-chip-btn px-3 py-1 fw-bold rounded-2" 
                                    data-size="{{ $s->size }}" 
                                    onclick="selectSize('{{ $s->size }}', this)">
                                {{ $s->size }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Purchase Actions -->
                <div class="border-top border-bottom py-3 mb-4">
                    <form action="{{ route('buy.now', $product->slug) }}" method="POST" id="pdpOrderForm">
                        @csrf
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="">
                        <input type="hidden" name="color" id="selectedColorInput" value="">
                        <input type="hidden" name="size" id="selectedSizeInput" value="">
                        <div class="row align-items-center g-3 mb-3">
                            <label class="col-auto form-label mb-0 fw-bold">পরিমাণ:</label>
                            <div class="col-auto">
                                <div class="input-group" style="width: 140px;">
                                    <button class="btn btn-outline-secondary px-3" type="button" onclick="decrementQty()">-</button>
                                    <input type="number" name="quantity" id="pdpQtyInput" class="form-control text-center fw-bold" value="1" min="1" max="{{ $product->stock_quantity }}">
                                    <button class="btn btn-outline-secondary px-3" type="button" onclick="incrementQty()">+</button>
                                </div>
                            </div>
                        </div>



                        <div class="pdp-actions-wrap mb-3">
                            <!-- Row 1: Order Now & Call Now -->
                            <div class="row g-2 mb-2">
                                <div class="col-12 col-sm-6">
                                    <button type="submit" class="btn btn-order-pdp w-100 py-3 px-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-bolt text-warning"></i>
                                        <span>অর্ডার করুন</span>
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <a href="tel:{{ $callPhone }}" onclick="trackPdpCall()" class="btn btn-call-pdp w-100 py-3 px-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                                        <i class="fas fa-phone-alt"></i>
                                        <span class="text-truncate">কল করুন {{ $displayPhone }}</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Row 2: WhatsApp Now -->
                            <div class="row g-2">
                                <div class="col-12">
                                    <a href="{{ $waUrl }}" onclick="trackPdpWhatsApp()" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-pdp w-100 py-3 px-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                                        <i class="fab fa-whatsapp fs-5"></i>
                                        <span>WhatsApp করুন {{ $displayPhone }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Guarantee & Assurance Box -->
                <div class="p-3 rounded-3 bg-light border small">
                    <div class="row g-2">
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="fas fa-truck text-danger fs-5"></i>
                            <span>সারা দেশে ক্যাশ অন হোম ডেলিভারি</span>
                        </div>
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="fas fa-undo-alt text-success fs-5"></i>
                            <span>৭ দিনের সহজ রিটার্ন পলিসি</span>
                        </div>
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="fas fa-shield-alt text-primary fs-5"></i>
                            <span>১০০% অথেনটিক অরিজিনাল প্রোডাক্ট</span>
                        </div>
                        <div class="col-6 d-flex align-items-center gap-2">
                            <i class="fas fa-headset text-warning fs-5"></i>
                            <span>২৪/৭ ডেডিকেটেড গ্রাহক সেবা</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description & Specifications -->
    <div class="card border-0 shadow-sm mt-5 p-4 rounded-3">
        <ul class="nav nav-tabs" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button" role="tab">পণ্যের বিবরণ (Description)</button>
            </li>
            @if(!empty($product->specifications))
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs-tab-pane" type="button" role="tab">স্পেসিফিকেশন (Specifications)</button>
            </li>
            @endif
        </ul>
        <div class="tab-content pt-4" id="productTabContent">
            <div class="tab-pane fade show active lh-lg" id="desc-tab-pane" role="tabpanel">
                {!! $product->description ?: '<p>পণ্যের বিস্তারিত তথ্যের জন্য আমাদের সাথে যোগাযোগ করুন।</p>' !!}
            </div>
            @if(!empty($product->specifications))
            <div class="tab-pane fade" id="specs-tab-pane" role="tabpanel">
                <table class="table table-bordered table-striped">
                    <tbody>
                        @foreach($product->specifications as $key => $val)
                        <tr>
                            <th class="w-25 bg-light">{{ $key }}</th>
                            <td>{{ $val }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-5">
        <h4 class="section-header-title mb-3">সম্পর্কিত পণ্যসমূহ (Related Products)</h4>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
            @foreach($relatedProducts as $relProduct)
            <div class="col">
                @include('frontend.partials.product-card', ['product' => $relProduct])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- ===========================================
     MOBILE FLOATING BOTTOM ORDER DOCK (Continuous Shake & Glow)
=========================================== -->
<div class="pdp-mobile-sticky-bar d-block d-md-none" id="pdpMobileStickyBar">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="pdp-sticky-price-box ps-1">
            <span class="text-muted d-block fw-medium" style="font-size: 10.5px; line-height: 1.1;">মূল্য:</span>
            <span class="pdp-sticky-price fw-bold text-danger tabular-nums" id="pdpStickyPrice">৳{{ number_format($product->final_price, 0) }}</span>
        </div>
        <button type="button" onclick="triggerPdpOrder()" class="btn btn-order-pdp flex-grow-1 py-2 px-2.5 fw-bold d-flex align-items-center justify-content-center gap-1.5 text-white shadow-sm">
            <i class="fas fa-bolt text-warning fs-5"></i>
            <span class="fs-6 fw-bold">অর্ডার করুন</span>
        </button>
    </div>
</div>

<style>
/* Mobile Floating Bottom Order Dock */
@media (max-width: 767.98px) {
    body {
        padding-bottom: 85px !important;
    }
    body .support-floating-wrapper {
        bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
    }
    .pdp-page-container {
        margin-top: 6px !important;
        padding-left: 11px !important;
        padding-right: 11px !important;
    }
    .pdp-gallery-card {
        padding: 7px !important;
        border-radius: 12px !important;
    }
    .main-image-container {
        margin-bottom: 8px !important;
    }
}
.pdp-mobile-sticky-bar {
    position: fixed;
    bottom: calc(10px + env(safe-area-inset-bottom, 0px));
    left: 12px;
    right: 12px;
    margin: 0 auto;
    max-width: 520px;
    z-index: 1040;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.2), 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1.5px solid rgba(241, 49, 36, 0.25);
    border-radius: 50px;
    padding: 7px 10px 7px 14px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.pdp-sticky-price-box {
    flex-shrink: 0;
    min-width: 72px;
    white-space: nowrap;
}
.pdp-sticky-price {
    font-size: 17.5px;
    letter-spacing: -0.3px;
    line-height: 1.2;
    display: block;
}
.pdp-mobile-sticky-bar .btn-order-pdp {
    min-height: 44px;
    font-size: 15.5px;
    border-radius: 50px;
    background: linear-gradient(135deg, #f13124 0%, #c9251a 100%);
    border: none;
    color: #ffffff;
    padding: 8px 10px;
    transform-origin: center center;
    animation: orderBtnPulseShake 2.6s infinite ease-in-out;
}
.pdp-mobile-sticky-bar .btn-order-pdp:active {
    transform: scale(0.96) !important;
}

/* Continuous Attention-Grabbing Shake & Pulse Animation */
@keyframes orderBtnPulseShake {
    0%, 100% {
        transform: scale(1) rotate(0deg);
        box-shadow: 0 4px 12px rgba(241, 49, 36, 0.4);
    }
    10% {
        transform: scale(1.04) rotate(-2deg);
        box-shadow: 0 6px 18px rgba(241, 49, 36, 0.65), 0 0 0 4px rgba(241, 49, 36, 0.18);
    }
    18% {
        transform: scale(1.04) rotate(2deg);
        box-shadow: 0 6px 18px rgba(241, 49, 36, 0.65), 0 0 0 4px rgba(241, 49, 36, 0.18);
    }
    26% {
        transform: scale(1.03) rotate(-1.5deg);
    }
    34% {
        transform: scale(1.02) rotate(1.5deg);
    }
    42% {
        transform: scale(1) rotate(0deg);
        box-shadow: 0 4px 12px rgba(241, 49, 36, 0.4);
    }
}

/* Product Image Slide Arrows (No Zoom) */
.pdp-carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #1e293b;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    z-index: 5;
    transition: all 0.2s ease;
}
.pdp-carousel-nav:hover {
    background: #ffffff;
    color: #dc2626;
    transform: translateY(-50%) scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.22);
}
.pdp-carousel-prev {
    left: 10px;
}
.pdp-carousel-next {
    right: 10px;
}
@media (max-width: 576px) {
    .pdp-carousel-nav {
        width: 32px;
        height: 32px;
        font-size: 13px;
    }
    .pdp-carousel-prev {
        left: 6px;
    }
    .pdp-carousel-next {
        right: 6px;
    }
}

@keyframes pdpShake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
.pdp-variant-shake {
    animation: pdpShake 0.4s ease-in-out 3;
    outline: 2px solid #ef4444;
    border-radius: 8px;
}
.thumb-item.active {
    border-color: #dc2626 !important;
    border-width: 2px !important;
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25) !important;
    transform: scale(1.04);
}
.thumb-item:hover:not(.active) {
    border-color: #94a3b8 !important;
    transform: scale(1.02);
}

/* Variant Swatches & Chips */
.color-swatch-chip {
    border-color: #cbd5e1;
    color: #334155;
    background: #fff;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}
.color-swatch-chip:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}
.color-swatch-chip.active {
    border-color: #dc2626 !important;
    background-color: #fff !important;
    color: #dc2626 !important;
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25) !important;
    font-weight: 700;
}
.size-chip-btn {
    border-color: #cbd5e1;
    color: #334155;
    background: #fff;
    min-width: 44px;
    font-size: 13px;
    transition: all 0.2s ease;
}
.size-chip-btn:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}
.size-chip-btn.active {
    border-color: #dc2626 !important;
    background-color: #dc2626 !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25) !important;
}

/* Product Description Rich Content Styling */
#desc-tab-pane {
    color: #334155;
    font-size: 15px;
    line-height: 1.8;
}
#desc-tab-pane img {
    max-width: 100% !important;
    height: auto !important;
    border-radius: 8px;
    margin: 14px 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    display: inline-block;
}
#desc-tab-pane ol {
    list-style-type: decimal !important;
    padding-left: 1.6rem !important;
    margin: 0.75rem 0 1rem !important;
}
#desc-tab-pane ul {
    list-style-type: disc !important;
    padding-left: 1.6rem !important;
    margin: 0.75rem 0 1rem !important;
}
#desc-tab-pane li {
    margin-bottom: 0.4rem;
}
#desc-tab-pane table {
    width: 100% !important;
    border-collapse: collapse;
    margin: 1rem 0;
}
#desc-tab-pane table th, #desc-tab-pane table td {
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
}
#desc-tab-pane table th {
    background-color: #f8fafc;
    font-weight: 600;
}
#desc-tab-pane h1, #desc-tab-pane h2, #desc-tab-pane h3, #desc-tab-pane h4, #desc-tab-pane h5, #desc-tab-pane h6 {
    font-weight: 700;
    color: #0f172a;
    margin-top: 1.25rem;
    margin-bottom: 0.6rem;
}

/* Product Detail Action Buttons (Order, Call, WhatsApp) */
.btn-order-pdp {
    background: linear-gradient(135deg, #e11d48 0%, #dc2626 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    min-height: 50px;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-order-pdp:hover {
    background: linear-gradient(135deg, #be123c 0%, #b91c1c 100%);
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
}

.btn-call-pdp {
    background: #ffffff;
    color: #0f172a !important;
    border: 1.5px solid #0f172a;
    border-radius: 8px;
    font-weight: 700;
    min-height: 50px;
    font-size: 15px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    transition: all 0.2s ease;
}
.btn-call-pdp:hover {
    background: #0f172a;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}

.btn-whatsapp-pdp {
    background: #25D366;
    color: #ffffff !important;
    border: 1px solid #20ba59;
    border-radius: 8px;
    font-weight: 700;
    min-height: 50px;
    font-size: 15px;
    box-shadow: 0 3px 10px rgba(37, 211, 102, 0.25);
    transition: all 0.2s ease;
}
.btn-whatsapp-pdp:hover {
    background: #1ebd5b;
    border-color: #1ebd5b;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 5px 14px rgba(37, 211, 102, 0.35);
}
</style>

@push('scripts')
<script>
    const galleryImages = @json($allGalleryImages->values());
    let currentImageIndex = 0;

    function changeMainImage(src, index, element) {
        currentImageIndex = index;
        const mainImg = document.getElementById('mainProductImg');
        if (!mainImg) return;
        
        mainImg.style.opacity = '0.35';
        const temp = new Image();
        temp.src = src;
        temp.onload = function() {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        };
        temp.onerror = function() {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        };

        // Update active state on thumbnails
        const thumbs = document.querySelectorAll('#pdpThumbnailStrip .thumb-item');
        thumbs.forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
            element.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        } else if (thumbs[index]) {
            thumbs[index].classList.add('active');
            thumbs[index].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        // Update Counter
        const counter = document.getElementById('pdpImageCounterText');
        if (counter && galleryImages.length > 0) {
            counter.innerText = (index + 1) + ' / ' + galleryImages.length;
        }
    }

    function slidePdpImage(direction, event) {
        if (event && event.stopPropagation) event.stopPropagation();
        if (!galleryImages || galleryImages.length <= 1) return;

        let newIndex = currentImageIndex + direction;
        if (newIndex < 0) newIndex = galleryImages.length - 1;
        if (newIndex >= galleryImages.length) newIndex = 0;

        const thumbs = document.querySelectorAll('#pdpThumbnailStrip .thumb-item');
        const targetThumb = thumbs[newIndex] || null;
        changeMainImage(galleryImages[newIndex], newIndex, targetThumb);
    }

    function triggerPdpOrder() {
        const orderForm = document.getElementById('pdpOrderForm');
        if (!orderForm) return;

        if (hasColorOptions && !selectedColor) {
            alert('অনুগ্রহ করে আপনার পছন্দের কালার নির্বাচন করুন।');
            const vBox = document.getElementById('pdpVariationsBox');
            if (vBox) {
                vBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                vBox.classList.add('pdp-variant-shake');
                setTimeout(() => vBox.classList.remove('pdp-variant-shake'), 1200);
            }
            return;
        }

        if (hasSizeOptions && !selectedSize) {
            alert('অনুগ্রহ করে পণ্যের সাইজ নির্বাচন করুন।');
            const vBox = document.getElementById('pdpVariationsBox');
            if (vBox) {
                vBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                vBox.classList.add('pdp-variant-shake');
                setTimeout(() => vBox.classList.remove('pdp-variant-shake'), 1200);
            }
            return;
        }

        if (orderForm.requestSubmit) {
            orderForm.requestSubmit();
        } else {
            orderForm.submit();
        }
    }

    function incrementQty() {
        const input = document.getElementById('pdpQtyInput');
        const max = parseInt(input.getAttribute('max')) || 999;
        let val = parseInt(input.value) || 1;
        if (val < max) input.value = val + 1;
    }

    function decrementQty() {
        const input = document.getElementById('pdpQtyInput');
        let val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    }

    // ===========================================
    // PRODUCT VARIATIONS JAVASCRIPT LOGIC
    // ===========================================
    const productVariants = @json($variantsData);
    const baseProductPrice = {{ (float)$product->current_price }};
    const baseRegularPrice = {{ $product->regular_price ? (float)$product->regular_price : 'null' }};
    let currentProductPrice = {{ (float)$product->final_price }};
    const hasColorOptions = {{ $availableColors->isNotEmpty() ? 'true' : 'false' }};
    const hasSizeOptions = {{ $availableSizes->isNotEmpty() ? 'true' : 'false' }};

    let selectedColor = null;
    let selectedSize = null;

    function selectColor(color, btn) {
        selectedColor = color;
        document.querySelectorAll('#colorSwatchesContainer .color-swatch-chip').forEach(el => el.classList.remove('active'));
        if (btn) btn.classList.add('active');
        
        const displayEl = document.getElementById('selectedColorDisplay');
        if (displayEl) displayEl.innerText = color;
        
        const inputEl = document.getElementById('selectedColorInput');
        if (inputEl) inputEl.value = color;

        // Auto switch main photo if this color/swatch has an image
        if (btn && btn.getAttribute('data-image')) {
            const imgUrl = btn.getAttribute('data-image');
            if (imgUrl) {
                changeMainImage(imgUrl, 0, null);
            }
        }

        resolveSelectedVariant();
    }

    function selectSize(size, btn) {
        selectedSize = size;
        document.querySelectorAll('#sizeChipsContainer .size-chip-btn').forEach(el => el.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const displayEl = document.getElementById('selectedSizeDisplay');
        if (displayEl) displayEl.innerText = size;

        const inputEl = document.getElementById('selectedSizeInput');
        if (inputEl) inputEl.value = size;

        resolveSelectedVariant();
    }

    function resolveSelectedVariant() {
        if (!productVariants || productVariants.length === 0) return;

        let matched = null;
        if (hasColorOptions && hasSizeOptions) {
            if (selectedColor && selectedSize) {
                matched = productVariants.find(v => v.color === selectedColor && v.size === selectedSize);
            }
        } else if (hasColorOptions) {
            if (selectedColor) {
                matched = productVariants.find(v => v.color === selectedColor);
            }
        } else if (hasSizeOptions) {
            if (selectedSize) {
                matched = productVariants.find(v => v.size === selectedSize);
            }
        }

        const summaryText = document.getElementById('variantSelectionSummary');
        const notice = document.getElementById('variantStockNotice');
        const variantIdInput = document.getElementById('selectedVariantId');

        let labelParts = [];
        if (selectedColor) labelParts.push('কালার: ' + selectedColor);
        if (selectedSize) labelParts.push('সাইজ: ' + selectedSize);
        if (summaryText) {
            summaryText.innerText = labelParts.length > 0 ? labelParts.join(' | ') : 'পছন্দ করুন';
        }

        if (matched) {
            if (variantIdInput) variantIdInput.value = matched.id;

            // Update Price if variant has specific price
            const effectivePrice = matched.effective_price || baseProductPrice;
            currentProductPrice = effectivePrice;
            const priceEl = document.getElementById('pdpPriceDisplay');
            if (priceEl) priceEl.innerText = 'Tk ' + Math.round(effectivePrice).toLocaleString('en-US');
            const stickyPrice = document.getElementById('pdpStickyPrice');
            if (stickyPrice) stickyPrice.innerText = 'Tk ' + Math.round(effectivePrice).toLocaleString('en-US');

            if (baseRegularPrice && baseRegularPrice > effectivePrice) {
                const savings = baseRegularPrice - effectivePrice;
                const savingsBadge = document.getElementById('pdpSavingsBadge');
                if (savingsBadge) {
                    savingsBadge.innerText = Math.round(savings).toLocaleString('en-US') + ' টাকা সেইভ';
                    savingsBadge.style.display = 'inline-block';
                }
            }

            // Update Stock
            const stockQty = matched.stock_quantity;
            const stockText = document.getElementById('pdpStockText');
            const qtyInput = document.getElementById('pdpQtyInput');
            if (qtyInput) {
                qtyInput.setAttribute('max', stockQty);
                if (parseInt(qtyInput.value) > stockQty) qtyInput.value = Math.max(1, stockQty);
            }

            if (stockQty > 0) {
                if (stockText) stockText.innerText = stockQty + ' টি আইটেম আছে মাত্র';
                if (notice) {
                    notice.innerText = '✓ ইন স্টক (' + stockQty + ' টি)';
                    notice.className = 'text-success fw-semibold';
                }
            } else {
                if (stockText) stockText.innerText = 'স্টক শেষ (Out of Stock)';
                if (notice) {
                    notice.innerText = '✕ স্টক আউট';
                    notice.className = 'text-danger fw-semibold';
                }
            }

            // Image switch if variant has dedicated image
            if (matched.image_url) {
                changeMainImage(matched.image_url, 0, null);
            }
        } else {
            if (variantIdInput) variantIdInput.value = '';
            currentProductPrice = baseProductPrice;
            const priceEl = document.getElementById('pdpPriceDisplay');
            if (priceEl) priceEl.innerText = 'Tk ' + Math.round(baseProductPrice).toLocaleString('en-US');
            const stickyPrice = document.getElementById('pdpStickyPrice');
            if (stickyPrice) stickyPrice.innerText = 'Tk ' + Math.round(baseProductPrice).toLocaleString('en-US');
        }
    }

    // Auto select first options on page load & add form submit validation
    document.addEventListener('DOMContentLoaded', function() {
        if (hasColorOptions) {
            const firstColor = document.querySelector('#colorSwatchesContainer .color-swatch-chip');
            if (firstColor) firstColor.click();
        }
        if (hasSizeOptions) {
            const firstSize = document.querySelector('#sizeChipsContainer .size-chip-btn');
            if (firstSize) firstSize.click();
        }

        // E-Commerce Tracking: ViewContent Event
        try {
            const productPrice = {{ (float) $product->final_price }};
            const productName = @json($product->name);
            const productId = '{{ $product->id }}';
            const categoryName = @json($product->category->name ?? 'General');

            // Meta Pixel & CAPI Hybrid Tracking: ViewContent
            if (typeof window.sidqTrackEvent === 'function') {
                window.sidqTrackEvent('ViewContent', {
                    content_name: productName,
                    content_category: categoryName,
                    content_ids: [productId],
                    content_type: 'product',
                    value: productPrice,
                    currency: 'BDT'
                });
            }

            // TikTok Pixel: ViewContent
            if (typeof ttq === 'object') {
                ttq.track('ViewContent', {
                    contents: [{
                        content_id: productId,
                        content_name: productName,
                        content_category: categoryName,
                        quantity: 1,
                        price: productPrice
                    }],
                    value: productPrice,
                    currency: 'BDT'
                });
            }

            // Google Tag Manager / GA4: view_item
            if (window.dataLayer) {
                window.dataLayer.push({
                    event: 'view_item',
                    ecommerce: {
                        currency: 'BDT',
                        value: productPrice,
                        items: [{
                            item_id: productId,
                            item_name: productName,
                            item_category: categoryName,
                            price: productPrice,
                            quantity: 1
                        }]
                    }
                });
            }
            if (typeof gtag === 'function') {
                gtag('event', 'view_item', {
                    currency: 'BDT',
                    value: productPrice,
                    items: [{
                        item_id: productId,
                        item_name: productName,
                        item_category: categoryName,
                        price: productPrice,
                        quantity: 1
                    }]
                });
            }
        } catch (e) {
            console.error('Tracking Error (ViewContent):', e);
        }

        // Product Main Image Swipe (Mobile) & Drag / Click (Desktop) - No Zoom
        const imageWrapper = document.getElementById('pdpMainImageWrapper');
        if (imageWrapper) {
            let startX = 0;
            let startY = 0;
            let isDragging = false;
            let startTime = 0;

            // Mobile touch swipe
            imageWrapper.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                    startTime = Date.now();
                }
            }, { passive: true });

            imageWrapper.addEventListener('touchend', function(e) {
                if (!startX || !galleryImages || galleryImages.length <= 1) return;
                const endX = e.changedTouches[0].clientX;
                const endY = e.changedTouches[0].clientY;
                const diffX = endX - startX;
                const diffY = endY - startY;
                const elapsed = Date.now() - startTime;

                if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                    if (diffX < 0) {
                        slidePdpImage(1); // Swipe left -> Next
                    } else {
                        slidePdpImage(-1); // Swipe right -> Prev
                    }
                } else if (Math.abs(diffX) < 12 && Math.abs(diffY) < 12 && elapsed < 350) {
                    // Tap on image advances to next image
                    slidePdpImage(1);
                }
                startX = 0;
                startY = 0;
            }, { passive: true });

            // Desktop mouse drag & click
            imageWrapper.addEventListener('mousedown', function(e) {
                if (e.target.closest('.pdp-carousel-nav')) return;
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                startTime = Date.now();
                imageWrapper.style.cursor = 'grabbing';
            });

            window.addEventListener('mouseup', function(e) {
                if (!isDragging) return;
                isDragging = false;
                imageWrapper.style.cursor = (galleryImages && galleryImages.length > 1) ? 'grab' : 'default';

                if (!galleryImages || galleryImages.length <= 1) return;
                const diffX = e.clientX - startX;
                const diffY = e.clientY - startY;
                const elapsed = Date.now() - startTime;

                if (Math.abs(diffX) > 35) {
                    if (diffX < 0) {
                        slidePdpImage(1);
                    } else {
                        slidePdpImage(-1);
                    }
                } else if (Math.abs(diffX) < 8 && Math.abs(diffY) < 8 && elapsed < 350) {
                    // Quick click on image: left 35% goes back, right 65% goes forward
                    const rect = imageWrapper.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;
                    if (clickX < rect.width * 0.35) {
                        slidePdpImage(-1);
                    } else {
                        slidePdpImage(1);
                    }
                }
            });
        }

        const orderForm = document.getElementById('pdpOrderForm');
        if (orderForm) {
            orderForm.addEventListener('submit', function(e) {
                if (hasColorOptions && !selectedColor) {
                    e.preventDefault();
                    alert('অনুগ্রহ করে আপনার পছন্দের কালার নির্বাচন করুন।');
                    document.getElementById('pdpVariationsBox')?.scrollIntoView({ behavior: 'smooth' });
                    return false;
                }
                if (hasSizeOptions && !selectedSize) {
                    e.preventDefault();
                    alert('অনুগ্রহ করে পণ্যের সাইজ নির্বাচন করুন।');
                    document.getElementById('pdpVariationsBox')?.scrollIntoView({ behavior: 'smooth' });
                    return false;
                }

                // Track AddToCart / InitiateCheckout when clicking Order Now
                try {
                    const qty = parseInt(document.getElementById('pdpQtyInput')?.value) || 1;
                    const price = (typeof currentProductPrice !== 'undefined' ? currentProductPrice : {{ (float) $product->final_price }});
                    const prodName = @json($product->name);
                    const prodId = '{{ $product->id }}';

                    if (typeof window.sidqTrackEvent === 'function') {
                        window.sidqTrackEvent('AddToCart', {
                            content_name: prodName,
                            content_ids: [prodId],
                            content_type: 'product',
                            value: price * qty,
                            currency: 'BDT'
                        });
                    }
                    if (typeof ttq === 'object') {
                        ttq.track('AddToCart', {
                            contents: [{
                                content_id: prodId,
                                content_name: prodName,
                                quantity: qty,
                                price: price
                            }],
                            value: price * qty,
                            currency: 'BDT'
                        });
                    }
                } catch (err) {
                    console.warn('Tracking OrderNow notice:', err);
                }
            });
        }
    });

    function addPdpToCart() {
        if (hasColorOptions && !selectedColor) {
            alert('অনুগ্রহ করে আপনার পছন্দের কালার নির্বাচন করুন।');
            document.getElementById('pdpVariationsBox')?.scrollIntoView({ behavior: 'smooth' });
            return;
        }
        if (hasSizeOptions && !selectedSize) {
            alert('অনুগ্রহ করে পণ্যের সাইজ নির্বাচন করুন।');
            document.getElementById('pdpVariationsBox')?.scrollIntoView({ behavior: 'smooth' });
            return;
        }

        const qtyInput = document.getElementById('pdpQtyInput');
        const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
        const variantId = document.getElementById('selectedVariantId')?.value || null;
        const price = (typeof currentProductPrice !== 'undefined' ? currentProductPrice : {{ (float) $product->final_price }});

        addToCart({{ $product->id }}, qty, variantId, {
            name: @json($product->name),
            price: price,
            value: price * qty
        });
    }

    function trackPdpCall() {
        try {
            if (typeof window.sidqTrackEvent === 'function') {
                window.sidqTrackEvent('Contact', {
                    content_name: @json($product->name),
                    contact_channel: 'phone_call'
                });
            }
        } catch(e) {}
    }

    function trackPdpWhatsApp() {
        try {
            if (typeof window.sidqTrackEvent === 'function') {
                window.sidqTrackEvent('Contact', {
                    content_name: @json($product->name),
                    contact_channel: 'whatsapp'
                });
            }
        } catch(e) {}
    }
</script>
@endpush
@endsection
