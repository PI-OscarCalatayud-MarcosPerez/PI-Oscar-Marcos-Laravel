@extends('layouts.app')

@section('title', 'Iniciar Sesión - MOKeys')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
@endpush

@section('content')
    <main class="contenedor-formulario-principal" id="main-content">
      <div class="caja-formulario" style="max-width: 400px;">
        <h2>Iniciar Sesión</h2>
        <p id="mensaje-error" class="error" style="color: #fa4841; display: none; text-align: center; font-weight: bold;"></p>

        <form action="/backend/auth/login.php" method="POST">
            <div class="grupo-input">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required placeholder="Ej: usuario123">
            </div>
            <div class="grupo-input">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Tu contraseña">
            </div>
            <button type="submit" class="btn-enviar">Entrar</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            ¿No tienes cuenta? <a href="{{ url('/register') }}" style="color: #fa4841; font-weight: bold;">Regístrate aquí</a>.
        </p>
      </div>
    </main>

    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Login</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
