<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products',          [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{slug}',   [ProductController::class, 'show']);

Route::get('/categories',        [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/banners',           [BannerController::class, 'index']);

Route::post('/coupons/validate', [CouponController::class, 'validate']);

Route::post('/orders',           [OrderController::class, 'store']);
Route::get('/orders/{number}',   [OrderController::class, 'show']);

Route::post('/payments/webhook', [PaymentController::class, 'webhook']);
