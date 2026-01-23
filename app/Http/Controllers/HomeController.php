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


        $comprados = Product::inRandomOrder()->take(8)->get(); // 8 aleatorios
        $ofertas = Product::inRandomOrder()->take(8)->get(); // Otros 8 aleatorios
        $nuevos = Product::latest()->take(8)->get();        // Los 8 más recientes

        return view('home', compact('comprados', 'ofertas', 'nuevos'));
    }

    public function showProduct($id)
    {
        $product = Product::with('reviews.user')->findOrFail($id);

        return view('producto', compact('product'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string',
            'rate' => 'required|integer|min:1|max:5'
        ]);

        Review::create([
            'user_id' => auth()->id() ?? 1, 
            'product_id' => $id,
            'comentario' => $request->comentario,
            'estrellas' => $request->rate
        ]);

        return redirect()->route('home')->with('success', '¡Opinión enviada!');
    }
    public function contacto()
{
    return view('contacto.index');
}
}