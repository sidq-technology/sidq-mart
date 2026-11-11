@php
    $exitIntentEnabled = \App\Models\Setting::get('exit_intent_enabled', '0') === '1';
    $scope = \App\Models\Setting::get('exit_intent_scope', 'pdp_only');
    $isPdp = request()->routeIs('product.detail');
    $isRestricted = request()->routeIs('checkout', 'order.success', 'admin.*', 'login', 'register');

    if (!$exitIntentEnabled || $isRestricted) {
        return;
    }

    if ($scope === 'pdp_only' && !$isPdp) {
        return;
    }

    $heading = \App\Models\Setting::get('exit_intent_heading', 'এক মিনিট! আপনার জন্য একটি বিশেষ উপহার 🎁');
    $subtitle = \App\Models\Setting::get('exit_intent_subtitle', 'এখনই অর্ডার সম্পূর্ণ করলে পাচ্ছেন স্পেশাল ডিসকাউন্ট! সীমিত সময়ের এই সুযোগ হাতছাড়া করবেন না।');
    $btnText = \App\Models\Setting::get('exit_intent_btn_text', 'ডিসকাউন্ট নিয়ে অর্ডার করুন ⚡');
    $dismissText = \App\Models\Setting::get('exit_intent_dismiss_text', 'না, আমি ডিসকাউন্ট ছাড়াই কিনতে চাই');
    $couponCode = strtoupper(trim((string) \App\Models\Setting::get('exit_intent_coupon_code', '')));

    $triggerBack = \App\Models\Setting::get('exit_intent_trigger_back_button', '1') === '1';
    $triggerScroll = \App\Models\Setting::get('exit_intent_trigger_scroll_up', '1') === '1';
    $triggerIdle = \App\Models\Setting::get('exit_intent_trigger_idle', '1') === '1';
    $idleSeconds = (int) \App\Models\Setting::get('exit_intent_idle_seconds', 25);
    if ($idleSeconds < 5) $idleSeconds = 25;
    $triggerDesktop = \App\Models\Setting::get('exit_intent_trigger_desktop', '1') === '1';

    $couponObj = null;
    $discountBadge = null;
    if (!empty($couponCode)) {
        $couponObj = \App\Models\Coupon::where('code', $couponCode)->where('is_active', true)->first();
        if ($couponObj) {
            $discountBadge = $couponObj->type === 'percent'
                ? ((int) $couponObj->value . '% ছাড়')
                : ('৳' . number_format($couponObj->value, 0) . ' ছাড়');
        }
    }

    $currentProduct = (isset($product) && $product instanceof \App\Models\Product) ? $product : null;
@endphp

<!-- ========================================================
     CUSTOMER EXIT-INTENT & BOUNCE RECOVERY MODAL
