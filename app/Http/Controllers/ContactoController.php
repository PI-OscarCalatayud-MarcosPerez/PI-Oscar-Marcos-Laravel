<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactoController extends Controller
{
    /**
     * Recibe los datos del formulario de contacto y los reenvía al webhook de n8n.
     * Actúa como proxy para evitar problemas de CORS entre el frontend y n8n.
     */
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'motivo' => 'required|string|max:2000',
        ]);

        $n8nUrl = env('N8N_WEBHOOK_CONTACTO', 'http://n8n:5678/webhook/contacto');

        try {
            $response = Http::timeout(10)->post($n8nUrl, $validated);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Incidencia registrada correctamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la incidencia en n8n.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo conectar con el servicio de incidencias.',
                'error' => $e->getMessage(),
            ], 503);
        }
    }
}
