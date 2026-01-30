<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService; // Usamos el servicio

class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
    }

    // Retorna todos los productos en formato JSON
    public function index()
    {
        return response()->json($this->service->listar());
    }

    // Retorna un producto específico
    public function show(string $id)
    {
        try {
            $product = $this->service->obtener($id);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }
}
