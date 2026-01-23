<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;




Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/contacto', function () {
    return view('pages.contacto');
})->name('contacto');

Route::get('/formulario', [App\Http\Controllers\ProductImportController::class, 'show'])->name('formulario');

require __DIR__ . '/auth.php';

// Catálogo de Productos (usando Controller + Repository/Service)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/comprar', [ProductController::class, 'buy'])->name('products.buy');

Route::get('/products/import', [App\Http\Controllers\ProductImportController::class, 'show'])->name('products.import.show');
Route::post('/products/import', [App\Http\Controllers\ProductImportController::class, 'store'])->name('products.import.store');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
