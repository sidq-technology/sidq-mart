<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FailedOrderController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Routes (Customer Facing)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');
Route::get('/product-category/{slug}', [ProductController::class, 'category'])->name('product.category');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');

// Checkout & Orders
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/buy-now/{product:slug}', [CheckoutController::class, 'buyNow'])->name('buy.now');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/capture-draft', [CheckoutController::class, 'captureDraft'])->name('checkout.capture-draft');
Route::get('/order-success/{order_number}', [CheckoutController::class, 'success'])->name('order.success');
Route::post('/order/{order_number}/add-upsell', [CheckoutController::class, 'addUpsellItem'])->name('order.add-upsell');
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');
Route::post('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');

// Order Tracking
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('order.tracking');

// Async Analytics & Meta CAPI Beacon Endpoint
Route::post('/tracking/meta-capi', [TrackingController::class, 'trackCapi'])->name('tracking.meta-capi');

// Legal & Policy Pages
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('pages.privacy');
Route::get('/terms-and-conditions', [HomeController::class, 'termsConditions'])->name('pages.terms');
Route::get('/return-and-refund-policy', [HomeController::class, 'returnRefundPolicy'])->name('pages.return');

/*
|--------------------------------------------------------------------------
| Customer Authentication & Account Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
Route::get('/register', [CustomerAuthController::class, 'showLoginForm'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// Protected Customer Area
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [CustomerDashboardController::class, 'showOrder'])->name('orders.show');
    Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Protected Admin Console Routes (RBAC Protected)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'admin', 'demo.readonly'])->group(function () {
        Route::get('/', fn() => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Orders Management
        Route::get('/orders', [OrderController::class, 'index'])
            ->middleware('permission:orders.view')
            ->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])
            ->middleware('permission:orders.create')
            ->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])
            ->middleware('permission:orders.create')
            ->name('orders.store');
        Route::get('/orders/api/search-products', [OrderController::class, 'searchProducts'])
            ->middleware('permission:orders.create')
            ->name('orders.search-products');
        Route::get('/orders/api/check-new', [OrderController::class, 'checkNewOrders'])
            ->middleware('permission:orders.view')
            ->name('orders.check-new');
        Route::get('/orders/ip-lookup', [OrderController::class, 'lookupIp'])
            ->middleware('permission:orders.view')
            ->name('orders.ip-lookup');
        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->middleware('permission:orders.view')
            ->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])
            ->middleware('permission:orders.edit')
            ->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])
            ->middleware('permission:orders.edit')
            ->name('orders.update');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->middleware('permission:orders.status')
            ->name('orders.status');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])
            ->middleware('permission:orders.view')
            ->name('orders.invoice');
        Route::post('/orders/{order}/send-to-sheet', [OrderController::class, 'sendToSheet'])
            ->middleware('permission:orders.view')
            ->name('orders.send-to-sheet');
        Route::post('/orders/{order}/send-to-courier', [OrderController::class, 'sendToCourier'])
            ->middleware('permission:orders.status')
            ->name('orders.send-to-courier');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
            ->middleware('permission:orders.delete')
            ->name('orders.destroy');

        // Failed & Incomplete Orders
        Route::get('/failed-orders', [FailedOrderController::class, 'index'])
            ->middleware('permission:orders.view')
            ->name('failed-orders.index');
        Route::get('/failed-orders/{id}', [FailedOrderController::class, 'show'])
            ->middleware('permission:orders.view')
            ->name('failed-orders.show');
        Route::post('/failed-orders/{id}/status', [FailedOrderController::class, 'updateStatus'])
            ->middleware('permission:orders.manage')
            ->name('failed-orders.update-status');
        Route::post('/failed-orders/{id}/convert', [FailedOrderController::class, 'convertToOrder'])
            ->middleware('permission:orders.manage')
            ->name('failed-orders.convert');
        Route::delete('/failed-orders/{id}', [FailedOrderController::class, 'destroy'])
            ->middleware('permission:orders.delete')
            ->name('failed-orders.destroy');

        // Products Catalog
        Route::get('/products', [AdminProductController::class, 'index'])
            ->middleware('permission:products.view')
            ->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])
            ->middleware('permission:products.create')
            ->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])
            ->middleware('permission:products.create')
            ->name('products.store');
        Route::get('/products/{product}', [AdminProductController::class, 'show'])
            ->middleware('permission:products.view')
            ->name('products.show');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])
            ->middleware('permission:products.edit')
            ->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])
            ->middleware('permission:products.edit')
            ->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])
            ->middleware('permission:products.delete')
            ->name('products.destroy');
        Route::delete('/product-images/{image}', [AdminProductController::class, 'deleteImage'])
            ->middleware('permission:products.edit')
            ->name('products.deleteImage');
        Route::post('/products/upload-description-image', [AdminProductController::class, 'uploadDescriptionImage'])
            ->middleware('permission:products.create')
            ->name('products.upload-description-image');

        // Media Library API & Uploads
        Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])
            ->name('media.index');
        Route::post('/media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])
            ->name('media.upload');

        // Categories
        Route::resource('categories', CategoryController::class)
            ->middleware('permission:categories.manage');

        // Finance & Revenue Analytics
        Route::get('/finance', [FinanceController::class, 'index'])
            ->middleware('permission:finance.view')
            ->name('finance.index');

        // Marketing & SMS Gateway
        Route::get('/marketing/sms', [MarketingController::class, 'smsIndex'])
            ->middleware('permission:settings.manage')
            ->name('marketing.sms');
        Route::post('/marketing/sms/test', [MarketingController::class, 'sendTestSms'])
            ->middleware('permission:settings.manage')
            ->name('marketing.sms.test');
        Route::post('/marketing/sms/toggle', [MarketingController::class, 'quickToggle'])
            ->middleware('permission:settings.manage')
            ->name('marketing.sms.toggle');
        Route::get('/marketing/sms/balance', [MarketingController::class, 'checkBalance'])
            ->middleware('permission:settings.manage')
            ->name('marketing.sms.balance');

        // Banners
        Route::resource('banners', BannerController::class)
            ->middleware('permission:banners.manage');

        // Coupons
        Route::resource('coupons', CouponController::class)
            ->middleware('permission:coupons.manage');

        // Staff & Users Management
        Route::resource('users', UserController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->middleware('permission:users.manage');

        // Integrations & Tracking Scripts
        Route::get('/integrations', [IntegrationController::class, 'index'])
            ->middleware('permission:integrations.manage')
            ->name('integrations.index');
        Route::post('/integrations', [IntegrationController::class, 'update'])
            ->middleware('permission:integrations.manage')
            ->name('integrations.update');
        Route::post('/integrations/test-google-sheet', [IntegrationController::class, 'testGoogleSheet'])
            ->middleware('permission:integrations.manage')
            ->name('integrations.test-google-sheet');
        Route::post('/integrations/test-meta-capi', [IntegrationController::class, 'testMetaCapi'])
            ->middleware('permission:integrations.manage')
            ->name('integrations.test-meta-capi');
        Route::post('/integrations/test-courier', [IntegrationController::class, 'testCourier'])
            ->middleware('permission:integrations.manage')
            ->name('integrations.test-courier');

        // Site Settings & Branding
        Route::get('/settings', [SettingController::class, 'index'])
            ->middleware('permission:settings.manage')
            ->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('settings.update');

        // Security & Order Fraud Protection
        Route::get('/security', [SecurityController::class, 'index'])
            ->middleware('permission:settings.manage')
            ->name('security.index');
        Route::post('/security', [SecurityController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('security.update');
        Route::post('/security/ip/add', [SecurityController::class, 'addIp'])
            ->middleware('permission:settings.manage')
            ->name('security.ip.add');
        Route::post('/security/ip/remove', [SecurityController::class, 'removeIp'])
            ->middleware('permission:settings.manage')
            ->name('security.ip.remove');

        // Help & Developer Support Portal
        Route::get('/support', [SupportController::class, 'index'])
            ->name('support.index');
    });
});

// Fallback direct storage file server (Guarantees image delivery on cPanel / shared hosting without symlink support)
Route::get('/storage/{path}', function ($path) {
    // 1. Check storage/app/public
    $filePath = storage_path('app/public/' . $path);
    if (file_exists($filePath) && is_file($filePath)) {
        return response()->file($filePath, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    // 2. Check public/storage (if public directory exists directly)
    $pubStoragePath = public_path('storage/' . $path);
    if (file_exists($pubStoragePath) && is_file($pubStoragePath)) {
        return response()->file($pubStoragePath, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    // 3. Check public_path directly
    $rootPub = public_path($path);
    if (file_exists($rootPub) && is_file($rootPub)) {
        return response()->file($rootPub, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    // 4. Return clean placeholder PNG instead of raw HTML 404 to prevent broken img icons
    $placeholder = public_path('images/placeholder-product.png');
    if (file_exists($placeholder)) {
        return response()->file($placeholder, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');

// Quick storage symlink helper for cPanel / live hosting without SSH terminal access
Route::get('/fix-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        return response('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2 style="color:#16a34a;">✅ Storage Linked Successfully!</h2><p style="color:#64748b;">' . htmlspecialchars($output ?: 'The [public/storage] link has been connected to [storage/app/public].') . '</p><a href="/" style="display:inline-block;padding:10px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin-top:15px;">Go to Storefront</a></div>');
    } catch (\Throwable $e) {
        return response('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2 style="color:#dc2626;">Notice on Storage Link</h2><p style="color:#64748b;">' . htmlspecialchars($e->getMessage()) . '</p><p style="color:#16a34a;margin-top:10px;">However, SIDQ Mart\'s Direct Storage Fallback is active, so images will still serve automatically!</p><a href="/" style="display:inline-block;padding:10px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin-top:15px;">Go to Storefront</a></div>');
    }
});
