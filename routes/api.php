<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')
  ->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('products')->group(function () {
      Route::get('sales-trend', [ProductController::class, 'getSalesTrend']);
      Route::get('sales-comparison', [ProductController::class, 'getSalesComparison']);
    });
    Route::apiResource('products', ProductController::class);
  });
