<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// =========================================================================
// SPRINT 3 - BACKEND LARAVEL V2 (Arquitectura MVC y Rutas)
// =========================================================================
// El frontend SPA (Vue.js) es el cliente principal, pero se han definido
// estas rutas para cumplir con los requisitos de arquitectura MVC de la Iteración 3.

Route::get('/', function () {
    return view('spa');
});

// Ruta para el catálogo en Blade (Requisito C5: "Vista Blade de listado")
// Aunque la SPA maneja el catálogo, este endpoint demuestra el uso de Controladores y Vistas Blade.
Route::get('/productos-blade', [ProductController::class, 'index'])->name('products.index');

// Rutas administrativas restringidas (Requisito C6: "CRUD administrativo")
// Se utiliza el middleware 'auth' para asegurar que solo usuarios autenticados accedan.
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    // En el futuro se añadirían aquí 'edit', 'update', etc.
});

// Route::get('/contacto', function () {
//     return view('pages.contacto');
// })->name('contacto');

Route::get('/sanctum/csrf-cookie', [App\Http\Controllers\Api\CsrfCookieController::class, 'show']); // Custom CSRF endpoint for Session Auth

// Route::get('/formulario', [App\Http\Controllers\ProductImportController::class, 'show'])->name('formulario');

require __DIR__ . '/auth.php';

// Catálogo de Productos (usando Controller + Repository/Service)
// Estas rutas están comentadas porque Vue maneja el frontend
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/comprar', [ProductController::class, 'buy'])->name('products.buy');
// Route::get('/products/import', [App\Http\Controllers\ProductImportController::class, 'show'])->name('products.import.show');

Route::post('/products/import', [App\Http\Controllers\ProductImportController::class, 'store'])->name('products.import.store');

// Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');

// Dashboard y Profile ahora se manejan en Vue
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\ProfileController;

// Rutas de profile comentadas - Vue maneja esto ahora
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// =========================================================================
// SPRINT 3 - INTEGRACIÓN CON VUE SPA
// =========================================================================
// Catch-all para cualquier otra ruta que no sea API, la maneja Vue Router.
// Esto permite que el servidor Laravel sirva la SPA correctamente.
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');
