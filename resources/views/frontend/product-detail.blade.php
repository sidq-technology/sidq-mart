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
@endphp

<div class="container my-4">
    <div class="row g-4">
        <!-- 1. Product Images Gallery -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm p-3 rounded-3 position-relative">
                
                <!-- Main Image with Hover Zoom Hint and Lightbox Trigger -->
                <div class="position-relative text-center mb-3 main-image-container rounded-3 overflow-hidden" 
                     style="cursor: zoom-in; background: #fafafa; border: 1px solid #f1f5f9; min-height: 320px; display: flex; align-items: center; justify-content: center;"
                     onclick="openImageLightbox()"
                     title="ছবিটি বড় করে দেখতে ক্লিক করুন">
                    
                    <img id="mainProductImg" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                         class="img-fluid rounded-3" 
                         style="max-height: 440px; width: 100%; object-fit: contain; transition: opacity 0.2s ease, transform 0.2s ease;">
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
                    <span class="badge fs-6 px-2 py-1 fw-bold text-white shadow-2xs" id="pdpSavingsBadge" style="background-color: #16a34a;">{{ number_format($savings, 0) }} টাকা সেইভ</span>
                    @endif
                </div>

                <!-- Stock Status -->
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

                @if($product->short_description)
                <div class="mb-4 text-secondary">
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

                        <div class="d-grid gap-3 d-sm-flex">
                            <!-- Direct Checkout / Buy Now -->
                            <button type="submit" class="btn btn-primary-sidq flex-fill py-3 fs-6">
                                <i class="fas fa-bolt me-1"></i> অর্ডার করুন
                            </button>

                            <!-- Add to Cart -->
                            <button type="button" class="btn btn-secondary-sidq flex-fill py-3 fs-6" onclick="addPdpToCart()">
                                <i class="fas fa-shopping-basket me-1"></i> কার্টে যোগ করুন
                            </button>
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
     PRODUCT IMAGE FULL VIEW LIGHTBOX MODAL
=========================================== -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(8px); background: rgba(0, 0, 0, 0.92);">
    <div class="modal-dialog modal-fullscreen m-0 p-0 border-0">
        <div class="modal-content bg-transparent border-0 text-white position-relative d-flex flex-column justify-content-between h-100">
            
            <!-- Top Controls Bar -->
            <div class="d-flex justify-content-between align-items-center p-3 p-md-4 w-100 position-relative z-3" style="background: linear-gradient(to bottom, rgba(0,0,0,0.85), transparent);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark bg-opacity-75 text-white border border-secondary px-3 py-2 rounded-pill font-monospace" id="lightboxCounter" style="font-size: 13px;">
                        1 / {{ $allGalleryImages->count() }}
                    </span>
                    <span class="d-none d-md-inline small text-light opacity-75 text-truncate" style="max-width: 400px;">{{ $product->name }}</span>
                </div>
                
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-dark btn-sm rounded-circle p-2 border-0 bg-opacity-75 text-white" onclick="toggleLightboxZoom()" title="জুম করুন / স্বাভাবিক আকার">
                        <i class="fas fa-search-plus fs-5" id="lightboxZoomIcon"></i>
                    </button>
                    <button type="button" class="btn btn-dark btn-sm rounded-circle p-2 border-0 bg-opacity-75 text-white" data-bs-dismiss="modal" aria-label="Close" title="বন্ধ করুন">
                        <i class="fas fa-times fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Main Fullscreen Image Area -->
            <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative px-2 px-md-4 overflow-hidden" onclick="handleBackdropClick(event)">
                @if($allGalleryImages->count() > 1)
                <button type="button" class="btn btn-dark rounded-circle p-3 position-absolute start-0 ms-2 ms-md-4 border-0 shadow bg-opacity-75 text-white z-3" onclick="prevLightboxImage(event)" title="পূর্ববর্তী ছবি">
                    <i class="fas fa-chevron-left fs-4"></i>
                </button>
                @endif

                <div class="lightbox-img-wrapper d-flex align-items-center justify-content-center" style="max-height: 80vh; max-width: 92vw;">
                    <img id="lightboxMainImg" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                         class="img-fluid rounded-2 shadow-lg" 
                         style="max-height: 80vh; max-width: 92vw; object-fit: contain; cursor: zoom-in; transition: transform 0.25s ease;" 
                         onclick="toggleLightboxZoom()">
                </div>

                @if($allGalleryImages->count() > 1)
                <button type="button" class="btn btn-dark rounded-circle p-3 position-absolute end-0 me-2 me-md-4 border-0 shadow bg-opacity-75 text-white z-3" onclick="nextLightboxImage(event)" title="পরবর্তী ছবি">
                    <i class="fas fa-chevron-right fs-4"></i>
                </button>
                @endif
            </div>

            <!-- Bottom Thumbnails Strip (if multiple images) -->
            @if($allGalleryImages->count() > 1)
            <div class="p-3 w-100 d-flex justify-content-center overflow-auto position-relative z-3" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                <div class="d-flex gap-2">
                    @foreach($allGalleryImages as $idx => $imgUrl)
                    <img src="{{ $imgUrl }}" alt="Thumb {{ $idx + 1 }}" 
                         class="rounded border border-2 lightbox-thumb" 
                         style="width: 58px; height: 58px; object-fit: cover; cursor: pointer; opacity: {{ $loop->first ? '1' : '0.45' }}; border-color: {{ $loop->first ? '#dc2626' : 'transparent' }} !important; transition: all 0.2s ease;" 
                         onclick="setLightboxImage({{ $idx }})">
                    @endforeach
                </div>
            </div>
            @else
            <div class="p-2"></div>
            @endif

        </div>
    </div>
