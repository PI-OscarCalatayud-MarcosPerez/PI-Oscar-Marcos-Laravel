@extends('layouts.app')

@section('title', 'Contacto - MOKeys')

@section('body-class', 'pagina-contacto')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
@endpush

@section('content')
    <main class="contenedor-formulario-principal" id="main-content">
      <div class="caja-formulario">
        <h1>Contacta con nosotros</h1>
        <p class="subtitulo-form">¿Tienes dudas con tu clave? Envíanos un mensaje.</p>
        
        <form action="/backend/formulari.php" method="post" id="formulario">
            <div class="grupo-input">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" />
                <span class="error" id="error-nom"></span>
            </div>
            <div class="grupo-input">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" placeholder="tucorreo@email.com" />
                <span class="error" id="error-correu"></span>
            </div>
            <div class="grupo-input">
                <label for="asunto">Asunto:</label>
                <select id="asunto" name="asunto">
                    <option value="">Seleccione un asunto</option>
                    <option value="informacion">Información General</option>
                    <option value="soporte">Soporte Técnico</option>
                    <option value="ventas">Ventas</option>
                </select>
                <span class="error" id="error-cicle"></span>
            </div>
            <div class="grupo-input">
                <label for="Telefono">Teléfono:</label>
                <input type="tel" id="Telefono" name="telefono" placeholder="+34 600 000 000" />
                <span class="error" id="error-telefono"></span>
            </div>
            <div class="grupo-input">
                <label for="mensaje">Mensaje / Consulta:</label>
                <textarea id="mensaje" name="mensaje" rows="5" required placeholder="Escribe aquí tu consulta..."></textarea>
            </div>
            <div class="grupo-checkbox">
                <label>
                    <input type="checkbox" id="consentimiento" name="consentimiento" />
                    Acepto el tratamiento de los datos
                </label>
                <span class="error" id="error-consentimiento"></span>
            </div>
            <button type="submit" class="btn-enviar">Enviar Mensaje</button>
        </form>
      </div>
    </main>
    
    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contacto</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/validacion.js') }}"></script>
@endsection
