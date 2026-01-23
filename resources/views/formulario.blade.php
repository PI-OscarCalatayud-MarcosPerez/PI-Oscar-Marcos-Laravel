@extends('layouts.app')

@section('title', 'Importar Productos - MOKeys')

@section('body-class', 'pagina-contacto')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
@endpush

@section('content')
    <main class="contenedor-formulario-principal" id="main-content">
      <div class="caja-formulario">
        <h1>Importación de Productos</h1>
        <p class="subtitulo-form">
          Sube tu archivo CSV para actualizar el catálogo.
        </p>

        <form action="/backend/procesar.php" method="POST" enctype="multipart/form-data">
          <div class="grupo-input">
            <label for="arxiuCsv">Seleccionar archivo (.csv):</label>
            <input type="file" name="arxiuCsv" id="arxiuCsv" required accept=".csv" />
          </div>

          <button type="submit" name="btnSubir" value="Subir" class="btn-enviar">
            Importar Productes
          </button>
        </form>
      </div>
    </main>

    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Importar</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
