<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FailedOrderController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\IntegrationController;
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
use App\Http\Controllers\ProductController;
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
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');
Route::post('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');

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
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', fn() => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Orders Management
        Route::get('/orders', [OrderController::class, 'index'])
            ->middleware('permission:orders.view')
            ->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->middleware('permission:orders.view')
            ->name('orders.show');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->middleware('permission:orders.status')
            ->name('orders.status');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])
            ->middleware('permission:orders.view')
            ->name('orders.invoice');
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

        // Categories
        Route::resource('categories', CategoryController::class)
            ->middleware('permission:categories.manage');

        // Finance & Revenue Analytics
        Route::get('/finance', [FinanceController::class, 'index'])
            ->middleware('permission:finance.view')
            ->name('finance.index');

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
