@extends('layouts.admin')

@section('title', 'অর্ডার ব্যবস্থাপনা - Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">অর্ডার ব্যবস্থাপনা (Order Management)</h3>
        <p class="text-muted small mb-0">গ্রাহকদের অর্ডারের তালিকা, স্ট্যাটাস পরিবর্তন এবং চালান প্রিন্ট করুন। যেকোনো অর্ডারে ক্লিক করে বিস্তারিত পপআপে দেখুন।</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
    <i class="fas fa-check-circle fs-5"></i>
    <div class="fw-semibold">{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

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

<!-- Orders Table Card -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">অর্ডার নম্বর</th>
                        <th>গ্রাহকের বিবরণ</th>
                        <th>ডেলিভারি এরিয়া</th>
                        <th>মোট বিল</th>
                        <th>পেমেন্ট</th>
                        <th>স্ট্যাটাস</th>
                        <th>তারিখ</th>
                        <th class="text-end pe-3">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <!-- Entire row is clickable to trigger the popup modal -->
                    <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" class="order-table-row">
                        <td class="ps-3">
                            <span class="fw-bold text-danger">
                                {{ $order->order_number }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $order->customer_name }}</div>
                            <div class="small text-muted font-monospace">{{ $order->customer_phone }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $order->delivery_zone === 'inside_dhaka' ? 'bg-info text-dark' : 'bg-primary' }}">
                                {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">৳{{ number_format($order->grand_total, 0) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                            @if($order->transaction_id)
                            <div class="small text-muted font-monospace">TrxID: {{ $order->transaction_id }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $order->status_badge_class }}">{{ $order->order_status }}</span>
                        </td>
                        <td class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-end pe-3" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" title="অর্ডার বিবরণ">
                                <i class="fas fa-eye"></i>
                            </button>
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

<!-- ==========================================
     ORDER DETAILS POPUP MODALS
     (Rendered outside table to prevent browser DOM foster-parenting spill)
=========================================== -->
@foreach($orders as $order)
@php
    $cleanPhone = preg_replace('/[^\d]/', '', $order->customer_phone);
    if (str_starts_with($cleanPhone, '01')) {
        $cleanPhone = '88' . $cleanPhone;
    }
    $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode("আসসালামু আলাইকুম {$order->customer_name}, SIDQ MART-এ আপনার অর্ডার (#{$order->order_number}) সংক্রান্ত তথ্যের জন্য যোগাযোগ করছি।");
@endphp

