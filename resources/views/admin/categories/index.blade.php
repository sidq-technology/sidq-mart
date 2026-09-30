@extends('layouts.admin')

@section('title', 'ক্যাটেগরি - Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">ক্যাটেগরি ব্যবস্থাপনা (Categories)</h3>
        <p class="text-muted small mb-0">পণ্যের মূল ক্যাটেগরি ও সাব-ক্যাটেগরি পরিচালনা করুন।</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-danger">
        <i class="fas fa-plus-circle me-1"></i> নতুন ক্যাটেগরি যুক্ত করুন
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">আইকন/ছবি</th>
                        <th>ক্যাটেগরির নাম</th>
                        <th>স্লাগ (Slug)</th>
                        <th>প্যারেন্ট ক্যাটেগরি</th>
                        <th>টপ ক্যাটেগরি</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                        </td>
                        <td class="fw-bold text-dark">
                            <a href="{{ route('product.category', $category->slug) }}" target="_blank" class="text-dark text-decoration-none">
                                {{ $category->name }}
                            </a>
                        </td>
                        <td class="small text-muted">{{ $category->slug }}</td>
                        <td>{{ $category->parent->name ?? 'None (Root)' }}</td>
                        <td>
                            @if($category->is_top)
                            <span class="badge bg-warning text-dark">টপ ক্যাটেগরি</span>
                            @else
                            <span class="badge bg-light text-muted border">সাধারণ</span>
                            @endif
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge bg-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('এই ক্যাটেগরি মুছে ফেলতে চান?')">
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
                        <td colspan="7" class="text-center py-5 text-muted">কোনো ক্যাটেগরি পাওয়া যায়নি।</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
