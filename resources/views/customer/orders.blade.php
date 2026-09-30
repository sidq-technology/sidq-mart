@extends('layouts.app')

@section('title', 'আমার সকল অর্ডার - ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="py-5" style="background: #f8fffa; min-height: 80vh;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-dark">আমার সকল অর্ডার (My Orders)</h3>
                <p class="text-muted small mb-0">আপনার দেওয়া সকল অর্ডারের তালিকা ও ডেলিভারি স্ট্যাটাস ট্র্যাকিং।</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> ড্যাশবোর্ডে ফিরুন
            </a>
        </div>

        <!-- Orders Table Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fw-bold mb-0 text-dark">মোট অর্ডার ({{ $orders->total() }})</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm {{ !request('status') ? 'btn-danger' : 'btn-light border' }}">সকল</a>
                    <a href="{{ route('customer.orders', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-danger' : 'btn-light border' }}">পেন্ডিং</a>
                    <a href="{{ route('customer.orders', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') === 'processing' ? 'btn-danger' : 'btn-light border' }}">প্রসেসিং</a>
                    <a href="{{ route('customer.orders', ['status' => 'delivered']) }}" class="btn btn-sm {{ request('status') === 'delivered' ? 'btn-danger' : 'btn-light border' }}">ডেলিভার্ড</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">অর্ডার নম্বর</th>
                                <th>তারিখ</th>
                                <th>পণ্য সংখ্যা</th>
                                <th>পেমেন্ট পদ্ধতি</th>
                                <th>মোট টাকা</th>
                                <th>ডেলিভারি স্ট্যাটাস</th>
                                <th class="text-end pe-4">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="fw-bold text-decoration-none" style="color: var(--color-brand-accent);">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="fw-semibold">{{ $order->items->count() ?? 1 }} টি</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                </td>
                                <td class="fw-bold text-dark">৳{{ number_format($order->grand_total, 0) }}</td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }}">{{ $order->order_status }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-light border text-primary">
                                        <i class="fas fa-eye me-1"></i> বিস্তারিত ও চালান
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    কোনো অর্ডার পাওয়া যায়নি।
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