<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-3 border-0 shadow">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">Order #{{ $order->order_number }}</span>
                    <h6 class="modal-title fw-bold text-dark mb-0">অর্ডার বিবরণ ও ব্যবস্থাপনা</h6>
                    <span class="badge {{ $order->status_badge_class }} ms-1">{{ ucfirst($order->order_status) }}</span>
                    <span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span>
                    <span class="text-muted small ms-2">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body (Two-Column Layout exactly like Failed Orders popup) -->
            <div class="modal-body p-4">
                <div class="row g-3">
                    
                    <!-- Left: Customer Info Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle text-primary me-1"></i> কাস্টমার তথ্য
                            </h6>
                            <div class="d-flex flex-column gap-2 small">
                                <div><strong>নাম:</strong> <span class="text-dark">{{ $order->customer_name }}</span></div>
                                <div>
                                    <strong>ফোন:</strong> 
                                    @if($order->customer_phone)
                                        <a href="tel:{{ $order->customer_phone }}" class="font-monospace text-primary fw-bold text-decoration-none">
                                            {{ $order->customer_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">নেই</span>
                                    @endif
                                </div>
                                <div><strong>ঠিকানা:</strong> <span class="text-dark">{{ $order->shipping_address }}</span></div>
                                <div>
                                    <strong>ডেলিভারি এরিয়া:</strong> 
                                    <span class="badge {{ $order->delivery_zone === 'inside_dhaka' ? 'bg-info text-dark' : 'bg-primary' }}">
                                        {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}
                                    </span>
                                </div>
                                <div>
                                    <strong>পেমেন্ট মেথড:</strong> 
                                    <span class="badge bg-white text-dark border">{{ strtoupper($order->payment_method) }}</span>
                                    @if($order->transaction_id)
                                    <span class="text-muted font-monospace ms-1">(TrxID: {{ $order->transaction_id }})</span>
                                    @endif
                                </div>
                                @if($order->customer_note)
                                <div><strong>গ্রাহকের নোট:</strong> <em class="text-dark">{{ $order->customer_note }}</em></div>
                                @endif
                                @if($order->ip_address)
                                <div class="mt-2 pt-2 border-top">
                                    <strong>আইপি অ্যাড্রেস:</strong> <span class="font-monospace text-muted">{{ $order->ip_address }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Call / WhatsApp Action Bar inside modal -->
                            @if(!empty($order->customer_phone))
                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                <a href="tel:{{ $order->customer_phone }}" class="btn btn-sm btn-success flex-grow-1 fw-bold">
                                    <i class="fas fa-phone-alt me-1"></i> সরাসরি কল
                                </a>
                                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm text-white flex-grow-1 fw-bold" style="background: #25D366;">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Ordered Items Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-shopping-cart text-danger me-1"></i> অর্ডারকৃত পণ্যসমূহ ({{ count($order->items) }} টি)
                            </h6>
                            <div class="d-flex flex-column gap-2 mb-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($order->items as $item)
                                @php
                                    $productUrl = ($item->product && $item->product->slug) ? route('product.detail', $item->product->slug) : null;
                                    $imgUrl = $item->product_image ?: ($item->product ? $item->product->primary_image_url : null);
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-2 border gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 0;">
                                        @if(!empty($imgUrl))
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" target="_blank" title="লাইভ প্রোডাক্ট পেজ খুলুন" class="flex-shrink-0">
                                                    <img src="{{ $imgUrl }}" alt="" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                                </a>
                                            @else
                                                <img src="{{ $imgUrl }}" alt="" class="rounded border flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                                            @endif
                                        @endif
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" target="_blank" class="fw-semibold text-dark text-decoration-none d-inline-flex align-items-center gap-1 hover-text-primary" style="font-size: 12px; line-height: 1.35;" title="লাইভ প্রোডাক্ট পেজ দেখুন">
                                                    <span>{{ $item->product_name }}</span>
                                                    <i class="fas fa-external-link-alt text-primary flex-shrink-0 ms-1" style="font-size: 10px;"></i>
                                                </a>
                                            @else
                                                <div class="fw-semibold text-dark" style="font-size: 12px; line-height: 1.35;">{{ $item->product_name }}</div>
                                            @endif
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                <span>৳{{ number_format($item->unit_price, 0) }} × {{ $item->quantity }}</span>
                                                @if($item->product && $item->product->sku)
                                                <span class="font-monospace text-secondary ms-1">({{ $item->product->sku }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ps-2">
                                        <span class="fw-bold text-dark small d-block">৳{{ number_format($item->total_price, 0) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Financial Breakdown -->
                            <div class="border-top pt-2 small">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>সাব-টোটাল:</span>
                                    <span>৳{{ number_format($order->subtotal, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span>৳{{ number_format($order->shipping_charge, 0) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between text-success mb-1">
                                    <span>কুপন ডিসকাউন্ট:</span>
                                    <span>-৳{{ number_format($order->discount_amount, 0) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6 mt-1 border-top pt-1">
                                    <span>সর্বমোট বিল:</span>
                                    <span class="text-danger">৳{{ number_format($order->grand_total, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Quick Status Manager Form -->
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-clipboard-check text-secondary me-1"></i> অর্ডার স্ট্যাটাস ও অভ্যন্তরীণ নোট
                            </h6>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label small fw-semibold text-dark mb-1">অর্ডার স্ট্যাটাস:</label>
                                        <select name="order_status" class="form-select form-select-sm">
                                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending (পেন্ডিং)</option>
                                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing (প্রসেসিং)</option>
                                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped (কুরিয়ারে আছে)</option>
                                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered (ডেলিভার্ড)</option>
                                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled (বাতিল)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">অ্যাডমিন নোট:</label>
                                        <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="প্রয়োজনে অভ্যন্তরীণ নোট লিখুন..." value="{{ $order->admin_notes }}">
                                    </div>
                                    <div class="col-12 col-sm-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">আপডেট</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-dark fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                        <i class="fas fa-print me-1"></i>
                        <span>ইনভয়েস প্রিন্ট করুন</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection
