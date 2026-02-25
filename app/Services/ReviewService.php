<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de reseñas.
 * Gestiona la lógica de negocio para crear y validar reseñas de productos.
 */
class ReviewService
{
    public function __construct(private ReviewRepository $repository)
    {
    }

    /**
     * Crea una nueva reseña para un producto.
     * Verifica que el usuario no haya valorado ya el mismo producto.
     *
     * @throws \Exception Si el usuario ya tiene una reseña para este producto.
     */
    public function crearResenya(array $data)
    {
        $userId = Auth::id();
        $data['user_id'] = $userId;

        // Verificar reseña duplicada
        $existe = Review::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($existe) {
            throw new \Exception('Ya has valorado este producto.');
        }

        return $this->repository->create($data);
    }
}
