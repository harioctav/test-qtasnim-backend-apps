<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('categories', CategoryController::class);

Route::middleware('auth:sanctum')
  ->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('products', ProductController::class);
  });
