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
    public function listar(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    // Lógica para obtener un producto
    public function obtener($id)
    {
        return $this->repository->find($id);
    }

    // Lógica para crear un producto
    public function crear(array $data)
    {
        // Aquí podrías añadir lógica extra, como subir imágenes
        return $this->repository->create($data);
    }

    // Lógica para vender código
    public function venderCodigo($productId)
    {
        return $this->repository->sellCode($productId);
    }
}
