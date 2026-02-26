<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;
use App\Models\Purchase;

class CompraController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function procesarCompraExitosa(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $productId = $validated['product_id'];

        try {
            $producto = $this->productService->obtener($productId);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $codigo = $this->productService->venderCodigo($productId);
        if (!$codigo) {
            Log::error('Intento de compra sin stock', [
                'product_id' => $productId,
                'email' => $user->email,
            ]);
            return response()->json([
                'error' => 'No hay códigos disponibles para este producto (Stock agotado)',
            ], 409);
        }

        $finalPrice = floatval($producto->precio);
        if ($producto->porcentaje_descuento > 0) {
            $finalPrice = $finalPrice * (1 - $producto->porcentaje_descuento / 100);
        }

        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'product_name' => $producto->nombre,
            'price_paid' => round($finalPrice, 2),
            'code' => $codigo,
            'platform' => $producto->plataforma ?? 'PC',
        ]);

        $payload = [
            'email_comprador' => $user->email,
            'nombre_usuario' => $user->name,
            'nombre_juego' => $producto->nombre,
            'codigo_juego' => $codigo,
            'plataforma' => $producto->plataforma ?? 'PC',
        ];

        $this->notificarWebhook($payload, $producto->nombre, $codigo);

        return response()->json([
            'message' => 'Compra procesada correctamente',
            'juego' => $producto->nombre,
            'codigo' => $codigo,
        ]);
    }

    public function miHistorial(Request $request)
    {
        $purchases = Purchase::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($purchases);
    }

    public function adminSales(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $purchases = Purchase::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $purchases->sum('price_paid');
        $totalSales = $purchases->count();
        $avgTicket = $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0;

        $byProduct = $purchases->groupBy('product_name')->map(function ($group) {
            return [
                'product' => $group->first()->product_name,
                'platform' => $group->first()->platform,
                'count' => $group->count(),
                'revenue' => round($group->sum('price_paid'), 2),
            ];
        })->sortByDesc('revenue')->values();

        return response()->json([
            'purchases' => $purchases,
            'stats' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_sales' => $totalSales,
                'avg_ticket' => $avgTicket,
            ],
            'by_product' => $byProduct,
        ]);
    }

    private function notificarWebhook(array $payload, string $juego, string $codigo): void
    {
        $webhookUrl = config('services.n8n.webhook_compra', 'http://n8n:5678/webhook/mokeys-compra');

        try {
            $response = Http::timeout(10)->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('Webhook n8n ejecutado correctamente', [
                    'email' => $payload['email_comprador'],
                    'codigo' => $codigo,
                    'juego' => $juego,
                ]);
            } else {
                Log::error('Webhook n8n respondió con error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al llamar webhook n8n: ' . $e->getMessage());
        }
    }
}
