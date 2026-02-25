<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ProductImportController;

/*
|--------------------------------------------------------------------------
| Rutas API
|--------------------------------------------------------------------------
| Endpoints de la API REST consumidos por la SPA (Vue.js).
| Organizadas por recurso y nivel de autenticación.
*/

// ── Usuario autenticado (Sanctum) ──
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── Catálogo público ──
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/products/{id}/recommendations', [ProductController::class, 'recommendations']);
Route::apiResource('categories', CategoryController::class);

// ── Autenticación ──
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// ── Endpoints públicos ──
Route::post('/import', [ProductImportController::class, 'importApi']);
Route::post('/contacto', [ContactoController::class, 'enviar']);

// ── Rutas protegidas (requieren sesión autenticada) ──
Route::middleware(['web', 'auth:web'])->group(function () {
    // Productos: creación
    Route::post('/products', [ProductController::class, 'store']);

    // Reseñas: CRUD
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Compra
    Route::post('/compra', [CompraController::class, 'procesarCompraExitosa']);

    // Perfil de usuario
    Route::put('/user/profile', [UserController::class, 'update']);
    Route::delete('/user/delete', [UserController::class, 'destroy']);
});
