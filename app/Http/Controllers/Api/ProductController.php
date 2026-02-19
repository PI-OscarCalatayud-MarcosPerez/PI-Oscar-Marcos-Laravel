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

    // Retorna todos los productos en formato JSON
    // Retorna todos los productos en formato JSON
    /**
     * @OA\Get(
     *      path="/api/products",
     *      tags={"Products"},
     *      summary="Get list of products",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     *     )
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['category', 'offers', 'platform', 'q', 'max_price']);
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
            'porcentaje_descuento' => 'nullable|integer|min:0|max:100',
            'plataforma' => 'nullable|string',
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
