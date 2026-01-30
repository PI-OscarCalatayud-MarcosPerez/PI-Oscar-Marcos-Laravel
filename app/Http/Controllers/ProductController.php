<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Inyectamos el Servicio
    public function __construct(private ProductService $service)
    {
    }

    public function index()
    {
        // 1. El controlador pide los datos al Servicio
        $products = $this->service->listar();

        // 2. Retorna la vista con los datos
        return view('products.index', compact('products'));
    }

    public function buy()
    {
        return view('pages.comprar');
    }

    public function show($id)
    {
        // Obtiene un producto individual
        $product = $this->service->obtener($id);
        return view('products.show', compact('product'));
    }
}
