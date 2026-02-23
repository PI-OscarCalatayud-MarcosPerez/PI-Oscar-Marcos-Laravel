<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;

class CompraController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    /**
     * Se llama tras confirmar un pago exitoso.
     * Usa el usuario autenticado para obtener email y nombre.
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
        $email = $user->email;
        $usuario = $user->name;

        // Obtener el producto
        try {
            $producto = $this->productService->obtener($productId);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Intentar obtener un código real (y marcarlo como vendido)
        $codigo = $this->productService->venderCodigo($productId);

        if (!$codigo) {
            Log::error("Intento de compra sin stock", ['product_id' => $productId, 'email' => $email]);
            return response()->json(['error' => 'No hay códigos disponibles para este producto (Stock agotado)'], 409);
        }

        // Datos para n8n
        $payload = [
            'email_comprador' => $email,
            'nombre_usuario' => $usuario,
            'nombre_juego' => $producto->nombre,
            'codigo_juego' => $codigo,
            'plataforma' => $producto->plataforma ?? 'PC',
        ];

        // URL del webhook de n8n
        $webhookUrl = env('N8N_WEBHOOK_URL', 'http://n8n:5678/webhook-test/mokeys-compra');

        try {
            $response = Http::timeout(10)
                ->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('Webhook n8n ejecutado correctamente', ['email' => $email, 'codigo' => $codigo, 'juego' => $producto->nombre]);
                return response()->json([
                    'message' => 'Compra procesada correctamente',
                    'juego' => $producto->nombre,
                    'codigo' => $codigo
                ]);
            } else {
                Log::error('Webhook n8n respondió con error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'message' => 'Compra procesada (email pendiente)',
                    'juego' => $producto->nombre,
                    'codigo' => $codigo
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al llamar webhook n8n: ' . $e->getMessage());
            return response()->json([
                'message' => 'Compra procesada (email pendiente)',
                'juego' => $producto->nombre,
                'codigo' => $codigo
            ]);
        }
    }
}
