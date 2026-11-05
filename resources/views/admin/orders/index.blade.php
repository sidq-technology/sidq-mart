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
                    <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" class="order-table-row" id="order-row-{{ $order->id }}">
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
                            <span class="badge {{ $order->status_badge_class }} order-table-status-badge">{{ ucfirst($order->order_status) }}</span>
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
                    <span class="badge {{ $order->status_badge_class }} ms-1 order-modal-status-badge">{{ ucfirst($order->order_status) }}</span>
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
                                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-1">
                                    <div>
                                        <strong>আইপি অ্যাড্রেস:</strong> 
                                        <span class="font-monospace text-muted small">{{ $order->ip_address }}</span>
                                    </div>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill fw-semibold btn-track-ip" 
                                            data-ip="{{ $order->ip_address }}" 
                                            style="font-size: 11px;">
                                        <i class="fas fa-location-dot me-1"></i> লোকেশন দেখুন
                                    </button>
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
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="ajax-order-status-form" data-order-id="{{ $order->id }}">
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
                                        <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold btn-update-status">আপডেট</button>
                                    </div>
                                    <div class="col-12 form-status-feedback mt-1 d-none"></div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="button" 
                            class="btn btn-sm fw-bold d-inline-flex align-items-center gap-1 shadow-xs btn-send-to-sheet"
                            style="background-color: #0f9d58; border-color: #0f9d58; color: #fff;"
                            data-url="{{ route('admin.orders.send-to-sheet', $order->id) }}"
                            data-order-number="{{ $order->order_number }}">
                        <i class="fas fa-file-excel"></i>
                        <span>Send to Sheet</span>
                    </button>

                    <!-- Send to Courier Dropdown / Tracking Badge -->
                    @php
                        $couriers = $enabledCouriers ?? app(\App\Services\Courier\CourierManager::class)->getEnabledCouriers();
                    @endphp

                    <div class="courier-action-slot d-inline-block">
                        @if(!empty($order->courier_name) && !empty($order->courier_tracking_code))
                            <div class="d-inline-flex align-items-center gap-1 p-1 px-2 rounded-2 bg-success-subtle border border-success-subtle text-success small fw-bold courier-badge-container">
                                <i class="fas fa-truck-fast"></i>
                                <span>{{ ucfirst($order->courier_name) }}:</span>
                                @if($order->courier_tracking_url)
                                    <a href="{{ $order->courier_tracking_url }}" target="_blank" class="text-success text-decoration-underline" title="কুরিয়ার ট্র্যাকিং দেখুন">{{ $order->courier_tracking_code }}</a>
                                @else
                                    <span>{{ $order->courier_tracking_code }}</span>
                                @endif
                            </div>
                        @elseif(!empty($couriers))
                            <div class="dropdown d-inline-block courier-dropdown-wrapper">
                                <button class="btn btn-sm btn-primary dropdown-toggle fw-bold d-inline-flex align-items-center gap-1 shadow-xs btn-courier-dropdown" 
                                        type="button" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        style="background-color: #2563eb; border-color: #2563eb;">
                                    <i class="fas fa-truck-fast"></i>
                                    <span>Send to Courier</span>
                                </button>
                                <ul class="dropdown-menu shadow border py-1" style="font-size: 13px;">
                                    <li class="dropdown-header small text-muted text-uppercase fw-bold px-3 py-1">কুরিয়ার নির্বাচন করুন</li>
                                    @foreach($couriers as $c)
                                    <li>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 btn-submit-to-courier" 
                                                data-url="{{ route('admin.orders.send-to-courier', $order->id) }}" 
                                                data-courier="{{ $c['key'] }}"
                                                data-label="{{ $c['label'] }}">
                                            <span class="badge {{ $c['badge_class'] }} rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                                <i class="{{ $c['icon'] }}" style="font-size: 10px;"></i>
                                            </span>
                                            <span class="fw-semibold text-dark">{{ $c['label'] }}</span>
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('admin.integrations.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" title="কুরিয়ার কনফিগার করতে ক্লিক করুন">
                                <i class="fas fa-truck-fast text-muted"></i>
                                <span class="small text-muted">Configure Courier</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-dark fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                        <i class="fas fa-print me-1"></i>
                        <span>ইনভয়েস প্রিন্ট করুন</span>
                    </a>
                </div>
                <div class="w-100 order-sheet-feedback mt-2 d-none"></div>
            </div>

        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-send-to-sheet').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const orderNum = this.getAttribute('data-order-number');
            const modalFooter = this.closest('.modal-footer');
            const feedbackBox = modalFooter ? modalFooter.querySelector('.order-sheet-feedback') : null;
            const originalContent = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> পাঠাচ্ছে...';

            if (feedbackBox) {
                feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                feedbackBox.innerHTML = '<span class="spinner-border spinner-border-sm"></span> গুগল স্প্রেডশীটে অর্ডার ডেটা সিঙ্ক করা হচ্ছে...';
                feedbackBox.classList.remove('d-none');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(({ ok, body }) => {
                this.disabled = false;
                if (ok && body.success) {
                    this.innerHTML = '<i class="fas fa-check-double me-1"></i> Sent to Sheet';
                    this.style.backgroundColor = '#0b7340';
                    this.style.borderColor = '#0b7340';

                    if (feedbackBox) {
                        feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-check-circle fs-6"></i> <div>' + body.message + '</div>';
                    }
                } else {
                    this.innerHTML = originalContent;
                    if (feedbackBox) {
                        feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-exclamation-triangle fs-6"></i> <div>' + (body.message || 'ত্রুটি ঘটেছে!') + '</div>';
                    } else {
                        alert(body.message || 'ত্রুটি ঘটেছে!');
                    }
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = originalContent;
                if (feedbackBox) {
                    feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                    feedbackBox.innerHTML = '<i class="fas fa-times-circle fs-6"></i> <div>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</div>';
                } else {
                    alert('রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message);
                }
            });
        });
    });

    // AJAX Order Status & Admin Notes Update (Keeps modal open)
    document.querySelectorAll('.ajax-order-status-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const orderId = this.getAttribute('data-order-id');
            const submitBtn = this.querySelector('.btn-update-status');
            const feedbackBox = this.querySelector('.form-status-feedback');
            const modalEl = this.closest('.modal');
            const modalBadge = modalEl ? modalEl.querySelector('.order-modal-status-badge') : null;
            const tableRow = document.getElementById('order-row-' + orderId);
            const tableBadge = tableRow ? tableRow.querySelector('.order-table-status-badge') : null;
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'আপডেট';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(({ ok, body }) => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }

                if (ok && body.success) {
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> সেভ হয়েছে';
                        submitBtn.classList.remove('btn-dark');
                        submitBtn.classList.add('btn-success');
                        setTimeout(() => {
                            submitBtn.innerHTML = originalBtnHtml;
                            submitBtn.classList.remove('btn-success');
                            submitBtn.classList.add('btn-dark');
                        }, 2500);
                    }

                    // Update Status Badges in Modal and Table
                    if (body.status_label && body.status_badge_class) {
                        if (modalBadge) {
                            modalBadge.className = 'badge ' + body.status_badge_class + ' ms-1 order-modal-status-badge';
                            modalBadge.textContent = body.status_label;
                        }
                        if (tableBadge) {
                            tableBadge.className = 'badge ' + body.status_badge_class + ' order-table-status-badge';
                            tableBadge.textContent = body.status_label;
                        }
                    }

                    if (feedbackBox) {
                        feedbackBox.className = 'col-12 form-status-feedback mt-2 alert alert-success py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-check-circle text-success"></i> <span>' + body.message + '</span>';
                        feedbackBox.classList.remove('d-none');
                        setTimeout(() => {
                            feedbackBox.classList.add('d-none');
                        }, 3500);
                    }
                } else {
                    if (submitBtn) submitBtn.innerHTML = originalBtnHtml;
                    if (feedbackBox) {
                        feedbackBox.className = 'col-12 form-status-feedback mt-2 alert alert-danger py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> <span>' + (body.message || 'আপডেট করতে সমস্যা হয়েছে') + '</span>';
                        feedbackBox.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
                if (feedbackBox) {
                    feedbackBox.className = 'col-12 form-status-feedback mt-2 alert alert-danger py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                    feedbackBox.innerHTML = '<i class="fas fa-times-circle text-danger"></i> <span>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</span>';
                    feedbackBox.classList.remove('d-none');
                }
            });
        });
    });

    // Handle Courier Booking via AJAX
    document.querySelectorAll('.btn-submit-to-courier').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const courier = this.getAttribute('data-courier');
            const courierLabel = this.getAttribute('data-label');
            const modalFooter = this.closest('.modal-footer');
            const slot = modalFooter ? modalFooter.querySelector('.courier-action-slot') : null;
            const dropdownBtn = modalFooter ? modalFooter.querySelector('.btn-courier-dropdown') : null;
            const feedbackBox = modalFooter ? modalFooter.querySelector('.order-sheet-feedback') : null;
            const modalEl = this.closest('.modal');
            const modalBadge = modalEl ? modalEl.querySelector('.order-modal-status-badge') : null;

            if (dropdownBtn) {
                dropdownBtn.disabled = true;
                dropdownBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> বুকিং হচ্ছে...';
            }

            if (feedbackBox) {
                feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                feedbackBox.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ' + courierLabel + '-এ পার্সেল বুকিং করা হচ্ছে...';
                feedbackBox.classList.remove('d-none');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ courier: courier })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(({ ok, body }) => {
                if (ok && body.success) {
                    if (slot) {
                        const trkUrl = body.tracking_url;
                        const trkLink = trkUrl 
                            ? `<a href="${trkUrl}" target="_blank" class="text-success text-decoration-underline">${body.tracking_code}</a>` 
                            : `<span>${body.tracking_code}</span>`;

                        slot.innerHTML = `
                            <div class="d-inline-flex align-items-center gap-1 p-1 px-2 rounded-2 bg-success-subtle border border-success-subtle text-success small fw-bold">
                                <i class="fas fa-truck-fast"></i>
                                <span>${body.courier_name.toUpperCase()}:</span>
                                ${trkLink}
                            </div>
                        `;
                    }

                    if (modalBadge && body.status_badge_class) {
                        modalBadge.className = 'badge ' + body.status_badge_class + ' ms-1 order-modal-status-badge';
                        modalBadge.textContent = 'Shipped';
                    }

                    if (feedbackBox) {
                        feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-check-circle fs-6"></i> <div>' + body.message + '</div>';
                    }
                } else {
                    if (dropdownBtn) {
                        dropdownBtn.disabled = false;
                        dropdownBtn.innerHTML = '<i class="fas fa-truck-fast"></i> <span>Send to Courier</span>';
                    }
                    if (feedbackBox) {
                        feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                        feedbackBox.innerHTML = '<i class="fas fa-exclamation-triangle fs-6"></i> <div>' + (body.message || 'বুকিং ব্যর্থ হয়েছে') + '</div>';
                    }
                }
            })
            .catch(err => {
                if (dropdownBtn) {
                    dropdownBtn.disabled = false;
                    dropdownBtn.innerHTML = '<i class="fas fa-truck-fast"></i> <span>Send to Courier</span>';
                }
                if (feedbackBox) {
                    feedbackBox.className = 'w-100 order-sheet-feedback mt-2 alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-0';
                    feedbackBox.innerHTML = '<i class="fas fa-times-circle fs-6"></i> <div>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</div>';
                }
            });
        });
    });
});
</script>
@endpush

@endsection
