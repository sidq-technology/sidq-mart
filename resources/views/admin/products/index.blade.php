@extends('layouts.admin')

@section('title', 'পণ্যসমূহ - Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">পণ্য ব্যবস্থাপনা (Product Catalog)</h3>
        <p class="text-muted small mb-0">পণ্য সংযোজন, মূল্য পরিবর্তন, স্টক ও ফ্ল্যাশ সেল পরিচালনা করুন।</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-danger">
        <i class="fas fa-plus-circle me-1"></i> নতুন পণ্য যুক্ত করুন
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="পণ্যের নাম বা SKU..." value="{{ $search }}">
            </div>
            <div class="col-12 col-md-4">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">-- সকল ক্যাটেগরি --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark flex-fill"><i class="fas fa-filter me-1"></i> ফিল্টার</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">রিসেট</a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ছবি</th>
                        <th>পণ্যের নাম</th>
                        <th>SKU</th>
                        <th>ক্যাটেগরি</th>
                        <th>নিয়মিত মূল্য</th>
                        <th>অফার মূল্য</th>
                        <th>স্টক</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-medium text-truncate" style="max-width: 250px;">
                                <a href="{{ route('product.detail', $product->slug) }}" target="_blank" class="text-dark text-decoration-none">
                                    {{ $product->name }}
                                </a>
                            </div>
                            <div class="small">
                                @if($product->is_flash_sale)
                                <span class="badge bg-warning text-dark me-1">ফ্ল্যাশ সেল</span>
                                @endif
                                @if($product->is_featured)
                                <span class="badge bg-info text-dark">ফিচার্ড</span>
                                @endif
                            </div>
                        </td>
                        <td class="small text-muted">{{ $product->sku }}</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="text-muted">৳{{ number_format($product->regular_price, 0) }}</td>
                        <td class="fw-bold text-danger">
                            @if($product->sale_price)
                            ৳{{ number_format($product->sale_price, 0) }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($product->stock_quantity <= 5)
                            <span class="badge bg-danger">স্টক: {{ $product->stock_quantity }}</span>
                            @else
                            <span class="badge bg-light text-dark border">স্টক: {{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_active)
                            <span class="badge bg-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই পণ্যটি মুছে ফেলতে চান?')">
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
                        <td colspan="9" class="text-center py-5 text-muted">কোনো পণ্য পাওয়া যায়নি।</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
