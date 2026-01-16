<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; // Importante añadir tu controlador
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. TU PÁGINA PRINCIPAL (Ahora es la raíz del sitio)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. DETALLE DE PRODUCTO
Route::get('/producto/{id}', [HomeController::class, 'showProduct'])->name('product.show');

// 3. GUARDAR COMENTARIO (Sin middleware 'auth' para que no te obligue a loguearte)
Route::post('/producto/{id}/review', [HomeController::class, 'storeReview'])->name('review.store');

// --- RUTAS DE BREEZE (Dashboard y Perfil) ---

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';