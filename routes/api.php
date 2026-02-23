<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Pública de Productos
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Crear producto (requiere autenticación)
Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
});
Route::get('/products/{id}/recommendations', [App\Http\Controllers\Api\ProductController::class, 'recommendations']);
Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']); // Registration for API
Route::get('/auth/google/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);
Route::post('/import', [App\Http\Controllers\ProductImportController::class, 'importApi']);


Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'destroy']);
});

// Ruta de compra (requiere autenticación)
Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/compra', [App\Http\Controllers\CompraController::class, 'procesarCompraExitosa']);
});

// Perfil de usuario (requiere autenticación)
Route::middleware(['web', 'auth:web'])->group(function () {
    Route::put('/user/profile', function (Request $request) {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json($user);
    });

    Route::delete('/user/delete', function (Request $request) {
        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    });
});
