@extends('layouts.admin')

@section('title', 'অর্ডার ব্যবস্থাপনা - Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">অর্ডার ব্যবস্থাপনা (Order Management)</h3>
        <p class="text-muted small mb-0">গ্রাহকদের অর্ডারের তালিকা, স্ট্যাটাস পরিবর্তন এবং চালান প্রিন্ট করুন।</p>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div class="btn-group btn-group-sm flex-wrap" role="group">
                <a href="{{ route('admin.orders.index') }}" class="btn {{ empty($status) ? 'btn-danger' : 'btn-outline-secondary' }}">
                    সকল ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn {{ $status === 'pending' ? 'btn-danger' : 'btn-outline-secondary' }}">
                    পেন্ডিং ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn {{ $status === 'processing' ? 'btn-danger' : 'btn-outline-secondary' }}">
                    প্রসেসিং ({{ $counts['processing'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="btn {{ $status === 'shipped' ? 'btn-danger' : 'btn-outline-secondary' }}">
                    কুরিয়ারে আছে ({{ $counts['shipped'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="btn {{ $status === 'delivered' ? 'btn-danger' : 'btn-outline-secondary' }}">
                    ডেলিভার্ড ({{ $counts['delivered'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn {{ $status === 'cancelled' ? 'btn-danger' : 'btn-outline-secondary' }}">
                    বাতিল ({{ $counts['cancelled'] }})
                </a>
            </div>

            <!-- Search input -->
            <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2">
                @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <input type="text" name="search" class="form-control form-control-sm" placeholder="অর্ডার # বা ফোন নম্বর..." value="{{ $search }}" style="width: 220px;">
                <button type="submit" class="btn btn-sm btn-dark"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>অর্ডার নম্বর</th>
                        <th>গ্রাহকের বিবরণ</th>
                        <th>ডেলিভারি এরিয়া</th>
                        <th>মোট বিল</th>
                        <th>পেমেন্ট</th>
                        <th>স্ট্যাটাস</th>
                        <th>তারিখ</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-danger text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $order->customer_name }}</div>
                            <div class="small text-muted">{{ $order->customer_phone }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $order->delivery_zone === 'inside_dhaka' ? 'bg-info text-dark' : 'bg-primary' }}">
                                {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}
                            </span>
                        </td>
                        <td class="fw-bold">৳{{ number_format($order->grand_total, 0) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                            @if($order->transaction_id)
                            <div class="small text-muted">TrxID: {{ $order->transaction_id }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $order->status_badge_class }}">{{ $order->order_status }}</span>
                        </td>
                        <td class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="অর্ডার বিবরণ">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="ইনভয়েস প্রিন্ট">
                                <i class="fas fa-print"></i>
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই অর্ডারটি মুছে ফেলতে চান?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="মুছে ফেলুন">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                            কোনো অর্ডার পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
