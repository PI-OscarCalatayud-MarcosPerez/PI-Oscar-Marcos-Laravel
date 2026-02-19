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

    // Obtener todos los registros, con filtros opcionales
    public function getAll(array $filters = [])
    {
        $query = $this->model->with('category');

        if (isset($filters['category'])) {
            $category = $filters['category'];
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            })->orWhere('categoria', $category); // Legacy support
        }

        if (isset($filters['offers']) && $filters['offers']) {
            $query->where('porcentaje_descuento', '>', 0);
        }

        return $query->get();
    }

    // Buscar un registro por ID
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    // Métodos para crear, actualizar y borrar
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
