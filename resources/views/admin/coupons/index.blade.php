@extends('layouts.admin')

@section('title', 'কুপন কোড - Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">কুপন ও ডিসকাউন্ট (Discount Coupons)</h3>
        <p class="text-muted small mb-0">গ্রাহকদের জন্য প্রচারমূলক ডিসকাউন্ট কোড তৈরি ও নিয়ন্ত্রণ করুন।</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-danger">
        <i class="fas fa-plus-circle me-1"></i> নতুন কুপন যুক্ত করুন
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>কুপন কোড</th>
                        <th>ধরন</th>
                        <th>ছাড়ের পরিমাণ</th>
                        <th>ন্যূনতম খরচ</th>
                        <th>মেয়াদ উত্তীর্ণের তারিখ</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td class="fw-bold text-danger">{{ $coupon->code }}</td>
                        <td>{{ $coupon->type === 'percent' ? 'শতাংশ (%)' : 'নির্দিষ্ট টাকা (৳)' }}</td>
                        <td class="fw-bold">{{ $coupon->type === 'percent' ? $coupon->value . '%' : '৳' . number_format($coupon->value, 0) }}</td>
                        <td>৳{{ number_format($coupon->min_spend, 0) }}</td>
                        <td class="small text-muted">{{ $coupon->expiry_date ? $coupon->expiry_date->format('d M Y') : 'মেয়াদহীন' }}</td>
                        <td>
                            @if($coupon->is_active)
                            <span class="badge bg-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('এই কুপন মুছে ফেলতে চান?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">কোনো কুপন তৈরি করা হয়নি।</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
