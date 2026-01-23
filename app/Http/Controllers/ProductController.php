<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
    }

    public function index()
    {
        // El controlador solo coordina: pide datos al servicio y retorna vista
        $products = $this->service->listar();

        // Retorna la vista organizada en la carpeta 'products'
        return view('products.index', compact('products'));
    }

    public function buy()
    {
        return view('pages.comprar');
    }

    public function show($id)
    {
        $product = $this->service->obtener($id);
        return view('products.show', compact('product'));
    }
}
