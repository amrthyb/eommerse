<?php
// namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/update-password', [AuthController::class, 'updatePassword']);

Route::get('/products', [ProductsController::class, 'index']);
// Route::middleware('auth:api')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
Route::middleware('auth:sanctum', 'verified')->group(function () {
    Route::put('/update', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // Menambahkan produk ke keranjang
    Route::post('/cart-add', [CartController::class, 'addToCart']);
    Route::delete('/cart-delete', [CartController::class, 'removeFromCart']);
    Route::put('/cart-update', [CartController::class, 'updateQuantity']);
    Route::get('/cart', [CartController::class, 'index']);

    // Fitur Checkout
    Route::post('/checkout', [OrderController::class, 'checkout']);
    // Route::get('/orders', [OrderController::class, 'getOrders']);

    // Rute untuk notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);


});


