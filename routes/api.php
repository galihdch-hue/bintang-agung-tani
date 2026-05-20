<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/slug/{slug}', [ProductController::class, 'showBySlug']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/me', [AuthController::class, 'me']);
  Route::get('/dashboard', [DashboardController::class, 'index']);

  Route::get('/cart', [CartController::class, 'index']);
  Route::get('/cart/count', [CartController::class, 'count']);
  Route::post('/cart', [CartController::class, 'store']);
  Route::put('/cart/{cartItem}', [CartController::class, 'update']);
  Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
  Route::delete('/cart', [CartController::class, 'clear']);

  Route::get('/profile', [UserController::class, 'profile']);
  Route::post('/profile', [UserController::class, 'updateProfile']);
  Route::post('/profile/password', [UserController::class, 'changePassword']);
  Route::delete('/profile/photo', [UserController::class, 'destroyProfilePhoto']);

  Route::get('/addresses', [UserController::class, 'addresses']);
  Route::post('/addresses', [UserController::class, 'storeAddress']);
  Route::put('/addresses/{address}', [UserController::class, 'updateAddress']);
  Route::delete('/addresses/{address}', [UserController::class, 'destroyAddress']);
  Route::patch('/addresses/{address}/default', [UserController::class, 'setDefaultAddress']);

  Route::get('/orders', [OrderController::class, 'index']);
  Route::get('/orders/{order}', [OrderController::class, 'show']);
  Route::post('/orders', [OrderController::class, 'store']);
  Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

  Route::get('/payment-methods', [PaymentController::class, 'methods']);
  Route::post('/orders/{order}/payment-method', [PaymentController::class, 'selectMethod']);
  Route::post('/orders/{order}/upload-proof', [PaymentController::class, 'uploadProof']);
});
