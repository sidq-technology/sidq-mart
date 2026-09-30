@extends('layouts.app')

@section('title', $product->name . ' - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('product.category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 250px;">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- 1. Product Images Gallery -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="text-center mb-3">
                    <img id="mainProductImg" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-fluid rounded-3" style="max-height: 440px; width: 100%; object-fit: contain;">
                </div>

                @if($product->galleryImages->count() > 0)
                <div class="d-flex gap-2 overflow-auto pb-2">
                    <img src="{{ $product->primary_image_url }}" alt="Thumb" class="rounded border p-1 thumb-nav active" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="changeMainImage('{{ $product->primary_image_url }}', this)">
                    @foreach($product->galleryImages as $gImg)
                    <img src="{{ $gImg->image_url }}" alt="Thumb" class="rounded border p-1 thumb-nav" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="changeMainImage('{{ $gImg->image_url }}', this)">
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- 2. Product Information & Purchase Block -->
        <div class="col-12 col-md-6">
            <div class="p-2">
                <h1 class="fs-4 fw-bold text-dark mb-2 lh-base">{{ $product->name }}</h1>

                <div class="d-flex align-items-center gap-3 mb-3 small text-muted">
                    @if($product->sku)
                    <span>SKU: <strong class="text-dark">{{ $product->sku }}</strong></span>
                    @endif
                    @if($product->category)
                    <span>ক্যাটেগরি: <a href="{{ route('product.category', $product->category->slug) }}" class="text-danger fw-medium">{{ $product->category->name }}</a></span>
                    @endif
                </div>

                <!-- Price Area -->
                <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-baseline gap-3">
                    <span class="fs-2 fw-bold text-danger tabular-nums">Tk {{ number_format($product->current_price, 0) }}</span>
                    @if($product->sale_price && $product->regular_price > $product->sale_price)
                    <del class="text-muted fs-5 tabular-nums">Tk {{ number_format($product->regular_price, 0) }}</del>
                    <span class="badge bg-danger fs-6">-{{ $product->discount_percent }}% ছাড়</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="mb-3">
                    @if($product->stock_quantity > 0)
                    <span class="badge bg-success py-2 px-3"><i class="fas fa-check-circle me-1"></i> ইন স্টক (In Stock)</span>
                    @else
                    <span class="badge bg-secondary py-2 px-3"><i class="fas fa-times-circle me-1"></i> স্টক শেষ (Out of Stock)</span>
                    @endif
                </div>

                @if($product->short_description)
                <div class="mb-4 text-secondary">
                    {!! nl2br(e($product->short_description)) !!}
                </div>
                @endif

                <!-- Purchase Actions -->
                <div class="border-top border-bottom py-3 mb-4">
                    <form action="{{ route('buy.now', $product->slug) }}" method="POST" id="pdpOrderForm">
                        @csrf
                        <div class="row align-items-center g-3 mb-3">
                            <label class="col-auto form-label mb-0 fw-bold">পরিমাণ (Quantity):</label>
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
                                <i class="fas fa-bolt me-1"></i> সরাসরি অর্ডার করুন (Order Now)
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

@push('scripts')
<script>
    function changeMainImage(src, element) {
        document.getElementById('mainProductImg').src = src;
        document.querySelectorAll('.thumb-nav').forEach(el => el.classList.remove('active', 'border-danger'));
        element.classList.add('active', 'border-danger');
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

    function addPdpToCart() {
        const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
        addToCart({{ $product->id }}, qty);
    }
</script>
@endpush
@endsection
