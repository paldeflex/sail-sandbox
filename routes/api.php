<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\JwtAuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware([JwtMiddleware::class, AdminMiddleware::class])->group(function () {
    Route::apiResource('products', ProductController::class);
});

Route::post('register', [JwtAuthController::class, 'register']);
Route::post('login', [JwtAuthController::class, 'login']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('me', [JwtAuthController::class, 'getUser']);
    Route::post('logout', [JwtAuthController::class, 'logout']);
});
