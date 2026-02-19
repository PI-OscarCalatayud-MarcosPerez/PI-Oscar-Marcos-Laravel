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
    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['category', 'offers']);
        return response()->json($this->service->listar($filters));
    }

    // Retorna un producto específico
    public function show(string $id)
    {
        try {
            $product = $this->service->obtener($id);
            $product->load('reviews.user', 'reviews'); // Eager load relationships
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Crea un nuevo producto
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string', // Legacy support
            'category_id' => 'nullable|exists:categories,id', // Relationship
            'seccion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Handle image upload
        $path = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('products', 'public'); // Store in 'storage/app/public/products'
            $validated['imagen_url'] = '/storage/' . $path; // Accessible URL
        }

        // Generate SKU if missing (simple random for now)
        if (!isset($validated['sku'])) {
            $validated['sku'] = strtoupper(uniqid('SKU-'));
        }

        $product = $this->service->crear($validated);

        return response()->json($product, 201);
    }
}
