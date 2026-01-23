<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $service)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:1000',
        ]);

        try {
            $this->service->crearResenya($request->all());
            return back()->with('success', '¡Reseña añadida correctamente!');
        } catch (\Exception $e) {
            return back()->withErrors(['review' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $review = \App\Models\Review::findOrFail($id);

        // Check if user is admin (assuming isAdmin method or role check)
        if (request()->user()->role !== 'admin') {
            abort(403, 'No tienes permiso para borrar reseñas.');
        }

        $review->delete();
        return back()->with('success', 'Reseña eliminada.');
    }
}