======================================================== -->
<div id="sidqExitIntentBackdrop" class="sidq-exit-modal-backdrop" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="sidq-exit-modal-card">
        <!-- Close Button -->
        <button type="button" class="sidq-exit-close-btn" id="sidqExitCloseBtn" aria-label="Close modal">&times;</button>

        <!-- Animated Festive Gift Icon Header -->
        <div class="sidq-exit-icon-wrap">
            <div class="sidq-exit-icon-glow"></div>
            <div class="sidq-exit-icon-badge">
                <span class="sidq-gift-emoji">🎁</span>
            </div>
            @if($discountBadge)
            <div class="sidq-discount-floating-pill">
                <i class="fas fa-bolt text-warning me-1"></i> {{ $discountBadge }}
            </div>
            @endif
        </div>

        <!-- Heading & Subtitle -->
        <h3 class="sidq-exit-heading">{!! e($heading) !!}</h3>
        <p class="sidq-exit-subtitle">{!! e($subtitle) !!}</p>

        @if($currentProduct)
        <!-- Compact PDP Product Preview -->
        <div class="sidq-exit-product-strip">
            <img src="{{ $currentProduct->primary_image_url }}" alt="{{ $currentProduct->name }}" class="sidq-exit-product-img" loading="lazy">
            <div class="sidq-exit-product-info">
                <div class="sidq-exit-product-title">{{ $currentProduct->name }}</div>
                <div class="sidq-exit-product-price">
                    <span class="sidq-price-current">৳{{ number_format($currentProduct->final_price, 0) }}</span>
                    @if($currentProduct->regular_price && $currentProduct->regular_price > $currentProduct->final_price)
                    <span class="sidq-price-regular">৳{{ number_format($currentProduct->regular_price, 0) }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Action CTA Button -->
        <button type="button" class="sidq-exit-claim-btn" id="sidqExitClaimBtn">
            <span class="sidq-claim-text">
                <i class="fas fa-shopping-bag me-1"></i> {!! e($btnText) !!}
            </span>
            <span class="sidq-claim-spinner" style="display: none;">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                অর্ডার পেজে নিয়ে যাওয়া হচ্ছে...
            </span>
        </button>

        <!-- Dismiss Link -->
        <div class="sidq-exit-dismiss-wrap">
            <button type="button" class="sidq-exit-dismiss-link" id="sidqExitDismissBtn">
                {!! e($dismissText) !!}
            </button>
        </div>
    </div>
</div>

<style>
/* Exit-Intent Modal Styles */
.sidq-exit-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.72);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 9999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s ease;
}
.sidq-exit-modal-backdrop.is-visible {
    opacity: 1;
    visibility: visible;
}
.sidq-exit-modal-card {
    background: #ffffff;
    border-radius: 24px;
    width: 100%;
    max-width: 440px;
    padding: 28px 24px 22px 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.05);
    position: relative;
    text-align: center;
    transform: scale(0.92) translateY(15px);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}
.sidq-exit-modal-backdrop.is-visible .sidq-exit-modal-card {
    transform: scale(1) translateY(0);
}
.sidq-exit-close-btn {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: #f1f5f9;
    color: #64748b;
    font-size: 22px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 2;
}
.sidq-exit-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: rotate(90deg);
}

/* Icon Wrap */
.sidq-exit-icon-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 12px;
}
.sidq-exit-icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(239, 68, 68, 0.35) 0%, rgba(245, 158, 11, 0.05) 70%, transparent 100%);
    transform: translate(-50%, -50%);
    border-radius: 50%;
    animation: sidqPulse 2s infinite ease-in-out;
}
.sidq-exit-icon-badge {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 3px solid #fecaca;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin: 0 auto;
    box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.25);
    animation: sidqGiftWobble 2.5s infinite ease-in-out;
}
.sidq-discount-floating-pill {
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 12px;
    border-radius: 20px;
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.35);
    border: 2px solid #ffffff;
}

/* Text */
.sidq-exit-heading {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 8px;
    margin-bottom: 6px;
    line-height: 1.35;
}
.sidq-exit-subtitle {
    font-size: 14.5px;
    color: #475569;
    line-height: 1.5;
    margin-bottom: 16px;
    padding: 0 8px;
}

/* Coupon Ticket */
.sidq-coupon-ticket {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    position: relative;
}
.sidq-coupon-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}
.sidq-coupon-tag {
    font-size: 10.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.sidq-coupon-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 16px;
    font-weight: 800;
    color: #dc2626;
    letter-spacing: 0.5px;
}
.sidq-coupon-divider {
    width: 1px;
    height: 32px;
    background: #e2e8f0;
    margin: 0 10px;
}
.sidq-coupon-right {
    font-size: 11.5px;
    font-weight: 600;
    color: #16a34a;
    white-space: nowrap;
}

/* Product Strip (PDP Only) */
.sidq-exit-product-strip {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f1f5f9;
    border-radius: 12px;
    padding: 8px 12px;
    margin-bottom: 16px;
    text-align: left;
}
.sidq-exit-product-img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    background: #ffffff;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
}
.sidq-exit-product-info {
    flex-grow: 1;
    min-width: 0;
}
.sidq-exit-product-title {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}
.sidq-exit-product-price {
    font-size: 13px;
    font-weight: 700;
    color: #dc2626;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sidq-price-regular {
    font-size: 11.5px;
    color: #94a3b8;
    text-decoration: line-through;
    font-weight: 400;
}

