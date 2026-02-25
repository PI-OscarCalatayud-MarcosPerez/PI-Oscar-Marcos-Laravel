<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controlador del perfil de usuario.
 * Gestiona la actualización de datos personales y eliminación de cuenta.
 */
class UserController extends Controller
{
    /**
     * Actualiza los datos del perfil del usuario autenticado.
     * Permite modificar nombre, apellido y nombre de usuario.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255',
        ]);

        $request->user()->update($validated);

        return response()->json($request->user());
    }

    /**
     * Elimina la cuenta del usuario autenticado de forma permanente.
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }
}
