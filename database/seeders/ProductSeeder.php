<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'nombre' => 'Elden Ring',
            'descripcion' => 'El nou joc de FromSoftware guanyador del GOTY.',
            'precio' => 59.99,
            'sku' => 'ELD-001',
            'stock' => 50,
            'imagen_url' => 'img/elden.webp', // <--- CAMBIO AQUÍ (antes era 'img')
            'categoria' => 'rpg'
        ]);

        Product::create([
            'nombre' => 'Hades II',
            'descripcion' => 'La seqüela del roguelike més aclamat.',
            'precio' => 29.99,
            'sku' => 'HAD-002',
            'stock' => 100,
            'imagen_url' => 'img/Hades2.webp', // <--- CAMBIO AQUÍ
            'categoria' => 'accion'
        ]);
        
    }
}