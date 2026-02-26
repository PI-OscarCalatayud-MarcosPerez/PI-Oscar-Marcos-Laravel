<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:1000',
        ]);

        try {
            $existing = Review::where('user_id', $request->user()->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Ya has valorado este producto.'], 409);
            }

            $review = Review::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'estrellas' => $request->estrellas,
                'comentario' => $request->comentario
            ]);

            return response()->json($review->load('user'), 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al guardar la reseña'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:1000',
        ]);

        $review = Review::findOrFail($id);

        if ($request->user()->role !== 'admin' && $request->user()->id !== $review->user_id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $review->update([
            'estrellas' => $request->estrellas,
            'comentario' => $request->comentario
        ]);

        return response()->json($review->load('user'), 200);
    }

    public function destroy($id, Request $request)
    {
        $review = Review::findOrFail($id);

        if ($request->user()->role !== 'admin' && $request->user()->id !== $review->user_id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Reseña eliminada'], 200);
    }

    public function adminIndex(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $reviews = Review::with(['user:id,name,email', 'product:id,nombre,imagen_url'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    public function adminDestroy($id, Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(['message' => 'Reseña eliminada'], 200);
    }
}
