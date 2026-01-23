@extends('layouts.app')

@section('title', 'Mi Perfil - MOKeys')

@section('body-class', 'pagina-contacto')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}" />
@endpush

@section('content')



    <main class="contenedor-formulario-principal">
        <div class="caja-formulario" style="max-width: 500px;">
            <h2 style="color: #0e273f; text-align: center;">Mi Perfil</h2>
            <p style="text-align: center;">Hola, <strong>{{ Auth::user()->name }}</strong> 👋</p>

            @if (session('status') === 'profile-updated')
                <div
                    style="background-color: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px;">
                    ¡Perfil actualizado con éxito!
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('patch')

                <div class="grupo-input">
                    <label>Usuario (No editable):</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled
                        style="background-color: #eee; color: #666;">
                </div>

                <div class="grupo-input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', Auth::user()->email) }}">
                    @error('email')
                        <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grupo-input">
                    <label for="name">Nombre:</label>
                    <input type="text" id="name" name="first_name" value="{{ old('first_name', Auth::user()->name) }}">
                    <!-- Nota: Auth::user()->name es el 'username/name' actual. Si se quiere separar nombre real se usaria last_name -->
                </div>

                <div class="grupo-input">
                    <label for="last_name">Apellidos:</label>
                    <input type="text" id="last_name" name="last_name"
                        value="{{ old('last_name', Auth::user()->last_name) }}">
                </div>

                <button type="submit" class="btn-enviar">Guardar Cambios</button>
            </form>

            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        style="color: #fa4841; font-weight: bold; text-decoration: none;">Cerrar Sesión</a>
                </form>
            </div>
        </div>
    </main>
@endsection
