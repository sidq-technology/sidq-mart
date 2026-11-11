@if(\App\Models\Setting::get('fomo_popup_enabled', '1') === '1' && !request()->routeIs('checkout', 'order.success', 'admin.*'))
@php
    $fomoFirstDelay = (float) \App\Models\Setting::get('fomo_popup_first_delay', 7);
    if ($fomoFirstDelay < 1) $fomoFirstDelay = 7;

    $fomoInterval = (float) \App\Models\Setting::get('fomo_popup_interval', 12);
    if ($fomoInterval < 3) $fomoInterval = 12;

    $fomoDuration = (float) \App\Models\Setting::get('fomo_popup_duration', 5);
    if ($fomoDuration < 2) $fomoDuration = 5;

    $fomoProducts = [];
    if (isset($product) && $product instanceof \App\Models\Product) {
        $fomoProducts = [
            [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->primary_image_url,
                'url' => route('product.detail', $product->slug)
            ]
        ];
    } else {
        $fomoProducts = \Illuminate\Support\Facades\Cache::remember('fomo_featured_products', 1800, function() {
            $prods = \App\Models\Product::where('is_active', true)
                ->where('is_featured', true)
                ->latest('id')
                ->take(8)
                ->get();
            if ($prods->isEmpty()) {
                $prods = \App\Models\Product::where('is_active', true)->latest('id')->take(6)->get();
            }
            return $prods->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'image' => $p->primary_image_url,
                'url' => route('product.detail', $p->slug)
            ])->toArray();
        });
    }
@endphp

@if(!empty($fomoProducts))
<!-- ==========================================
     SIDQ Mart — Live Social Proof FOMO Popup (Center Screen, Left-in / Left-out)
=========================================== -->
<div id="sidqFomoToast" class="sidq-fomo-toast" style="display:none;" aria-live="polite">
    <button type="button" class="fomo-close-btn" onclick="dismissSidqFomo(event)" title="বন্ধ করুন" aria-label="Close">
        &times;
    </button>
    <a id="fomoProductLink" href="#" class="fomo-inner-link">
        <div class="fomo-img-wrap">
            <img id="fomoProductImg" src="" alt="Product" loading="lazy">
        </div>
        <div class="fomo-text-wrap">
            <div class="fomo-buyer-line">
                <span class="fomo-buyer-name" id="fomoBuyerName"></span>
                <span class="fomo-verified-badge" title="ভেরিফাইড ক্রেতা"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="fomo-action-text">এইমাত্র অর্ডার করেছেন</div>
            <div class="fomo-product-title text-truncate" id="fomoProductTitle"></div>
            <div class="fomo-meta-line">
                <span class="fomo-pulse-dot"></span>
                <span id="fomoLocation"></span> &bull; <span id="fomoTime"></span>
            </div>
        </div>
    </a>
</div>

<style>
/* SIDQ FOMO Recent Sales Notification Styles — Center of Screen */
.sidq-fomo-toast {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1045;
    width: 330px;
    max-width: calc(100vw - 32px);
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.95);
    border-radius: 16px;
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.04);
    padding: 12px 14px;
    font-family: inherit;
    user-select: none;
    will-change: transform, opacity;
}

/* Enter from Left into Screen Center */
.sidq-fomo-toast.fomo-entering {
    animation: fomoSlideInCenter 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Exit to Left from Screen Center */
.sidq-fomo-toast.fomo-leaving {
    animation: fomoSlideOutCenter 0.42s cubic-bezier(0.7, 0, 0.84, 0) forwards;
}

@keyframes fomoSlideInCenter {
    0% {
        transform: translate(calc(-50% - 100vw), -50%);
        opacity: 0;
    }
    100% {
        transform: translate(-50%, -50%);
        opacity: 1;
    }
}

@keyframes fomoSlideOutCenter {
    0% {
        transform: translate(-50%, -50%);
        opacity: 1;
    }
    100% {
        transform: translate(calc(-50% - 100vw), -50%);
        opacity: 0;
    }
}

.sidq-fomo-toast .fomo-close-btn {
    position: absolute;
    top: 4px;
    right: 8px;
    background: transparent;
    border: none;
    font-size: 18px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px 4px;
    z-index: 2;
    transition: color 0.15s ease;
}

.sidq-fomo-toast .fomo-close-btn:hover {
    color: #dc2626;
}

.sidq-fomo-toast .fomo-inner-link {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: inherit;
}

.sidq-fomo-toast .fomo-img-wrap {
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    border-radius: 9px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
}

.sidq-fomo-toast .fomo-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.sidq-fomo-toast .fomo-text-wrap {
    flex: 1;
    min-width: 0;
    line-height: 1.25;
}

.sidq-fomo-toast .fomo-buyer-line {
    display: flex;
    align-items: center;
    gap: 4px;
}

.sidq-fomo-toast .fomo-buyer-name {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
}

.sidq-fomo-toast .fomo-verified-badge {
    color: #10b981;
    font-size: 11px;
}

.sidq-fomo-toast .fomo-action-text {
    font-size: 11px;
    color: #64748b;
    margin-top: 1px;
}

.sidq-fomo-toast .fomo-product-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    max-width: 220px;
    margin-top: 2px;
}

