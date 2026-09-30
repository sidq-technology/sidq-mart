@extends('layouts.app')

@section('title', 'অর্ডার সফল হয়েছে - ' . $order->order_number . ' | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 text-center mb-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 72px; height: 72px;">
                        <i class="fas fa-check fa-2x"></i>
                    </span>
                </div>

                <h2 class="fw-bold text-dark mb-1">অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!</h2>
                <p class="text-muted">আপনার অর্ডারের জন্য আন্তরিক ধন্যবাদ। আমাদের প্রতিনিধি শীঘ্রই আপনার সাথে যোগাযোগ করবেন।</p>

                <div class="alert alert-light border py-3 my-3">
                    <span class="text-muted d-block small">অর্ডার রেফারেন্স নম্বর:</span>
                    <strong class="fs-4 text-danger">{{ $order->order_number }}</strong>
                </div>

                <!-- Order Details Summary -->
                <div class="text-start mt-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">ডেলিভারির তথ্য</h5>
                    <div class="row g-2 small mb-4">
                        <div class="col-sm-6">
                            <span class="text-muted">গ্রাহকের নাম:</span>
                            <div class="fw-bold">{{ $order->customer_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">মোবাইল নম্বর:</span>
                            <div class="fw-bold">{{ $order->customer_phone }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <span class="text-muted">ডেলিভারি ঠিকানা:</span>
                            <div class="fw-bold">{{ $order->shipping_address }}</div>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted">পেমেন্ট মেথড:</span>
                            <div class="fw-bold text-uppercase">{{ $order->payment_method }} ({{ $order->payment_status }})</div>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted">অর্ডার স্ট্যাটাস:</span>
                            <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                        </div>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3">অর্ডারকৃত পণ্যসমূহ</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>পণ্য</th>
                                    <th>মূল্য</th>
                                    <th>পরিমাণ</th>
                                    <th class="text-end">মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product_image)
                                            <img src="{{ $item->product_image }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <span class="fw-medium">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td>৳{{ number_format($item->unit_price, 0) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($item->total_price, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th colspan="3" class="text-end">সাবটোটাল:</th>
                                    <th class="text-end">৳{{ number_format($order->subtotal, 0) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">ডেলিভারি চার্জ:</th>
                                    <th class="text-end">৳{{ number_format($order->shipping_charge, 0) }}</th>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <th colspan="3" class="text-end text-success">ডিসকাউন্ট:</th>
                                    <th class="text-end text-success">-৳{{ number_format($order->discount_amount, 0) }}</th>
                                </tr>
                                @endif
                                <tr class="fs-5">
                                    <th colspan="3" class="text-end text-danger">সর্বমোট বিল:</th>
                                    <th class="text-end text-danger">৳{{ number_format($order->grand_total, 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('home') }}" class="btn btn-secondary-sidq">
                            <i class="fas fa-home me-1"></i> হোম পেজে যান
                        </a>
                        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> প্রিন্ট ইনভয়েস
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
