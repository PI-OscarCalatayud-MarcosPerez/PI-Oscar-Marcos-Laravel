<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Ensure Categories Exist
        $categories = ['rpg', 'accion', 'deportes', 'plataformas', 'terror', 'shooter', 'software', 'ia'];
        foreach ($categories as $cat) {
            \App\Models\Category::firstOrCreate(['name' => $cat]);
        }

        // 1. Productos individuales (Rellenan la base de datos inicialmente)
        $individuales = [
            ['Elden Ring', 'El nuevo juego de FromSoftware ganador del GOTY.', 59.99, 'ELD-001', 50, 'img/elden.webp', 'rpg'],
            ['Hades II', 'La secuela del roguelike más aclamado.', 29.99, 'HAD-002', 100, 'img/Hades2.webp', 'rpg'],
        ];

        foreach ($individuales as $p) {
            Product::updateOrCreate(
                ['sku' => $p[3]],
                [
                    'nombre' => $p[0],
                    'descripcion' => $p[1],
                    'precio' => $p[2],
                    'stock' => $p[4],
                    'imagen_url' => $p[5],
                    'categoria' => $p[6],
                    'category_id' => \App\Models\Category::where('name', $p[6])->first()->id ?? null
                ]
            );
        }

        // 2. Productos organizados por Sección de la Home
        // Estructura: [Nombre, Descripción, Precio, SKU, Stock, Imagen, Categoría Real, Descuento, Plataforma]
        $secciones = [
            'comprados' => [
                ['Marvel\'s Spider-Man 2', 'Increíble juego de acción y aventura.', 69.99, 'SPM-002', 200, 'img/Spider-Man.webp', 'accion', 0, 'PS5'],
                ['EA Sports FC 24', 'El rey del fútbol vuelve con nueva marca.', 59.99, 'FC-24', 300, 'img/FC.webp', 'deportes', 0, 'PC'],
                ['Monster Hunter Wilds', 'Caza monstruos gigantes en entornos salvajes.', 69.99, 'MHW-003', 150, 'img/MonsterHunterWilds.jpeg', 'rpg', 0, 'PC'],
                ['Hollow Knight: Silksong', 'La esperada secuela de Hollow Knight.', 29.99, 'HKS-004', 500, 'img/silksong.webp', 'plataformas', 0, 'Switch'],
                ['NBA 2K26', 'El simulador de baloncesto definitivo.', 69.99, 'NBA-26', 120, 'img/nba2k26.jpeg', 'deportes', 0, 'Xbox'],
                ['Silent Hill f', 'El terror psicológico regresa.', 59.99, 'SHF-005', 80, 'img/silenthillf.jpeg', 'terror', 0, 'PC'],
                ['Final Fantasy VII Rebirth', 'La continuación de la épica aventura de Cloud.', 79.99, 'FF7-R', 100, 'img/ff7rebirth.webp', 'rpg', 0, 'PS5'],
                ['Tekken 8', 'Siente el poder de cada golpe.', 69.99, 'TK-8', 120, 'img/tekken8.webp', 'accion', 0, 'PC'],
                ['God of War Ragnarök', 'El viaje de Kratos y Atreus continúa.', 59.99, 'GOW-R', 150, 'img/gowragnarok.webp', 'accion', 0, 'PC'],
                ['Starfield', 'Explora el espacio en este RPG de Bethesda.', 69.99, 'SF-001', 200, 'img/starfield.webp', 'rpg', 0, 'PC'],
            ],
            'ofertas' => [
                ['Borderlands 4', 'Looter shooter caótico y divertido.', 19.99, 'BOR-006', 100, 'img/Borderlands4.jpeg', 'shooter', 50, 'PC'],
                ['Elden Ring: Shadow of the Erdtree', 'Expansión masiva del GOTY.', 15.00, 'ELD-DLC', 200, 'img/elden.webp', 'rpg', 30, 'PC'],
                ['Hades', 'El roguelike original que definió el género.', 9.99, 'HAD-001', 150, 'img/Hades2.webp', 'rpg', 40, 'Switch'],
                ['Repo Man', 'RPG narrativo de gestión de deudas.', 5.00, 'REP-007', 50, 'img/repo.webp', 'rpg', 25, 'PC'],
                ['Battle Bit', 'Shooter masivo low-poly.', 4.99, 'BBL-008', 300, 'img/battel.jpeg', 'shooter', 10, 'PC'],
                ['Peak Design', 'Herramienta de diseño favorita.', 8.50, 'PEAK-009', 60, 'img/peak.jpeg', 'software', 15, 'PC'],
                ['Cyberpunk 2077: Ultimate Edition', 'Sumérgete en el oscuro futuro de Night City.', 29.99, 'CP-77', 200, 'img/cyberpunk2077.webp', 'rpg', 60, 'PC'],
                ['The Witcher 3: Wild Hunt', 'Caza monstruos en esta mítica aventura.', 14.99, 'W3-WH', 400, 'img/witcher3.webp', 'rpg', 70, 'PC'],
                ['Red Dead Redemption 2', 'Una epopeya del salvaje oeste.', 19.79, 'RDR-2', 300, 'img/rdr2.webp', 'accion', 67, 'PC'],
                ['Doom Eternal', 'Arrasa con el infierno.', 9.99, 'DOOM-E', 150, 'img/doometernal.webp', 'shooter', 75, 'PC'],
                ['Civilization VI', 'Construye un imperio que resista el paso del tiempo.', 5.99, 'CIV-6', 500, 'img/civilization6.webp', 'rpg', 90, 'PC'],
                ['Portal 2', 'Física y puzles en Aperture Science.', 0.99, 'PORT-2', 999, 'img/portal2.webp', 'plataformas', 90, 'PC'],
            ],
            'software' => [
                ['Windows 11 Pro', 'El sistema operativo más reciente de Microsoft.', 129.99, 'WIN-11', 1000, 'img/windows11.jpg', 'software', 0, 'PC'],
                ['Microsoft Office 365', 'Suite de productividad en la nube.', 69.99, 'OFF-365', 500, 'img/office365.jpeg', 'software', 0, 'PC'],
                ['Adobe Creative Cloud', 'Todas las apps creativas de Adobe.', 299.99, 'ADB-CC', 100, 'img/adobe.png', 'software', 0, 'PC'],
                ['ChatGPT Plus', 'IA conversacional avanzada con GPT-4.', 20.00, 'GPT-PLUS', 9999, 'img/chatgpt.jpg', 'ia', 0, 'Web'],
                ['Gemini Advanced', 'El model de IA más capaz de Google.', 19.99, 'GOO-GEM', 9999, 'img/gemini.jpeg', 'ia', 0, 'Web'],
                ['Perplexity Pro', 'Búsqueda impulsada por IA.', 20.00, 'PER-PRO', 500, 'img/perplexity.jpeg', 'ia', 0, 'Web'],
                ['AutoCAD 2024', 'Diseño asistido por ordenador profesional.', 1500.00, 'ACAD-24', 50, 'img/autocad.webp', 'software', 10, 'PC'],
                ['IntelliJ IDEA Ultimate', 'El IDE más inteligente para Java.', 149.00, 'IJ-ULT', 100, 'img/intellij.webp', 'software', 20, 'PC'],
            ]
        ];

        // 3. Bucle dinámico para crear todos los productos de las secciones
        foreach ($secciones as $nombreSeccion => $listaProductos) {
            foreach ($listaProductos as $p) {
                Product::updateOrCreate(
                    ['sku' => $p[3]],
                    [
                        'nombre' => $p[0],
                        'descripcion' => $p[1],
                        'precio' => $p[2],
                        'stock' => $p[4],
                        'imagen_url' => $p[5],
                        'categoria' => $p[6], // Categoría real (ej. 'rpg')
                        'seccion' => $nombreSeccion, // Sección de la home (ej. 'comprados')
                        'porcentaje_descuento' => $p[7],
                        'plataforma' => $p[8],
                        'category_id' => \App\Models\Category::where('name', $p[6])->first()->id ?? null,
                        'is_eco' => rand(0, 100) < 20 // 20% likely to be eco
                    ]
                );
            }
        }
    }
}
