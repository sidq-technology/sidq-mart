@extends('layouts.admin')

@section('title', 'ব্যানার স্লাইডার - Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">ব্যানার স্লাইডার (Hero Sliders)</h3>
        <p class="text-muted small mb-0">হোমপেজের মূল প্রমোশনাল ব্যানার পরিচালনা করুন।</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-danger">
        <i class="fas fa-plus-circle me-1"></i> নতুন ব্যানার যুক্ত করুন
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;">ব্যানার ছবি</th>
                        <th>শিরোনাম</th>
                        <th>টার্গেট লিঙ্ক</th>
                        <th>ডিসপ্লে ক্রম</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ $banner->image_url }}" alt="Banner" class="rounded border" style="width: 120px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $banner->title ?: 'N/A' }}</div>
                            <small class="text-muted">{{ $banner->subtitle }}</small>
                        </td>
                        <td class="small text-muted">{{ $banner->target_url ?: '#' }}</td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            @if($banner->is_active)
                            <span class="badge bg-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('এই ব্যানারটি মুছে ফেলতে চান?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">কোনো ব্যানার স্লাইডার পাওয়া যায়নি।</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
