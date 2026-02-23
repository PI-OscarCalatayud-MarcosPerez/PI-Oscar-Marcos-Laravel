<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCode;

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

        if (isset($filters['platform'])) {
            $platforms = explode(',', $filters['platform']);
            $query->where(function ($q) use ($platforms) {
                foreach ($platforms as $platform) {
                    $q->orWhere('plataforma', 'like', "%{$platform}%")
                        ->orWhere('descripcion', 'like', "%{$platform}%");
                }
            });
        }

        if (isset($filters['max_price'])) {
            $query->where('precio', '<=', $filters['max_price']);
        }

        if (isset($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if (isset($filters['offers']) && filter_var($filters['offers'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('porcentaje_descuento', '>', 0);
        }

        $perPage = $filters['per_page'] ?? 12;

        return $query->paginate($perPage);
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

    // Método para vender un código (se elimina tras la venta)
    public function sellCode($productId)
    {
        $code = ProductCode::where('product_id', $productId)
            ->where('is_sold', false)
            ->first();

        if ($code) {
            $codigoTexto = $code->code;
            $code->delete();
            return $codigoTexto;
        }

        return null;
    }
}
