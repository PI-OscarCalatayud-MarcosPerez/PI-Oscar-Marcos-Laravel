<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\ProductCode;
use Illuminate\Http\Request;

/**
 * Controlador API de productos.
 * Gestiona las operaciones CRUD del catálogo para la SPA (Vue.js).
 */
class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
    }

    /**
     * Lista todos los productos con filtros opcionales.
     * Soporta filtrado por categoría, plataforma, precio, ofertas y búsqueda.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category', 'offers', 'platform', 'q', 'max_price', 'page', 'per_page']);
        return response()->json($this->service->listar($filters));
    }

    /**
     * Retorna un producto específico con sus reseñas.
     */
    public function show(string $id)
    {
        try {
            $product = $this->service->obtener($id);
            $product->load('reviews.user');
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    /**
     * Crea un producto nuevo o añade un código a uno existente.
     * Si ya existe un producto con el mismo nombre, solo se añade el código.
     */
    public function store(Request $request)
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
            return $this->agregarCodigoExistente($product, $validated['codigo']);
        }

        return $this->crearProductoNuevo($request, $validated);
    }

    /**
     * Devuelve hasta 3 productos recomendados similares al indicado.
     * Busca por la misma categoría; si no hay, devuelve productos aleatorios.
     */
    public function recommendations(string $id)
    {
        try {
            return response()->json($this->service->recomendaciones($id));
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'precio' => 'sometimes|numeric|min:0',
            'categoria' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'seccion' => 'nullable|string',
            'porcentaje_descuento' => 'nullable|integer|min:0|max:100',
            'plataforma' => 'nullable|string',
            'is_eco' => 'nullable|boolean',
            'imagen_url' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);
        $product->load('category');

        return response()->json([
            'message' => 'Producto actualizado',
            'product' => $product,
            'stock' => $product->stock,
        ]);
    }

    public function adminIndex()
    {
        $products = Product::with('category')->get();

        $result = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'nombre' => $product->nombre,
                'descripcion' => $product->descripcion,
                'precio' => $product->precio,
                'sku' => $product->sku,
                'imagen_url' => $product->imagen_url,
                'categoria' => $product->categoria,
                'category' => $product->category,
                'category_id' => $product->category_id,
                'seccion' => $product->seccion,
                'porcentaje_descuento' => $product->porcentaje_descuento,
                'plataforma' => $product->plataforma,
                'is_eco' => $product->is_eco,
                'stock' => $product->stock,
                'codes_count' => $product->productCodes()->count(),
                'sold_count' => $product->productCodes()->where('is_sold', true)->count(),
            ];
        });

        return response()->json($result);
    }

    public function getCodes(string $id)
    {
        $product = Product::findOrFail($id);
        $codes = $product->productCodes()->orderBy('is_sold')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'product' => $product->nombre,
            'codes' => $codes,
            'total' => $codes->count(),
            'available' => $codes->where('is_sold', false)->count(),
            'sold' => $codes->where('is_sold', true)->count(),
        ]);
    }

    public function addCodes(Request $request, string $id)
    {
        $validated = $request->validate([
            'codes' => 'required|array|min:1',
            'codes.*' => 'required|string|max:255|distinct|unique:product_codes,code',
        ]);

        $product = Product::findOrFail($id);
        $created = [];

        foreach ($validated['codes'] as $code) {
            $created[] = ProductCode::create([
                'product_id' => $product->id,
                'code' => $code,
                'is_sold' => false,
            ]);
        }

        return response()->json([
            'message' => count($created) . ' códigos añadidos',
            'stock' => $product->stock,
            'codes_added' => count($created),
        ]);
    }

    private function agregarCodigoExistente(Product $product, string $codigo)
    {
        ProductCode::create([
            'product_id' => $product->id,
            'code' => $codigo,
            'is_sold' => false,
        ]);

        return response()->json([
            'message' => 'Código añadido al producto existente',
            'product' => $product,
            'stock' => $product->stock,
        ]);
    }

    private function crearProductoNuevo(Request $request, array $validated)
    {
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('products', 'public');
            $validated['imagen_url'] = '/storage/' . $path;
        }

        $validated['sku'] = strtoupper(uniqid('SKU-'));

        $codigo = $validated['codigo'];
        unset($validated['codigo']);

        $product = $this->service->crear($validated);

        ProductCode::create([
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
}
