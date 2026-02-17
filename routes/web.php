<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;



// NOTA: El frontend ahora se maneja completamente con Vue.js (Vite)
// Las siguientes rutas están comentadas porque Vue SPA maneja estas vistas

// Route::get('/', function () {
//     return view('pages.home');
// })->name('home');

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

// Ruta principal para SPA (Vue)
Route::get('/', function () {
    return view('spa');
});

// Catch-all para cualquier otra ruta que no sea API, la maneja Vue Router
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');
