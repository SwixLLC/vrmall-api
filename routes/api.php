<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Api\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Api\Rider\AuthController as RiderAuthController;
use App\Http\Controllers\Api\Rider\OrderController as RiderOrderController;

Route::prefix('v1')->group(function () {
    Route::get('/settings', [SettingController::class, 'index']);
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    });
    Route::prefix('seller/auth')->group(function () {
        Route::post('/register', [SellerAuthController::class, 'register']);
        Route::post('/login', [SellerAuthController::class, 'login']);
    });
    Route::prefix('rider/auth')->group(function () {
        Route::post('/register', [RiderAuthController::class, 'register']);
        Route::post('/login', [RiderAuthController::class, 'login']);
    });
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::apiResource('addresses', AddressController::class);
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/add', [CartController::class, 'add']);
        Route::put('/cart/update', [CartController::class, 'update']);
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
        Route::delete('/cart/clear', [CartController::class, 'clear']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
        Route::get('/wallet', [WalletController::class, 'index']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
        Route::post('/wallet/topup', [WalletController::class, 'topup']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/read/{id}', [NotificationController::class, 'markRead']);
        Route::post('/notifications/fcm-token', [NotificationController::class, 'saveFcmToken']);
    });
    Route::middleware('auth:sanctum')->prefix('seller')->group(function () {
        Route::post('/auth/logout', [SellerAuthController::class, 'logout']);
        Route::get('/auth/profile', [SellerAuthController::class, 'profile']);
        Route::apiResource('products', SellerProductController::class);
        Route::get('/orders', [SellerOrderController::class, 'index']);
        Route::get('/orders/{id}', [SellerOrderController::class, 'show']);
        Route::put('/orders/{id}/status', [SellerOrderController::class, 'updateStatus']);
        Route::get('/store', [SellerStoreController::class, 'show']);
        Route::put('/store', [SellerStoreController::class, 'update']);
    });
    Route::middleware('auth:sanctum')->prefix('rider')->group(function () {
        Route::post('/auth/logout', [RiderAuthController::class, 'logout']);
        Route::get('/auth/profile', [RiderAuthController::class, 'profile']);
        Route::get('/orders/available', [RiderOrderController::class, 'available']);
        Route::get('/orders/my', [RiderOrderController::class, 'myOrders']);
        Route::post('/orders/{id}/accept', [RiderOrderController::class, 'accept']);
        Route::post('/orders/{id}/pickup', [RiderOrderController::class, 'pickup']);
        Route::post('/orders/{id}/deliver', [RiderOrderController::class, 'deliver']);
        Route::put('/location', [RiderOrderController::class, 'updateLocation']);
    });
});
Route::get('/settings', [SettingController::class, 'index']);
