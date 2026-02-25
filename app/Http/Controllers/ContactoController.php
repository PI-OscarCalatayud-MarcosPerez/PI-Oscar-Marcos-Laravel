<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Controlador de contacto/incidencias.
 * Actúa como proxy entre el frontend y el webhook de n8n
 * para evitar problemas de CORS.
 */
class ContactoController extends Controller
{
    /**
     * Recibe los datos del formulario de contacto y los reenvía al webhook de n8n.
     * Valida los campos obligatorios antes de realizar el envío.
     */
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'motivo' => 'required|string|max:2000',
        ]);

        $n8nUrl = config('services.n8n.webhook_contacto', 'http://n8n:5678/webhook/contacto');

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
            ], 503);
        }
    }
}