/* CTA Button */
.sidq-exit-claim-btn {
    width: 100%;
    background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
    color: #ffffff;
    border: none;
    border-radius: 14px;
    padding: 14px 20px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.2px;
    cursor: pointer;
    box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.45);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sidq-exit-claim-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px -5px rgba(225, 29, 72, 0.55);
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
}
.sidq-exit-claim-btn:active {
    transform: translateY(0);
}

/* Dismiss Link */
.sidq-exit-dismiss-wrap {
    margin-top: 14px;
}
.sidq-exit-dismiss-link {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    text-decoration: underline;
    transition: color 0.15s ease;
    padding: 4px;
}
.sidq-exit-dismiss-link:hover {
    color: #64748b;
}

/* Animations */
@keyframes sidqGiftWobble {
    0%, 100% { transform: scale(1) rotate(0deg); }
    10% { transform: scale(1.08) rotate(-6deg); }
    20% { transform: scale(1.08) rotate(6deg); }
    30% { transform: scale(1.04) rotate(-3deg); }
    40% { transform: scale(1) rotate(0deg); }
}
@keyframes sidqPulse {
    0%, 100% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.4; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
}

@media (max-width: 480px) {
    .sidq-exit-modal-card {
        padding: 24px 18px 18px 18px;
        border-radius: 20px;
    }
    .sidq-exit-heading {
        font-size: 18px;
    }
    .sidq-exit-subtitle {
        font-size: 14px;
    }
    .sidq-exit-claim-btn {
        font-size: 15px;
        padding: 13px 16px;
    }
}
</style>

