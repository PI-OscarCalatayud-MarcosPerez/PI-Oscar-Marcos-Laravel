<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    // Inyección de dependencias en el constructor
    public function __construct(private ProductRepository $repository)
    {
    }

    public function listar()
    {
        return $this->repository->getAll();
    }

    public function obtener($id)
    {
        return $this->repository->find($id);
    }

    // Métodos futuros para crear/actualizar
}
