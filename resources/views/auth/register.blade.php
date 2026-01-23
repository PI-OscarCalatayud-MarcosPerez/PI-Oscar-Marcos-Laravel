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

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="grupo-input">
                    <label for="name">Nombre:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Tu nombre">
                    @error('name')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="grupo-input">
                    <label for="last_name">Apellidos:</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                        placeholder="Tus apellidos">
                    @error('last_name')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="grupo-input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="tucorreo@ejemplo.com">
                    @error('email')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="grupo-input">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="grupo-input">
                    <label for="password_confirmation">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Repite tu contraseña">
                    @error('password_confirmation')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-enviar">Registrarse</button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: #fa4841; font-weight: bold;">Inicia sesión
                    aquí</a>.
            </p>

        </div>
    </main>
@endsection