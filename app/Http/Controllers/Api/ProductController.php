<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
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

    /**
     * Añade un código de activación a un producto existente.
     */
    private function agregarCodigoExistente(\App\Models\Product $product, string $codigo)
    {
        \App\Models\ProductCode::create([
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

    /**
     * Crea un producto nuevo con su primer código de activación.
     */
    private function crearProductoNuevo(Request $request, array $validated)
    {
        // Subir imagen si se proporciona
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('products', 'public');
            $validated['imagen_url'] = '/storage/' . $path;
        }

        // Generar SKU único
        $validated['sku'] = strtoupper(uniqid('SKU-'));

        // Separar el código antes de crear el producto
        $codigo = $validated['codigo'];
        unset($validated['codigo']);

        $product = $this->service->crear($validated);

        // Crear el código de activación asociado
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
}
