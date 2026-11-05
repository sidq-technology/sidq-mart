@extends('layouts.admin')

@section('title', 'অর্ডার বিস্তারিত: ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> অর্ডারের তালিকায় ফিরুন
        </a>
        <h3 class="fw-bold mb-0 text-dark">অর্ডার: {{ $order->order_number }}</h3>
        <span class="text-muted small">তারিখ: {{ $order->created_at->format('d F Y, h:i A') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" 
                class="btn btn-success fw-bold d-inline-flex align-items-center gap-1 shadow-sm btn-send-to-sheet"
                style="background-color: #0f9d58; border-color: #0f9d58; color: #fff;"
                data-url="{{ route('admin.orders.send-to-sheet', $order->id) }}"
                data-order-number="{{ $order->order_number }}">
            <i class="fas fa-file-excel"></i>
            <span>Send to Sheet</span>
        </button>

        @php
            $couriers = $enabledCouriers ?? app(\App\Services\Courier\CourierManager::class)->getEnabledCouriers();
        @endphp

        <div class="courier-action-slot d-inline-block">
            @if(!empty($order->courier_name) && !empty($order->courier_tracking_code))
                <div class="d-inline-flex align-items-center gap-1 p-2 rounded-2 bg-success-subtle border border-success-subtle text-success small fw-bold">
                    <i class="fas fa-truck-fast"></i>
                    <span>{{ ucfirst($order->courier_name) }}:</span>
                    @if($order->courier_tracking_url)
                        <a href="{{ $order->courier_tracking_url }}" target="_blank" class="text-success text-decoration-underline">{{ $order->courier_tracking_code }}</a>
                    @else
                        <span>{{ $order->courier_tracking_code }}</span>
                    @endif
                </div>
            @elseif(!empty($couriers))
                <div class="dropdown d-inline-block courier-dropdown-wrapper">
                    <button class="btn btn-primary dropdown-toggle fw-bold d-inline-flex align-items-center gap-1 shadow-sm btn-courier-dropdown" 
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
                <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" title="কুরিয়ার কনফিগার করুন">
                    <i class="fas fa-truck-fast text-muted"></i>
                    <span class="small text-muted">Configure Courier</span>
                </a>
            @endif
        </div>

        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-dark">
            <i class="fas fa-print me-1"></i> ইনভয়েস প্রিন্ট করুন
        </a>
    </div>
</div>

<div id="showOrderFeedback" class="alert d-none mb-4"></div>

<div class="row g-4">
    <!-- Left: Order Items & Pricing Breakdown -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">অর্ডারকৃত পণ্যসমূহ (Order Items)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>পণ্য</th>
                                <th>একক মূল্য</th>
                                <th>পরিমাণ</th>
                                <th class="text-end">মোট মূল্য</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="rounded me-2" style="width: 48px; height: 48px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $item->product_name }}</div>
                                            @if(!empty($item->variant_text))
                                            <div class="mt-1">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1 small">
                                                    <i class="fas fa-tag me-1"></i> {{ $item->variant_text }}
                                                </span>
                                            </div>
                                            @endif
                                            @if($item->product)
                                            <small class="text-muted d-block mt-1">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>৳{{ number_format($item->unit_price, 0) }}</td>
                                <td>{{ $item->quantity }} টি</td>
                                <td class="text-end fw-bold">৳{{ number_format($item->total_price, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="3" class="text-end text-muted">পণ্যের মূল্য (Subtotal):</th>
                                <th class="text-end">৳{{ number_format($order->subtotal, 0) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end text-muted">ডেলিভারি চার্জ ({{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}):</th>
                                <th class="text-end">৳{{ number_format($order->shipping_charge, 0) }}</th>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <th colspan="3" class="text-end text-success">কুপন ডিসকাউন্ট:</th>
                                <th class="text-end text-success">-৳{{ number_format($order->discount_amount, 0) }}</th>
                            </tr>
                            @endif
                            <tr class="fs-5">
                                <th colspan="3" class="text-end text-danger">সর্বমোট বিল (Grand Total):</th>
                                <th class="text-end text-danger">৳{{ number_format($order->grand_total, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($order->customer_note)
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-light">
            <h6 class="fw-bold mb-1 text-dark"><i class="fas fa-comment-dots text-danger me-1"></i> গ্রাহকের নোট:</h6>
            <p class="mb-0 text-muted">{{ $order->customer_note }}</p>
        </div>
        @endif
    </div>

    <!-- Right: Customer Info & Status Manager -->
    <div class="col-12 col-lg-4">
        <!-- Status Update Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">স্ট্যাটাস পরিবর্তন (Update Status)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" id="showOrderStatusForm">
                    @csrf
                    <div class="mb-3">
                        <label for="order_status" class="form-label small fw-bold">অর্ডার স্ট্যাটাস</label>
                        <select name="order_status" id="order_status" class="form-select">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending (পেন্ডিং)</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing (প্রসেসিং)</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped / In Courier (কুরিয়ারে আছে)</option>
                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered (ডেলিভার্ড)</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled (বাতিল)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="admin_notes" class="form-label small fw-bold">অ্যাডমিন নোট (Internal Notes)</label>
                        <textarea name="admin_notes" id="admin_notes" rows="2" class="form-control" placeholder="কুরিয়ার ট্র্যাকিং কোড বা ডেলিভারি তথ্য...">{{ $order->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2" id="btnUpdateShowStatus">
                        <i class="fas fa-save me-1"></i> স্ট্যাটাস আপডেট করুন
                    </button>
                    <div id="showStatusFeedback" class="mt-2 alert d-none py-1 px-2 small"></div>
                </form>
            </div>
        </div>

        <!-- Customer & Shipping Address Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">গ্রাহকের তথ্য</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small">গ্রাহকের নাম:</span>
                    <div class="fw-bold fs-6">{{ $order->customer_name }}</div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">মোবাইল নম্বর:</span>
                    <div class="fw-bold fs-6">
                        <a href="tel:{{ $order->customer_phone }}" class="text-danger text-decoration-none">
                            <i class="fas fa-phone-alt me-1"></i> {{ $order->customer_phone }}
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">ডেলিভারির সম্পূর্ণ ঠিকানা:</span>
                    <div class="p-2 bg-light rounded border small fw-medium mt-1">
                        {{ $order->shipping_address }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small">পেমেন্ট মেথড:</span>
                    <div class="fw-bold text-uppercase">{{ $order->payment_method }}</div>
                    @if($order->transaction_id)
                    <div class="small text-danger fw-bold">TrxID: {{ $order->transaction_id }}</div>
                    @endif
                </div>

                @if($order->ip_address)
                <div class="pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <div>
                        <span class="text-muted small">আইপি অ্যাড্রেস:</span>
                        <div class="font-monospace text-muted small fw-bold">{{ $order->ip_address }}</div>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.querySelector('.btn-send-to-sheet');
    const feedbackBox = document.getElementById('showOrderFeedback');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const url = this.getAttribute('data-url');
        const originalContent = this.innerHTML;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> পাঠাচ্ছে...';

        if (feedbackBox) {
            feedbackBox.className = 'alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-4';
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
                    feedbackBox.className = 'alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-4';
                    feedbackBox.innerHTML = '<i class="fas fa-check-circle fs-6"></i> <div>' + body.message + '</div>';
                }
            } else {
                this.innerHTML = originalContent;
                if (feedbackBox) {
                    feedbackBox.className = 'alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-4';
                    feedbackBox.innerHTML = '<i class="fas fa-exclamation-triangle fs-6"></i> <div>' + (body.message || 'ত্রুটি ঘটেছে!') + '</div>';
                }
            }
        })
        .catch(err => {
            this.disabled = false;
            this.innerHTML = originalContent;
            if (feedbackBox) {
                feedbackBox.className = 'alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-4';
                feedbackBox.innerHTML = '<i class="fas fa-times-circle fs-6"></i> <div>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</div>';
            }
        });
    });

    // AJAX Status update in show page
    const statusForm = document.getElementById('showOrderStatusForm');
    const btnUpdate = document.getElementById('btnUpdateShowStatus');
    const statusFeedback = document.getElementById('showStatusFeedback');

    if (statusForm && btnUpdate) {
        statusForm.addEventListener('submit', function (e) {
            e.preventDefault();
            btnUpdate.disabled = true;
            const originalHtml = btnUpdate.innerHTML;
            btnUpdate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> সংরক্ষণ হচ্ছে...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new FormData(this)
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(({ ok, body }) => {
                btnUpdate.disabled = false;
                if (ok && body.success) {
                    btnUpdate.innerHTML = '<i class="fas fa-check me-1"></i> আপডেট সম্পন্ন!';
                    btnUpdate.classList.remove('btn-danger');
                    btnUpdate.classList.add('btn-success');
                    setTimeout(() => {
                        btnUpdate.innerHTML = originalHtml;
                        btnUpdate.classList.remove('btn-success');
                        btnUpdate.classList.add('btn-danger');
                    }, 2500);

                    if (statusFeedback) {
                        statusFeedback.className = 'mt-2 alert alert-success py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                        statusFeedback.innerHTML = '<i class="fas fa-check-circle text-success"></i> <span>' + body.message + '</span>';
                        statusFeedback.classList.remove('d-none');
                        setTimeout(() => statusFeedback.classList.add('d-none'), 3500);
                    }
                } else {
                    btnUpdate.innerHTML = originalHtml;
                    if (statusFeedback) {
                        statusFeedback.className = 'mt-2 alert alert-danger py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                        statusFeedback.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> <span>' + (body.message || 'আপডেট করতে সমস্যা হয়েছে') + '</span>';
                        statusFeedback.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                btnUpdate.disabled = false;
                btnUpdate.innerHTML = originalHtml;
                if (statusFeedback) {
                    statusFeedback.className = 'mt-2 alert alert-danger py-1 px-2 small d-flex align-items-center gap-1 mb-0';
                    statusFeedback.innerHTML = '<i class="fas fa-times-circle text-danger"></i> <span>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</span>';
                    statusFeedback.classList.remove('d-none');
                }
            });
        });
    }

    // Handle Courier Booking in show page
    document.querySelectorAll('.btn-submit-to-courier').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const courier = this.getAttribute('data-courier');
            const courierLabel = this.getAttribute('data-label');
            const slot = document.querySelector('.courier-action-slot');
            const dropdownBtn = document.querySelector('.btn-courier-dropdown');
            const feedbackBox = document.getElementById('showOrderFeedback');

            if (dropdownBtn) {
                dropdownBtn.disabled = true;
                dropdownBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> বুকিং হচ্ছে...';
            }

            if (feedbackBox) {
                feedbackBox.className = 'alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-4';
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
                            <div class="d-inline-flex align-items-center gap-1 p-2 rounded-2 bg-success-subtle border border-success-subtle text-success small fw-bold">
                                <i class="fas fa-truck-fast"></i>
                                <span>${body.courier_name.toUpperCase()}:</span>
                                ${trkLink}
                            </div>
                        `;
                    }

                    if (feedbackBox) {
                        feedbackBox.className = 'alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-4';
                        feedbackBox.innerHTML = '<i class="fas fa-check-circle fs-6"></i> <div>' + body.message + '</div>';
                    }
                } else {
                    if (dropdownBtn) {
                        dropdownBtn.disabled = false;
                        dropdownBtn.innerHTML = '<i class="fas fa-truck-fast"></i> <span>Send to Courier</span>';
                    }
                    if (feedbackBox) {
                        feedbackBox.className = 'alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-4';
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
                    feedbackBox.className = 'alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-4';
                    feedbackBox.innerHTML = '<i class="fas fa-times-circle fs-6"></i> <div>রিকোয়েস্ট ব্যর্থ হয়েছে: ' + err.message + '</div>';
                }
            });
        });
    });
});
</script>
@endpush
