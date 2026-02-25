<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

/**
 * Servicio de productos.
 * Encapsula la lógica de negocio del catálogo, delegando
 * las operaciones de persistencia al repositorio.
 */
class ProductService
{
    public function __construct(private ProductRepository $repository)
    {
    }

    /**
     * Lista productos con filtros opcionales (categoría, precio, búsqueda, etc.).
     */
    public function listar(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Obtiene un producto por su ID. Lanza excepción si no existe.
     */
    public function obtener($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Crea un producto nuevo en la base de datos.
     */
    public function crear(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Vende un código de activación para el producto indicado.
     * Retorna el código vendido o null si no hay stock.
     */
    public function venderCodigo($productId)
    {
        return $this->repository->sellCode($productId);
    }

    /**
     * Devuelve hasta 3 productos recomendados.
     * Busca en la misma categoría; si no hay, devuelve aleatorios.
     */
    public function recomendaciones($id): \Illuminate\Support\Collection
    {
        $product = $this->obtener($id);

        // Buscar productos de la misma categoría
        $recomendados = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Fallback: si no hay similares, devolver productos aleatorios
        if ($recomendados->isEmpty()) {
            $recomendados = Product::where('id', '!=', $id)
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        return $recomendados;
    }
}
