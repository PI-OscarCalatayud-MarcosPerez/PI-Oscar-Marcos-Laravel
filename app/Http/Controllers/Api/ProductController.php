<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService; // Usamos el servicio
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
    }
    //store
    // Retorna todos los productos en formato JSON
    // Retorna todos los productos en formato JSON

    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['category', 'offers', 'platform', 'q', 'max_price', 'page', 'per_page']);
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

    // Crea un producto nuevo o añade un código a uno existente
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'codigo' => 'required|string|max:255|unique:product_codes,code',
            'categoria' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'seccion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
            'porcentaje_descuento' => 'nullable|integer|min:0|max:100',
            'plataforma' => 'nullable|string',
        ]);

        // Buscar si ya existe un producto con el mismo nombre
        $product = \App\Models\Product::where('nombre', $validated['nombre'])->first();

        if ($product) {
            // Producto existente: solo añadir el código
            \App\Models\ProductCode::create([
                'product_id' => $product->id,
                'code' => $validated['codigo'],
                'is_sold' => false,
            ]);

            return response()->json([
                'message' => 'Código añadido al producto existente',
                'product' => $product,
                'stock' => $product->stock,
            ], 200);
        }

        // Producto nuevo: crear producto + código
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('products', 'public');
            $validated['imagen_url'] = '/storage/' . $path;
        }

        if (!isset($validated['sku'])) {
            $validated['sku'] = strtoupper(uniqid('SKU-'));
        }

        // Quitar 'codigo' antes de crear el producto
        $codigo = $validated['codigo'];
        unset($validated['codigo']);

        $product = $this->service->crear($validated);

        // Crear el código para el nuevo producto
        \App\Models\ProductCode::create([
            'product_id' => $product->id,
            'code' => $codigo,
            'is_sold' => false,
        ]);

        return response()->json([
            'message' => 'Producto creado con código',
            'product' => $product,
            'stock' => 1,
        ], 201);
    }
    public function recommendations($id)
    {
        try {
            $product = $this->service->obtener($id);
            // Simple logic: Same category, different ID
            $recommendations = \App\Models\Product::where('category_id', $product->category_id)
                ->where('id', '!=', $id)
                ->inRandomOrder()
                ->take(3)
                ->get();

            // Fallback: If no related products, return random ones
            if ($recommendations->isEmpty()) {
                $recommendations = \App\Models\Product::where('id', '!=', $id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }

            return response()->json($recommendations);
        } catch (\Exception $e) {
            return response()->json([], 200); // Return empty array on error to avoiding breaking UI
        }
    }
}
