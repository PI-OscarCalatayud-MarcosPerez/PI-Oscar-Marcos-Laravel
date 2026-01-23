@extends('layouts.app')

@section('title', 'Iniciar Sesión - MOKeys')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
@endpush

@section('content')
    <main class="contenedor-formulario-principal" id="main-content">
        <div class="caja-formulario" style="max-width: 400px;">
            <h2>Iniciar Sesión</h2>
            <p id="mensaje-error" class="error"
                style="color: #fa4841; display: none; text-align: center; font-weight: bold;"></p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email Address -->
                <div class="grupo-input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="tu@email.com">
                    @error('email')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="grupo-input">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Tu contraseña">
                    @error('password')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="grupo-input"
                    style="flex-direction: row; align-items: center; justify-content: start; gap: 10px;">
                    <input id="remember_me" type="checkbox" name="remember" style="width: auto;">
                    <label for="remember_me" style="margin: 0;">Recordarme</label>
                </div>

                <button type="submit" class="btn-enviar">Entrar</button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                ¿No tienes cuenta? <a href="{{ route('register') }}" style="color: #fa4841; font-weight: bold;">Regístrate
                    aquí</a>.
            </p>
        </div>
    </main>

    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"
                            style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Login</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection