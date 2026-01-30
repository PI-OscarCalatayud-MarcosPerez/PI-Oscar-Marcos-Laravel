<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements BaseRepository
{
    protected $model;

    // Inyección del Modelo Product
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    // Obtener todos los registros
    public function getAll()
    {
        return $this->model->all();
    }

    // Buscar un registro por ID
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    // Métodos para crear, actualizar y borrar (simplificados/pendientes de uso)
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update($id, array $data)
    { /* ... */
    }
    public function delete($id)
    { /* ... */
    }
}
