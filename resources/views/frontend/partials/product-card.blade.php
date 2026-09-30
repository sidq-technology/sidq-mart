<div class="product-card sidq-product-card shadow-sm">
    <div class="product-media">
        <a href="{{ route('product.detail', $product->slug) }}">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" loading="lazy">
        </a>
        @if($product->discount_percent > 0)
        <div class="badge-discount">-{{ $product->discount_percent }} %</div>
        @endif

        <!-- Floating Quick Add-To-Cart Action -->
        <button type="button" class="btn-quick-cart" onclick="addToCart({{ $product->id }}, 1)" title="কার্টে যোগ করুন" aria-label="Add to cart">
            <i class="fas fa-shopping-basket"></i>
        </button>
    </div>
    
    <div class="product-content">
        <h6 class="product-name">
            <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
        </h6>

        <div class="product-price">
            <span class="new-price tabular-nums"><b>Tk {{ number_format($product->current_price, 0) }}</b></span>
            @if($product->sale_price && $product->regular_price > $product->sale_price)
            <del class="old-price tabular-nums">Tk {{ number_format($product->regular_price, 0) }}</del>
            @endif
        </div>

        <div class="mt-auto">
            <!-- Buy Now / Order Now Button (Direct to checkout) -->
            <form action="{{ route('buy.now', $product->slug) }}" method="POST" class="d-grid w-100">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-order-now w-100">
                    <i class="fas fa-shopping-cart"></i> অর্ডার করুন
                </button>
            </form>
        </div>
    </div>
</div>
