@extends('layouts.admin')

@section('title', 'ফেইল্ড ও পরিত্যক্ত চেকআউট — Failed Orders')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-white text-danger border px-2 py-1 small fw-semibold">
                    <i class="fas fa-exclamation-circle me-1"></i> Abandoned Checkout Recovery
                </span>
                <span class="text-muted small">গ্রাহকদের অসমাপ্ত ও ড্রাফট অর্ডার ট্র্যাকিং</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">ফেইল্ড ও পরিত্যক্ত চেকআউট (Failed Orders)</h1>
            <p class="text-muted small mb-0">গ্রাহক চেকআউটে এসে নাম-ঠিকানা বা ফোন লিখে অর্ডার সম্পন্ন না করলে (বাটন না চাপলে) স্বয়ংক্রিয়ভাবে এখানে তালিকাভুক্ত হয়। যেকোনো অর্ডারে ক্লিক করলে পপআপে সম্পূর্ণ বিবরণ দেখা যাবে।</p>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
        <i class="fas fa-check-circle fs-5"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <div class="fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPI & Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">অসম্পূর্ণ / ফেইল্ড অর্ডার</span>
                        <h3 class="fw-bold text-danger mb-0">{{ number_format($stats['total_unrecovered']) }} <span class="fs-6 fw-normal text-muted">টি</span></h3>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 20px;">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">আজকের অসম্পূর্ণ অর্ডার</span>
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['today_unrecovered']) }} <span class="fs-6 fw-normal text-muted">টি</span></h3>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(245, 158, 11, 0.12); color: #f59e0b; font-size: 20px;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">সম্ভাব্য বিক্রয় ক্ষতি (Lost Revenue)</span>
                        <h3 class="fw-bold text-dark mb-0">৳{{ number_format($stats['lost_revenue'], 2) }}</h3>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(99, 102, 241, 0.12); color: #6366f1; font-size: 20px;">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 bg-white rounded-3 shadow-xs p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">পুনরুদ্ধারকৃত অর্ডার (Recovered)</span>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_recovered']) }} <span class="fs-6 fw-normal text-muted">টি</span></h3>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 20px;">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 bg-white rounded-3 shadow-xs mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
                
                <!-- Status Filter Pills -->
                <div class="btn-group btn-group-sm flex-wrap" role="group">
                    <a href="{{ route('admin.failed-orders.index') }}" class="btn {{ !request()->has('status') ? 'btn-danger' : 'btn-outline-secondary' }}">
                        সকল রেকর্ড
                    </a>
                    <a href="{{ route('admin.failed-orders.index', ['status' => 'unrecovered']) }}" class="btn {{ request('status') === 'unrecovered' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        অসম্পূর্ণ / পেন্ডিং ({{ $stats['total_unrecovered'] }})
                    </a>
                    <a href="{{ route('admin.failed-orders.index', ['status' => 'abandoned']) }}" class="btn {{ request('status') === 'abandoned' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        বাটনে চাপেনি (Abandoned)
                    </a>
                    <a href="{{ route('admin.failed-orders.index', ['status' => 'attempted']) }}" class="btn {{ request('status') === 'attempted' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        প্লেস ব্যর্থ (Attempted)
                    </a>
                    <a href="{{ route('admin.failed-orders.index', ['status' => 'contacted']) }}" class="btn {{ request('status') === 'contacted' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        যোগাযোগ সম্পন্ন
                    </a>
                    <a href="{{ route('admin.failed-orders.index', ['status' => 'recovered']) }}" class="btn {{ request('status') === 'recovered' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        অর্ডারে রূপান্তর (Recovered)
                    </a>
                </div>

                <!-- Search form -->
                <form action="{{ route('admin.failed-orders.index') }}" method="GET" class="d-flex align-items-center gap-2 w-100 w-lg-auto">
                    @if(request()->filled('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="ফোন, নাম বা আইপি দিয়ে খুঁজুন..." value="{{ request('search') }}" style="min-width: 220px;">
                        <button type="submit" class="btn btn-dark"><i class="fas fa-search"></i></button>
                    </div>
                    @if(request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('admin.failed-orders.index') }}" class="btn btn-sm btn-outline-secondary" title="রিসেট"><i class="fas fa-redo"></i></a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Failed Orders Table Card -->
    <div class="card border-0 bg-white rounded-3 shadow-xs overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-3 py-3" style="width: 70px;">#ID</th>
                        <th class="py-3">গ্রাহকের বিবরণ</th>
                        <th class="py-3">পণ্যসমূহ ও কার্ট</th>
                        <th class="py-3">সম্ভাব্য বিল</th>
                        <th class="py-3">স্ট্যাটাস ও কারণ</th>
                        <th class="py-3">তারিখ ও সময়</th>
                        <th class="pe-3 py-3 text-end" style="width: 170px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($failedOrders as $order)
                    @php $badge = $order->status_badge; @endphp
                    <!-- Entire row is clickable to open details popup -->
                    <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#failedOrderModal{{ $order->id }}" class="failed-order-table-row">
                        <!-- ID -->
                        <td class="ps-3 fw-bold text-muted font-monospace">#{{ $order->id }}</td>

                        <!-- Customer Details & Quick Contact Buttons -->
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold text-dark fs-6">{{ $order->customer_name ?: 'নামহীন কাস্টমার' }}</span>
                                
                                @if(!empty($order->customer_phone))
                                <div class="d-flex align-items-center gap-2">
                                    <span class="font-monospace text-secondary fw-semibold">
                                        <i class="fas fa-phone-alt text-muted small me-1"></i>{{ $order->customer_phone }}
                                    </span>

                                    <!-- Quick Call Button -->
                                    <a href="tel:{{ $order->customer_phone }}" onclick="event.stopPropagation();" class="badge bg-success-subtle text-success border border-success text-decoration-none px-2 py-1" title="সরাসরি কল দিন">
                                        <i class="fas fa-phone small me-1"></i>কল
                                    </a>

                                    <!-- Quick WhatsApp Button -->
                                    @if($order->whatsapp_url)
                                    <a href="{{ $order->whatsapp_url }}" onclick="event.stopPropagation();" target="_blank" class="badge text-decoration-none px-2 py-1" style="background: rgba(37, 211, 102, 0.15); color: #128c7e; border: 1px solid #86efac;" title="WhatsApp-এ মেসেজ পাঠান">
                                        <i class="fab fa-whatsapp small me-1"></i>হোয়াটসঅ্যাপ
                                    </a>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted small">ফোন নাম্বার প্রদান করেনি</span>
                                @endif

                                @if(!empty($order->shipping_address))
                                <span class="text-muted text-truncate" style="max-width: 250px; font-size: 11px;" title="{{ $order->shipping_address }}">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $order->shipping_address }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Products preview -->
                        <td>
                            @if(!empty($order->cart_items) && is_array($order->cart_items))
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-light text-dark border align-self-start" style="font-size: 11px;">
                                    {{ count($order->cart_items) }} টি প্রোডাক্ট
                                </span>
                                <div class="d-flex align-items-center gap-1 flex-wrap" style="max-width: 220px;">
                                    @foreach(array_slice($order->cart_items, 0, 3) as $item)
                                        <span class="small text-truncate text-secondary" style="max-width: 200px;" title="{{ $item['name'] ?? '' }}">
                                            • {{ $item['name'] ?? 'Product' }} (×{{ $item['quantity'] ?? 1 }})
                                        </span>
                                    @endforeach
                                    @if(count($order->cart_items) > 3)
                                        <span class="text-muted" style="font-size: 10px;">+ আরও {{ count($order->cart_items) - 3 }} টি</span>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span class="text-muted small">কোনো পণ্য সংরক্ষিত নেই</span>
                            @endif
                        </td>

                        <!-- Bill amount -->
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">৳{{ number_format($order->total_amount, 2) }}</span>
                                <span class="text-muted" style="font-size: 11px;">
                                    (পণ্য: ৳{{ number_format($order->subtotal, 0) }} + ডেলিভারি: ৳{{ number_format($order->shipping_charge, 0) }})
                                </span>
                            </div>
                        </td>

                        <!-- Status badge & Failure Reason -->
                        <td>
                            <div class="d-flex flex-column gap-1 align-items-start">
                                <span class="badge px-2 py-1 d-inline-flex align-items-center gap-1" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 1px solid {{ $badge['border'] }}; font-size: 11px;">
                                    <i class="fas {{ $badge['icon'] }} small"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>

                                @if(!empty($order->failure_reason))
                                <span class="text-muted text-truncate" style="max-width: 220px; font-size: 11px;" title="{{ $order->failure_reason }}">
                                    <i class="fas fa-info-circle text-secondary me-1"></i>{{ $order->failure_reason }}
                                </span>
                                @endif

                                @if($order->is_recovered && $order->recovered_order_id)
                                <button type="button" class="badge bg-success text-white border-0 mt-1 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#failedOrderModal{{ $order->id }}" style="font-size: 10px; cursor: pointer;">
                                    <i class="fas fa-check me-1"></i>অর্ডার দেখুন (Recovered)
                                </button>
                                @endif
                            </div>
                        </td>

                        <!-- Timestamp & IP -->
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-semibold">{{ $order->created_at->format('d M, Y h:i A') }}</span>
                                <span class="text-muted" style="font-size: 11px;">{{ $order->created_at->diffForHumans() }}</span>
                                @if(!empty($order->ip_address))
                                <span class="font-monospace text-muted" style="font-size: 10px;" title="গ্রাহকের আইপি">
                                    <i class="fas fa-network-wired me-1"></i>{{ $order->ip_address }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Action Buttons -->
                        <td class="pe-3 text-end" onclick="event.stopPropagation();">
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Details Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-light border text-dark shadow-xs rounded-2" data-bs-toggle="modal" data-bs-target="#failedOrderModal{{ $order->id }}" title="বিস্তারিত দেখুন">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>

                                <!-- Convert to Confirmed Order button (if not already recovered and has phone) -->
                                @if(!$order->is_recovered && !empty($order->customer_phone))
                                <form method="POST" action="{{ route('admin.failed-orders.convert', $order->id) }}" class="d-inline" onsubmit="return confirm('আপনি কি এই ফেইল্ড রেকর্ডটিকে একটি লাইভ কনফার্মড অর্ডারে রূপান্তর করতে চান?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success text-white shadow-xs rounded-2" title="অর্ডারে রূপান্তর করুন">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- Delete button -->
                                <form method="POST" action="{{ route('admin.failed-orders.destroy', $order->id) }}" class="d-inline" onsubmit="return confirm('আপনি কি এই ফেইল্ড অর্ডারের রেকর্ডটি মুছে ফেলতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger shadow-xs rounded-2" title="মুছে ফেলুন">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted">
                            <i class="fas fa-check-circle text-success fs-2 mb-2 d-block"></i>
                            <span class="fw-semibold d-block">কোনো ফেইল্ড বা অসম্পূর্ণ অর্ডার পাওয়া যায়নি।</span>
                            <span class="small">কাস্টমার যখন চেকআউটে এসে নাম-ঠিকানা লিখবে কিন্তু অর্ডার প্লেস করবে না, তখন তা এখানে স্বয়ংক্রিয়ভাবে দৃশ্যমান হবে।</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($failedOrders->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $failedOrders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ==========================================
     FAILED ORDER DETAILS POPUP MODALS
     (Rendered outside table to prevent browser DOM spill)
=========================================== -->
@foreach($failedOrders as $order)
<div class="modal fade text-start" id="failedOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-3 border-0 shadow">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">Failed Checkout #{{ $order->id }}</span>
                    <h6 class="modal-title fw-bold text-dark mb-0">গ্রাহকের অসম্পূর্ণ অর্ডার বিবরণ</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                @if($order->is_recovered && $order->recoveredOrder)
                <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 border-0" style="background: rgba(16, 185, 129, 0.15); color: #065f46;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>
                            <div class="fw-bold">এই অসম্পূর্ণ রেকর্ডটি মূল অর্ডারে রূপান্তরিত হয়েছে!</div>
                            <div class="small">লাইভ অর্ডার নম্বর: <strong>#{{ $order->recoveredOrder->order_number }}</strong> (স্ট্যাটাস: {{ ucfirst($order->recoveredOrder->order_status) }})</div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row g-3">
                    <!-- Customer Info Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle text-primary me-1"></i> কাস্টমার তথ্য
                            </h6>
                            <div class="d-flex flex-column gap-2 small">
                                <div><strong>নাম:</strong> <span class="text-dark">{{ $order->customer_name ?: 'নামহীন' }}</span></div>
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
                                <div><strong>ঠিকানা:</strong> <span class="text-dark">{{ $order->shipping_address ?: 'দেওয়া হয়নি' }}</span></div>
                                <div>
                                    <strong>ডেলিভারি এরিয়া:</strong> 
                                    <span class="badge {{ $order->delivery_zone === 'outside_dhaka' ? 'bg-primary' : 'bg-info text-dark' }}">
                                        {{ $order->delivery_zone === 'outside_dhaka' ? 'ঢাকার বাহিরে' : 'ঢাকার ভিতরে' }}
                                    </span>
                                </div>
                                <div><strong>পেমেন্ট মেথড:</strong> <span class="badge bg-white text-dark border">{{ strtoupper($order->payment_method ?: 'COD') }}</span></div>
                                @if($order->customer_note)
                                <div><strong>গ্রাহকের নোট:</strong> <em>{{ $order->customer_note }}</em></div>
                                @endif
                                <div class="mt-2 pt-2 border-top">
                                    <strong>আইপি অ্যাড্রেস:</strong> <span class="font-monospace text-muted">{{ $order->ip_address ?: 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Call / WhatsApp Action Bar inside modal -->
                            @if(!empty($order->customer_phone))
                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                <a href="tel:{{ $order->customer_phone }}" class="btn btn-sm btn-success flex-grow-1 fw-bold">
                                    <i class="fas fa-phone-alt me-1"></i> সরাসরি কল করুন
                                </a>
                                @if($order->whatsapp_url)
                                <a href="{{ $order->whatsapp_url }}" target="_blank" class="btn btn-sm text-white flex-grow-1 fw-bold" style="background: #25D366;">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cart Items Box -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-shopping-cart text-danger me-1"></i> কার্ট আইটেমসমূহ
                            </h6>
                            @if(!empty($order->cart_items) && is_array($order->cart_items))
                            <div class="d-flex flex-column gap-2 mb-3" style="max-height: 180px; overflow-y: auto;">
                                @foreach($order->cart_items as $item)
                                @php
                                    $itemSlug = $item['slug'] ?? null;
                                    $itemUrl = $itemSlug ? route('product.detail', $itemSlug) : null;
                                    $itemImage = $item['image'] ?? null;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-2 border gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 0;">
                                        @if(!empty($itemImage))
                                            @if($itemUrl)
                                                <a href="{{ $itemUrl }}" target="_blank" title="লাইভ প্রোডাক্ট পেজ খুলুন" class="flex-shrink-0">
                                                    <img src="{{ $itemImage }}" alt="" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                                </a>
                                            @else
                                                <img src="{{ $itemImage }}" alt="" class="rounded border flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover;">
                                            @endif
                                        @endif
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            @if($itemUrl)
                                                <a href="{{ $itemUrl }}" target="_blank" class="fw-semibold text-dark text-decoration-none d-inline-flex align-items-center gap-1 hover-text-primary" style="font-size: 12px; line-height: 1.35;" title="লাইভ প্রোডাক্ট পেজ দেখুন">
                                                    <span>{{ $item['name'] ?? 'Product' }}</span>
                                                    <i class="fas fa-external-link-alt text-primary flex-shrink-0 ms-1" style="font-size: 10px;"></i>
                                                </a>
                                            @else
                                                <div class="fw-semibold text-dark" style="font-size: 12px; line-height: 1.35;">{{ $item['name'] ?? 'Product' }}</div>
                                            @endif
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                <span>৳{{ number_format($item['unit_price'] ?? 0, 0) }} × {{ $item['quantity'] ?? 1 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ps-2">
                                        <span class="fw-bold text-dark small d-block">৳{{ number_format($item['total_price'] ?? 0, 0) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="border-top pt-2 small">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>সাব-টোটাল:</span>
                                    <span>৳{{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span>৳{{ number_format($order->shipping_charge, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6 mt-1 border-top pt-1">
                                    <span>সর্বমোট:</span>
                                    <span class="text-danger">৳{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                            @else
                            <div class="text-muted small py-3 text-center">কোনো পণ্য তালিকাভুক্ত নেই।</div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Update & Admin Notes Form -->
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-clipboard-check text-secondary me-1"></i> ফলো-আপ নোট ও স্ট্যাটাস পরিবর্তন
                            </h6>
                            <form method="POST" action="{{ route('admin.failed-orders.update-status', $order->id) }}">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label small fw-semibold text-dark mb-1">স্ট্যাটাস:</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="abandoned" {{ $order->status === 'abandoned' ? 'selected' : '' }}>পরিত্যক্ত (Abandoned)</option>
                                            <option value="attempted" {{ $order->status === 'attempted' ? 'selected' : '' }}>প্লেস ব্যর্থ (Attempted)</option>
                                            <option value="contacted" {{ $order->status === 'contacted' ? 'selected' : '' }}>যোগাযোগ করা হয়েছে (Contacted)</option>
                                            <option value="recovered" {{ $order->status === 'recovered' ? 'selected' : '' }}>অর্ডারে রূপান্তর (Recovered)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">গ্রাহকের সাথে আলাপের নোট:</label>
                                        <input type="text" name="contact_notes" class="form-control form-control-sm" placeholder="যেমন: কাস্টমার বিকেলে কল দিতে বলেছেন..." value="{{ $order->contact_notes }}">
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
            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                @if(!$order->is_recovered && !empty($order->customer_phone))
                <form method="POST" action="{{ route('admin.failed-orders.convert', $order->id) }}" onsubmit="return confirm('এই রেকর্ডটিকে লাইভ কনফার্মড অর্ডারে রূপান্তর করবেন?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1">
                        <i class="fas fa-cart-plus"></i>
                        <span>সরাসরি মূল অর্ডারে রূপান্তর করুন</span>
                    </button>
                </form>
                @endif
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection
