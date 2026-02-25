<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;

/**
 * Controlador de compras.
 * Gestiona el flujo de compra: validación, asignación de código
 * de producto y notificación al webhook de n8n.
 */
class CompraController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    /**
     * Procesa una compra exitosa.
     * Obtiene un código disponible para el producto, lo marca como vendido
     * y envía los datos al webhook de n8n para notificar al comprador.
     */
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

        // Obtener el producto desde el servicio
        try {
            $producto = $this->productService->obtener($productId);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Intentar obtener un código disponible y marcarlo como vendido
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

        // Preparar datos para el webhook de n8n
        $payload = [
            'email_comprador' => $user->email,
            'nombre_usuario' => $user->name,
            'nombre_juego' => $producto->nombre,
            'codigo_juego' => $codigo,
            'plataforma' => $producto->plataforma ?? 'PC',
        ];

        // Enviar notificación a n8n (no bloquea la compra si falla)
        $this->notificarWebhook($payload, $producto->nombre, $codigo);

        return response()->json([
            'message' => 'Compra procesada correctamente',
            'juego' => $producto->nombre,
            'codigo' => $codigo,
        ]);
    }

    /**
     * Envía los datos de compra al webhook de n8n.
     * Si falla, la compra se mantiene pero se registra el error.
     */
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