.sidq-fomo-toast .fomo-meta-line {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 3px;
}

.sidq-fomo-toast .fomo-pulse-dot {
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 rgba(16, 185, 129, 0.6);
    animation: fomoPulseDot 2s infinite;
}

@keyframes fomoPulseDot {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Mobile Responsive Optimization — Stays Centered */
@media (max-width: 768px) {
    .sidq-fomo-toast {
        top: 50%;
        left: 50%;
        width: calc(100vw - 32px);
        max-width: 320px;
        padding: 10px 12px;
    }
}
</style>

<script>
(function() {
    // If dismissed in this session, do not show again
    if (sessionStorage.getItem('sidq_fomo_dismissed') === '1') {
        return;
    }

    const fomoProducts = @json($fomoProducts);
    if (!fomoProducts || !fomoProducts.length) return;

    const firstDelayMs = {{ (int)($fomoFirstDelay * 1000) }};
    const intervalMs = {{ (int)($fomoInterval * 1000) }};
    const durationMs = {{ (int)($fomoDuration * 1000) }};

    const buyerNames = [
        'তানিয়া আক্তার', 'সাদিয়া সুলতানা', 'ফারহানা ইসলাম', 'নুসরাত জাহান', 
        'মোঃ রফিকুল ইসলাম', 'সাবরিনা আক্তার', 'আশরাফুল আলম', 'নাজমুল হাসান', 
        'জান্নাতুল ফেরদৌস', 'মোঃ শাহাদাত হোসেন', 'ইমরান খান', 'সুমাইয়া আক্তার', 
        'মেহজাবিন চৌধুরী', 'তানভীর আহমেদ', 'আরিফুল হক', 'তাসলিমা বেগম', 
        'মোঃ জাহিদুল ইসলাম', 'মাকসুদা আক্তার', 'রাশেদুল ইসলাম', 'রুবাইয়া ইয়াসমিন'
    ];

    const locations = [
        'উত্তরা, ঢাকা', 'মিরপুর, ঢাকা', 'ধানমন্ডি, ঢাকা', 'বনশ্রী, ঢাকা', 
        'মোহাম্মদপুর, ঢাকা', 'চট্টগ্রাম সদর', 'সিলেট সদর', 'রাজশাহী সদর', 
        'খুলনা', 'কুমিল্লা', 'বগুড়া', 'গাজীপুর সদর', 'নারায়ণগঞ্জ', 
        'রংপুর', 'ময়মনসিংহ', 'বরিশাল', 'কক্সবাজার', 'ফেনী'
    ];

    const times = [
        'এইমাত্র', '১ মিনিট আগে', '২ মিনিট আগে', '৩ মিনিট আগে', 
        '৪ মিনিট আগে', '৫ মিনিট আগে', '৬ মিনিট আগে', '৭ মিনিট আগে'
    ];

    const toastEl = document.getElementById('sidqFomoToast');
    const buyerNameEl = document.getElementById('fomoBuyerName');
    const productTitleEl = document.getElementById('fomoProductTitle');
    const productImgEl = document.getElementById('fomoProductImg');
    const productLinkEl = document.getElementById('fomoProductLink');
    const locationEl = document.getElementById('fomoLocation');
    const timeEl = document.getElementById('fomoTime');

    let fomoTimeout = null;
    let isFomoVisible = false;

    function getRandomItem(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    }

    function showRandomFomo() {
        if (sessionStorage.getItem('sidq_fomo_dismissed') === '1') return;
        if (isFomoVisible) return;

        const prod = getRandomItem(fomoProducts);
        const buyer = getRandomItem(buyerNames);
        const loc = getRandomItem(locations);
        const tm = getRandomItem(times);

        buyerNameEl.textContent = buyer;
        productTitleEl.textContent = prod.name;
        productImgEl.src = prod.image;
        productImgEl.alt = prod.name;
        productLinkEl.href = prod.url;
        locationEl.textContent = loc;
        timeEl.textContent = tm;

        toastEl.style.display = 'block';
        toastEl.classList.remove('fomo-leaving');
        toastEl.classList.add('fomo-entering');
        isFomoVisible = true;

        // Auto hide after configured duration
        fomoTimeout = setTimeout(function() {
            hideFomo();
        }, durationMs);
    }

    function hideFomo() {
        if (!isFomoVisible) return;
        toastEl.classList.remove('fomo-entering');
        toastEl.classList.add('fomo-leaving');

        setTimeout(function() {
            toastEl.style.display = 'none';
            toastEl.classList.remove('fomo-leaving');
            isFomoVisible = false;

            // Schedule next popup after configured interval
            if (sessionStorage.getItem('sidq_fomo_dismissed') !== '1') {
                setTimeout(showRandomFomo, intervalMs);
            }
        }, 420);
    }

    window.dismissSidqFomo = function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (fomoTimeout) clearTimeout(fomoTimeout);
        sessionStorage.setItem('sidq_fomo_dismissed', '1');
        hideFomo();
    };

    // First trigger after configured first delay (default 7s)
    setTimeout(showRandomFomo, firstDelayMs);
})();
</script>
@endif
@endif
