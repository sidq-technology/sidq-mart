@extends('layouts.app')

@section('title', $category->name . ' - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
            @if($category->parent)
            <li class="breadcrumb-item"><a href="{{ route('product.category', $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="p-4 bg-light rounded-3 mb-4 border d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fs-4 fw-bold text-dark mb-1">{{ $category->name }}</h1>
            <p class="text-muted small mb-0">মোট পণ্য: {{ $products->total() }} টি</p>
        </div>
        @if($category->image)
        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded-circle border" style="width: 60px; height: 60px; object-fit: cover;">
        @endif
    </div>

    @if($products->count() > 0)
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
        @foreach($products as $product)
        <div class="col">
            @include('frontend.partials.product-card', ['product' => $product])
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
        <h5>এই ক্যাটেগরিতে বর্তমানে কোনো পণ্য নেই!</h5>
        <p class="text-muted">অন্যান্য ক্যাটেগরির পণ্য দেখতে হোমপেজে ফিরে যান।</p>
        <a href="{{ route('home') }}" class="btn btn-primary-sidq mt-2">হোমপেজে যান</a>
    </div>
    @endif
</div>
@endsection
