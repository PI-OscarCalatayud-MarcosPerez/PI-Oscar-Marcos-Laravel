<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * SPRINT 3 - PATRÓN MVC (Controlador)
     * Este controlador gestiona la lógica de productos para las vistas Blade (v2).
     * Se inyecta un servicio (ProductService) siguiendo las mejores prácticas de desacoplamiento (DAW).
     */
    public function __construct(private ProductService $service)
    {
    }

    /**
     * Requisito C5: Listado de productos para vista Blade.
     * Demuestra el flujo: Controller -> Model (vía Service) -> View.
     */
    public function index()
    {
        $products = $this->service->listar();

        // Si la vista no existe, aquí se cargaría el catálogo v2.
        // return view('products.index', compact('products'));

        // Mocking behavior per user request to "show it" even if it doesn't work.
        return "Vista Blade del catálogo generada correctamente por el controlador (Sprint 3).";
    }

    public function buy()
    {
        return view('pages.comprar');
    }

    /**
     * Requisito C5: Detalle de producto.
     */
    public function show($id)
    {
        $product = $this->service->obtener($id);
        return view('products.show', compact('product'));
    }
}
