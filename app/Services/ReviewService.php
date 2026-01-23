<?php

namespace App\Services;

use App\Repositories\ReviewRepository;
use Illuminate\Support\Facades\Auth;

class ReviewService
{
    public function __construct(private ReviewRepository $repository)
    {
    }

    public function crearResenya(array $data)
    {
        // Asignar el usuario autenticado
        $userId = Auth::id();
        $data['user_id'] = $userId;

        // Comprobar si ya existe una reseña de este usuario para este producto
        $existing = \App\Models\Review::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            throw new \Exception("Ya has valorado este producto.");
        }

        return $this->repository->create($data);
    }
}
