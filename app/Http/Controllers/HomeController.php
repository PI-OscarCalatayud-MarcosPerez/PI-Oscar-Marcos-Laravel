<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // Cargar página de inicio
    public function index()
    {
        // CAMBIO: En lugar de buscar categorías específicas que quizás no tienes,
        // cogemos productos aleatorios o los últimos creados para rellenar la web.

        $comprados = Product::inRandomOrder()->take(8)->get(); // 8 aleatorios
        $ofertas = Product::inRandomOrder()->take(8)->get(); // Otros 8 aleatorios
        $nuevos = Product::latest()->take(8)->get();        // Los 8 más recientes

        return view('home', compact('comprados', 'ofertas', 'nuevos'));
    }

    // Cargar detalle del producto
    public function showProduct($id)
    {
        // Buscamos el producto y cargamos sus reviews y el autor de la review
        $product = Product::with('reviews.user')->findOrFail($id);

        return view('producto', compact('product'));
    }

    // Guardar un comentario nuevo
    public function storeReview(Request $request, $id)
    {
        // Validamos que envíen texto y estrellas
        $request->validate([
            'comentario' => 'required|string',
            'rate' => 'required|integer|min:1|max:5' // 'rate' es el name de tus radio buttons
        ]);

        Review::create([
            'user_id' => Auth::id(), // Usuario logueado
            'product_id' => $id,
            'comentario' => $request->comentario,
            'estrellas' => $request->rate
        ]);

        return back()->with('success', '¡Tu opinión ha sido publicada!');
    }
}