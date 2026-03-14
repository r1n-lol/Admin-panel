<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\EnrollmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category_id}', [CategoryController::class, 'show']);
    Route::get('/products/{product_id}', [ProductController::class, 'show']);
    Route::post('/products/{product_id}/buy', [EnrollmentController::class, 'buy']);
    Route::get('/orders', [EnrollmentController::class, 'index']);
    Route::get('/orders/{order}', [EnrollmentController::class, 'cancel']);
});