<script>
(function() {
    'use strict';

    // Guard: Prevent double initialization
    if (window.__sidqExitIntentInitialized) return;
    window.__sidqExitIntentInitialized = true;

    const STORAGE_KEY = 'sidq_exit_intent_shown';
    const backdrop = document.getElementById('sidqExitIntentBackdrop');
    const closeBtn = document.getElementById('sidqExitCloseBtn');
    const dismissBtn = document.getElementById('sidqExitDismissBtn');
    const claimBtn = document.getElementById('sidqExitClaimBtn');

    if (!backdrop || !claimBtn) return;

    // Check session frequency capping
    function hasShown() {
        try {
            return sessionStorage.getItem(STORAGE_KEY) === '1';
        } catch (e) {
            return false;
        }
    }

    function markShown() {
        try {
            sessionStorage.setItem(STORAGE_KEY, '1');
        } catch (e) {}
    }

    let isVisible = false;

    function showModal(reason) {
        if (hasShown() || isVisible) return;
        markShown();
        isVisible = true;

        backdrop.style.display = 'flex';
        // Force reflow for CSS transition
        backdrop.offsetHeight;
        backdrop.classList.add('is-visible');

        // Optional custom pixel event
        try {
            if (typeof window.fbq === 'function') {
                window.fbq('trackCustom', 'ExitIntentShown', { trigger: reason || 'unknown' });
            }
        } catch(e) {}
    }

    function hideModal() {
        if (!isVisible) return;
        isVisible = false;
        backdrop.classList.remove('is-visible');
        setTimeout(() => {
            if (!isVisible) backdrop.style.display = 'none';
        }, 320);
    }

    // Close handlers
    if (closeBtn) closeBtn.addEventListener('click', hideModal);
    if (dismissBtn) dismissBtn.addEventListener('click', hideModal);
    backdrop.addEventListener('click', function(e) {
        if (e.target === backdrop) hideModal();
    });

    // 1-Click CTA Claim Action
    claimBtn.addEventListener('click', function() {
        const textSpan = claimBtn.querySelector('.sidq-claim-text');
        const spinnerSpan = claimBtn.querySelector('.sidq-claim-spinner');

        claimBtn.disabled = true;
        if (textSpan) textSpan.style.display = 'none';
        if (spinnerSpan) spinnerSpan.style.display = 'inline-flex';

        const payload = {
            product_id: @json($currentProduct ? $currentProduct->id : null),
            variant_id: null,
            coupon_code: @json($couponCode)
        };

        // If on PDP with selected variant ID, capture it
        const variantInput = document.getElementById('selectedVariantId');
        if (variantInput && variantInput.value) {
            payload.variant_id = parseInt(variantInput.value, 10);
        }

        fetch('{{ route('exit-intent.claim') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                window.location.href = '{{ route('checkout') }}';
            }
        })
        .catch(err => {
            // Safe fallback redirect
            const fallbackUrl = '{{ route('checkout') }}' + (@json($couponCode) ? ('?coupon=' + encodeURIComponent(@json($couponCode))) : '');
            window.location.href = fallbackUrl;
        });
    });

    // Don't setup listeners if already triggered in this session
    if (hasShown()) return;

    // ==========================================
    // TRIGGER 1: MOBILE BACK BUTTON INTERCEPT
    // ==========================================
    @if($triggerBack)
    (function setupBackButtonTrigger() {
        try {
            // Push dummy state after 600ms so initial page load completes
            setTimeout(() => {
                if (hasShown()) return;
                history.pushState({ sidq_exit_guard: true }, '', window.location.href);
            }, 600);

            window.addEventListener('popstate', function(e) {
                if (!hasShown()) {
                    // Trap the back navigation and show modal
                    showModal('back_button');
                    // Re-push so if user dismisses, next back click navigates normally
                    history.pushState(null, '', window.location.href);
                }
            });
        } catch(e) {}
    })();
    @endif

    // ==========================================
    // TRIGGER 2: RAPID SCROLL-UP GESTURE
    // ==========================================
    @if($triggerScroll)
    (function setupScrollUpTrigger() {
        let lastScrollY = window.scrollY;
        let lastScrollTime = Date.now();
        let scrollTicking = false;

        window.addEventListener('scroll', function() {
            if (hasShown() || isVisible || scrollTicking) return;
            scrollTicking = true;

            requestAnimationFrame(() => {
                const currentY = window.scrollY;
                const currentTime = Date.now();
                const deltaY = lastScrollY - currentY;
                const deltaTime = currentTime - lastScrollTime;

                // If user has scrolled down past 350px and suddenly flicks upward fast (> 180px in < 220ms)
                if (currentY > 300 && deltaY > 160 && deltaTime < 240) {
                    showModal('scroll_up');
                }

                lastScrollY = currentY;
                lastScrollTime = currentTime;
                scrollTicking = false;
            });
        }, { passive: true });
    })();
    @endif

    // ==========================================
    // TRIGGER 3: IDLE / INACTIVITY TIMER
    // ==========================================
    @if($triggerIdle)
    (function setupIdleTrigger() {
        const idleLimitMs = {{ $idleSeconds }} * 1000;
        let idleTimer = null;

        function resetIdleTimer() {
            if (hasShown() || isVisible) {
                if (idleTimer) clearTimeout(idleTimer);
                return;
            }
            if (idleTimer) clearTimeout(idleTimer);
            idleTimer = setTimeout(() => {
                showModal('idle');
            }, idleLimitMs);
        }

        ['touchstart', 'touchmove', 'mousemove', 'scroll', 'keydown', 'click'].forEach(evt => {
            window.addEventListener(evt, resetIdleTimer, { passive: true });
        });

        resetIdleTimer();
    })();
    @endif

    // ==========================================
    // TRIGGER 4: DESKTOP MOUSE LEAVE
    // ==========================================
    @if($triggerDesktop)
    (function setupDesktopMouseLeave() {
        document.addEventListener('mouseleave', function(e) {
            if (hasShown() || isVisible) return;
            // When mouse pointer leaves near top edge of browser window
            if (e.clientY <= 15) {
                showModal('desktop_mouse_leave');
            }
        });
    })();
    @endif

})();
</script>
