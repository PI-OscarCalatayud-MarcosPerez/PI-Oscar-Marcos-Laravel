@extends('layouts.app')

@section('content')
<div class="contenedor-formulario-principal">
    <div class="caja-formulario">
        <h1>Contacta con nosotros</h1>
        <p class="subtitulo-form">¿Tienes dudas con tu clave? Envíanos un mensaje.</p>
        
        @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('contacto.store') }}" method="POST" id="formulario">
            @csrf
            <div class="grupo-input">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required value="{{ old('nombre') }}" />
                @error('nombre') <span class="error" style="color:red; font-size: 0.8em;">{{ $message }}</span> @enderror
            </div>

            <div class="grupo-input">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" placeholder="tucorreo@email.com" required value="{{ old('correo') }}" />
                @error('correo') <span class="error" style="color:red; font-size: 0.8em;">{{ $message }}</span> @enderror
            </div>

            <div class="grupo-input">
                <label for="asunto">Asunto:</label>
                <select id="asunto" name="asunto" required>
                    <option value="">Seleccione un asunto</option>
                    <option value="informacion">Información General</option>
                    <option value="soporte">Soporte Técnico</option>
                    <option value="ventas">Ventas</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="Telefono">Teléfono:</label>
                <input type="tel" id="Telefono" name="telefono" placeholder="+34 600 000 000" value="{{ old('telefono') }}" />
            </div>

            <div class="grupo-input">
                <label for="mensaje">Mensaje / Consulta:</label>
                <textarea id="mensaje" name="mensaje" rows="5" required placeholder="Escribe aquí tu consulta...">{{ old('mensaje') }}</textarea>
            </div>

            <div class="grupo-checkbox">
                <label>
                    <input type="checkbox" name="consentimiento" required />
                    Acepto el tratamiento de los datos
                </label>
            </div>

            <button type="submit" class="btn-enviar">Enviar Mensaje</button>
        </form>
    </div>
</div>

<div class="container-fluid breadcrumb-container" style="margin-top: 20px;">
    <div class="container px-md-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contacto</li>
            </ol>
        </nav>
    </div>
</div>
@endsection