@extends('layouts.app')

@section('title', 'অনুসন্ধানের ফলাফল: ' . $query . ' - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4">
    <div class="p-4 bg-light rounded-3 mb-4 border">
        <h1 class="fs-4 fw-bold text-dark mb-1">
            <i class="fas fa-search text-danger me-2"></i> অনুসন্ধানের ফলাফল: "{{ $query }}"
        </h1>
        <p class="text-muted small mb-0">মোট পাওয়া গেছে: {{ $products->total() }} টি পণ্য</p>
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
        <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
        <h5>দুঃখিত! আপনার কাঙ্ক্ষিত পণ্যটি খুঁজে পাওয়া যায়নি।</h5>
        <p class="text-muted">বানান সঠিক আছে কিনা নিশ্চিত করুন অথবা অন্য কোনো শব্দ দিয়ে অনুসন্ধান করুন।</p>
        <a href="{{ route('home') }}" class="btn btn-primary-sidq mt-2">সকল পণ্য দেখুন</a>
    </div>
    @endif
</div>
@endsection