</div>

<style>
.main-image-container:hover #mainProductImg {
    transform: scale(1.02);
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
</style>

@push('scripts')
<script>
    const galleryImages = @json($allGalleryImages->values());
    let currentImageIndex = 0;
    let isZoomed = false;
    let lightboxModalInstance = null;

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
        document.querySelectorAll('#pdpThumbnailStrip .thumb-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }
    }

    function openImageLightbox() {
        const modalEl = document.getElementById('imageLightboxModal');
        if (!modalEl) return;
        
        if (!lightboxModalInstance) {
            lightboxModalInstance = new bootstrap.Modal(modalEl);
        }
        
        setLightboxImage(currentImageIndex);
        lightboxModalInstance.show();
    }

    function setLightboxImage(index) {
        if (!galleryImages || galleryImages.length === 0) return;
        if (index < 0) index = galleryImages.length - 1;
        if (index >= galleryImages.length) index = 0;
        
        currentImageIndex = index;
        const targetSrc = galleryImages[currentImageIndex];
        
        const lbImg = document.getElementById('lightboxMainImg');
        if (lbImg) {
            lbImg.style.opacity = '0.35';
            const temp = new Image();
            temp.src = targetSrc;
            temp.onload = function() {
                lbImg.src = targetSrc;
                lbImg.style.opacity = '1';
            };
            temp.onerror = function() {
                lbImg.src = targetSrc;
                lbImg.style.opacity = '1';
            };
        }

        // Reset zoom state
        resetLightboxZoom();

        // Update Counter
        const counter = document.getElementById('lightboxCounter');
        if (counter) {
            counter.innerText = (currentImageIndex + 1) + ' / ' + galleryImages.length;
        }

        // Update Lightbox thumbnails
        document.querySelectorAll('.lightbox-thumb').forEach((thumb, i) => {
            if (i === currentImageIndex) {
                thumb.style.opacity = '1';
                thumb.style.borderColor = '#dc2626';
            } else {
                thumb.style.opacity = '0.45';
                thumb.style.borderColor = 'transparent';
            }
        });

        // Sync main page image & thumbnail
        const pageMainImg = document.getElementById('mainProductImg');
        if (pageMainImg) pageMainImg.src = targetSrc;
        const pageThumbs = document.querySelectorAll('#pdpThumbnailStrip .thumb-item');
        if (pageThumbs && pageThumbs[currentImageIndex]) {
            pageThumbs.forEach(t => t.classList.remove('active'));
            pageThumbs[currentImageIndex].classList.add('active');
        }
    }

    function prevLightboxImage(e) {
        if (e) e.stopPropagation();
        setLightboxImage(currentImageIndex - 1);
    }

    function nextLightboxImage(e) {
        if (e) e.stopPropagation();
        setLightboxImage(currentImageIndex + 1);
    }

    function toggleLightboxZoom() {
        const lbImg = document.getElementById('lightboxMainImg');
        const zoomIcon = document.getElementById('lightboxZoomIcon');
        if (!lbImg) return;

        isZoomed = !isZoomed;
        if (isZoomed) {
            lbImg.style.transform = 'scale(1.75)';
            lbImg.style.cursor = 'zoom-out';
            if (zoomIcon) {
                zoomIcon.classList.remove('fa-search-plus');
                zoomIcon.classList.add('fa-search-minus');
            }
        } else {
            resetLightboxZoom();
        }
    }

    function resetLightboxZoom() {
        isZoomed = false;
        const lbImg = document.getElementById('lightboxMainImg');
        const zoomIcon = document.getElementById('lightboxZoomIcon');
        if (lbImg) {
            lbImg.style.transform = 'scale(1)';
            lbImg.style.cursor = 'zoom-in';
        }
        if (zoomIcon) {
            zoomIcon.classList.remove('fa-search-minus');
            zoomIcon.classList.add('fa-search-plus');
        }
    }

    function handleBackdropClick(e) {
        if (e.target === e.currentTarget) {
            if (lightboxModalInstance) lightboxModalInstance.hide();
        }
    }

    // Keyboard navigation (ArrowLeft, ArrowRight, Escape)
    document.addEventListener('keydown', function(e) {
        const modalEl = document.getElementById('imageLightboxModal');
        if (modalEl && modalEl.classList.contains('show')) {
            if (e.key === 'ArrowLeft') prevLightboxImage();
            else if (e.key === 'ArrowRight') nextLightboxImage();
        }
    });

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
            const priceEl = document.getElementById('pdpPriceDisplay');
            if (priceEl) priceEl.innerText = 'Tk ' + Math.round(effectivePrice).toLocaleString('en-US');

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

        const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
        const variantId = document.getElementById('selectedVariantId')?.value || null;
        addToCart({{ $product->id }}, qty, variantId);
    }
</script>
@endpush
@endsection
