<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Publica de Productos
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']); // Registration for API

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'destroy']);
});
