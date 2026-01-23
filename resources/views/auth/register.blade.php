@extends('layouts.app')

@section('title', 'Registro - MOKeys')

@section('body-class', 'pagina-contacto')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}" />
@endpush

@section('content')
    <main class="contenedor-formulario-principal">
      <div class="caja-formulario" style="max-width: 450px;"> 
        <h2 style="text-align: center; color: #0e273f; margin-top: 0;">Crear Cuenta</h2>
        <p class="subtitulo-form">Únete a MOKeys y empieza a ahorrar.</p>

        <p id="mensaje-error" class="error" style="color: #fa4841; display: none; text-align: center; font-weight: bold; margin-bottom: 20px;"></p>

        <form action="/backend/auth/register.php" method="POST">
            
            <div class="grupo-input">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required placeholder="Elige un nombre único">
            </div>

            <div class="grupo-input">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="tucorreo@ejemplo.com">
            </div>

            <div class="grupo-input">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
            </div>

            <button type="submit" class="btn-enviar">Registrarse</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            ¿Ya tienes cuenta? <a href="{{ url('/login') }}" style="color: #fa4841; font-weight: bold;">Inicia sesión aquí</a>.
        </p>

      </div>
    </main>
@endsection
