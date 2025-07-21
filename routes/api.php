<?php

use App\Http\Controllers\Auth\AuthentificatedSessionController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterUserController::class, 'register']);
Route::post('/login', [AuthentificatedSessionController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthentificatedSessionController::class, 'logout']);
});

Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
