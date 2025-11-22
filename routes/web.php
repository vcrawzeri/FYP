<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TryOnController;

// Public routes
Route::middleware(['guestOrVerified'])->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('home');
    Route::get('/product/{product:slug}', [ProductController::class, 'view'])->name('product.view');

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
        Route::post('/remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
        Route::post('/update-quantity/{product:slug}', [CartController::class, 'updateQuantity'])->name('update-quantity');
    });
});

// Authenticated & verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.update');
    Route::post('/profile/password-update', [ProfileController::class, 'passwordUpdate'])->name('profile_password.update');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder'])->name('cart.checkout-order');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/view/{order}', [OrderController::class, 'view'])->name('order.view');
});

// Stripe webhook
Route::post('/webhook/stripe', [CheckoutController::class, 'webhook']);

// Try-On routes
Route::post('/tryon', [TryOnController::class, 'processTryOn'])->name('tryon.process');
Route::get('/tryon/status/{jobId}', [TryOnController::class, 'checkStatus'])->name('tryon.status');

// Test route for API configuration
Route::get('/test-api-config', function () {
    return response()->json([
        'api_key_configured' => !empty(env('TRYON_API_KEY')),
        'api_key' => env('TRYON_API_KEY') ? substr(env('TRYON_API_KEY'), 0, 10) . '...' : 'Not set',
        'environment' => app()->environment(),
        'app_debug' => config('app.debug')
    ]);
});

require __DIR__ . '/auth.php';
