@if($products->count() > 0)
    <ul class="list-group list-group-flush">
        @foreach($products->take(6) as $item)
        <li class="list-group-item list-group-item-action py-2">
            <a href="{{ route('product.detail', $item->slug) }}" class="d-flex align-items-center text-decoration-none text-dark">
                <img src="{{ $item->primary_image_url }}" alt="{{ $item->name }}" class="rounded me-2" style="width: 44px; height: 44px; object-fit: cover;">
                <div class="flex-grow-1 text-truncate">
                    <div class="text-truncate fw-medium" style="font-size: 13px;">{{ $item->name }}</div>
                    <div class="text-danger fw-bold" style="font-size: 12px;">Tk {{ number_format($item->current_price, 0) }}</div>
                </div>
            </a>
        </li>
        @endforeach
        <li class="list-group-item text-center bg-light py-2">
            <a href="{{ route('search', ['q' => $query]) }}" class="text-primary text-decoration-none fw-bold" style="font-size: 13px;">
                সবগুলো ({{ $products->total() }}) ফলাফল দেখুন &rarr;
            </a>
        </li>
    </ul>
@else
    <div class="p-3 text-center text-muted" style="font-size: 13px;">
        "{{ $query }}" সম্পর্কিত কোনো পণ্য পাওয়া যায়নি।
    </div>
@endif
