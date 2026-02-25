<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
| Rutas servidas por Laravel. La SPA de Vue.js es el cliente principal,
| pero se definen rutas Blade para cumplir requisitos MVC.
*/

// ── Página principal (SPA) ──
Route::get('/', fn() => view('spa'));

// ── Vista Blade del catálogo (demostración MVC) ──
Route::get('/productos-blade', [ProductController::class, 'index'])->name('products.index');

// ── Administración (requiere autenticación) ──
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
});

// ── CSRF para autenticación con sesión ──
Route::get('/sanctum/csrf-cookie', [App\Http\Controllers\Api\CsrfCookieController::class, 'show']);

// ── Importación de productos desde CSV ──
Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');

// ── Reseñas (rutas web con redirección Blade) ──
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ── Autenticación ──
require __DIR__ . '/auth.php';

// ── Catch-all: cualquier ruta no definida la maneja Vue Router ──
Route::get('/{any}', fn() => view('spa'))->where('any', '.*');
