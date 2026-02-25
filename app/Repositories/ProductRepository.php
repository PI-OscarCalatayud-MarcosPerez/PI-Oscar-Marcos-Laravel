<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCode;

/**
 * Repositorio de productos.
 * Gestiona las operaciones de persistencia y consultas sobre el catálogo.
 */
class ProductRepository implements BaseRepository
{
    public function __construct(private Product $model)
    {
    }

    /**
     * Obtiene productos paginados con filtros opcionales.
     * Soporta: categoría, plataforma, precio máximo, búsqueda y ofertas.
     */
    public function getAll(array $filters = [])
    {
        $query = $this->model->with('category');

        if (isset($filters['category'])) {
            $category = $filters['category'];
            $query->whereHas('category', fn($q) => $q->where('name', $category))
                ->orWhere('categoria', $category);
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

        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Busca un producto por ID. Lanza ModelNotFoundException si no existe.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Crea un nuevo producto con los datos proporcionados.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Actualiza un producto existente.
     */
    public function update($id, array $data)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    /**
     * Elimina un producto por ID.
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * Obtiene un código disponible para el producto y lo elimina (venta).
     * Retorna el texto del código o null si no hay stock.
     */
    public function sellCode($productId): ?string
    {
        $code = ProductCode::where('product_id', $productId)
            ->where('is_sold', false)
            ->first();

        if (!$code) {
            return null;
        }

        $codigoTexto = $code->code;
        $code->delete();

        return $codigoTexto;
    }
}
