<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    // Inyección de Dependencia: El servicio recibe el repositorio
    public function __construct(private ProductRepository $repository)
    {
    }

    // Lógica para listar productos
    public function listar()
    {
        return $this->repository->getAll();
    }

    // Lógica para obtener un producto
    public function obtener($id)
    {
        return $this->repository->find($id);
    }
}
