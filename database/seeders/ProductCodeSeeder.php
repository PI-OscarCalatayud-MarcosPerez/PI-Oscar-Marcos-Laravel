<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCode;
use Illuminate\Support\Str;

class ProductCodeSeeder extends Seeder
{
    /**
     * Genera códigos de activación para todos los productos.
     * Cada producto tendrá al menos 5 códigos disponibles.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->info('No hay productos. Ejecuta el seeder de productos primero.');
            return;
        }

        $codesPerProduct = 5;
        $totalCreated = 0;

        foreach ($products as $product) {
            $existingCodes = $product->productCodes()->count();
            $codesToCreate = $codesPerProduct - $existingCodes;

            if ($codesToCreate <= 0) {
                $this->command->info("✓ {$product->nombre} ya tiene {$existingCodes} códigos.");
                continue;
            }

            // Generar prefijo basado en el nombre del juego
            $prefix = strtoupper(Str::slug($product->nombre, '-'));
            $prefix = substr($prefix, 0, 15);

            for ($i = 0; $i < $codesToCreate; $i++) {
                $code = $prefix . '-' . strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5));

                ProductCode::create([
                    'product_id' => $product->id,
                    'code' => $code,
                    'is_sold' => false,
                ]);
                $totalCreated++;
            }

            $this->command->info("+ {$product->nombre}: {$codesToCreate} códigos creados (total: {$codesPerProduct})");
        }

        $this->command->info("Códigos creados: {$totalCreated}");
    }
}
