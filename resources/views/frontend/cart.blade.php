@extends('layouts.app')

@section('title', 'শপিং কার্ট - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-4">
    <h2 class="fs-4 fw-bold mb-4 border-bottom pb-2">
        <i class="fas fa-shopping-basket text-danger me-2"></i> আপনার শপিং কার্ট
    </h2>

    @if(count($items) > 0)
    <div class="row g-4">
        <!-- Cart Items Table -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm p-3 rounded-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="min-width: 220px;">পণ্য</th>
                                <th scope="col">মূল্য</th>
                                <th scope="col">পরিমাণ</th>
                                <th scope="col">মোট</th>
                                <th scope="col" class="text-end">মুছে ফেলুন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded me-3" style="width: 55px; height: 55px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('product.detail', $item['slug']) }}" class="text-dark fw-medium text-decoration-none">
                                                {{ $item['name'] }}
                                            </a>
                                            @if(!empty($item['variant_text']))
                                            <div class="small text-danger fw-semibold mt-1" style="font-size: 12px;">{{ $item['variant_text'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold tabular-nums">{{ number_format($item['unit_price'], 0) }} ৳</td>
                                <td>
                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center" style="max-width: 110px;">
                                        @csrf
                                        <input type="hidden" name="item_key" value="{{ $item['item_key'] }}">
                                        <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center me-1" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="fw-bold text-danger tabular-nums">{{ number_format($item['total_price'], 0) }} ৳</td>
                                <td class="text-end">
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="item_key" value="{{ $item['item_key'] }}">
                                        <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="মুছে ফেলুন">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('home') }}" class="btn btn-outline-dark">
                        <i class="fas fa-arrow-left me-1"></i> আরো কেনাকাটা করুন
                    </a>
                </div>
            </div>
        </div>

        <!-- Cart Summary & Checkout Link -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-light">
                <h5 class="fw-bold border-bottom pb-2 mb-3">অর্ডার সারাংশ</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">পণ্যসমূহের মূল্য:</span>
                    <strong class="tabular-nums">{{ number_format($subtotal, 0) }} ৳</strong>
                </div>

                <div class="d-flex justify-content-between mb-3 text-muted small">
                    <span>ডেলিভারি চার্জ:</span>
                    <span>চেকআউটে নির্ধারিত হবে</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5 fw-bold">মোট টাকা:</span>
                    <span class="fs-4 fw-bold text-danger tabular-nums">{{ number_format($subtotal, 0) }} ৳</span>
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-primary-sidq w-100 py-3 fs-6">
                    <i class="fas fa-check-circle me-1"></i> সরাসরি অর্ডার করুন (Proceed to Checkout)
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
        <h4>আপনার কার্ট বর্তমানে খালি!</h4>
        <p class="text-muted">পণ্য কিনতে হোমপেজে ফিরে যান।</p>
        <a href="{{ route('home') }}" class="btn btn-primary-sidq mt-2">শপিং শুরু করুন</a>
    </div>
    @endif
</div>
@endsection
