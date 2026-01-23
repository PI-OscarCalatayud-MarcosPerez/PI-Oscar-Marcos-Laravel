@extends('layouts.app')

@section('title', 'Importar Productos - MOKeys')
@section('body-class', 'pagina-contacto')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}" />
    <style>
        /* Estilos específicos para los mensajes de este proceso (portados del original) */
        .resumen-caja {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .resumen-exito {
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: #2e7d32;
        }

        .resumen-error {
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
        }

        .resumen-caja ul {
            margin: 5px 0 0 20px;
            padding: 0;
        }

        .info-columnas {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .input-fichero {
            background: #f9f9f9;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <main class="contenedor-formulario-principal" id="main-content">
        <div class="caja-formulario">
            <h1>Importación de Productos</h1>
            <p class="subtitulo-form">
                Sube tu archivo CSV para actualizar el catálogo.
            </p>

            @if (!empty($messages))
                <div class="resumen-caja resumen-exito">
                    <strong>Estado:</strong>
                    <ul>
                        @foreach ($messages as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                    @if (isset($importedCount) && $importedCount > 0)
                        <p style="margin-top:10px;">
                            <a href="{{ route('products.index') }}" target="_blank" style="color:#2e7d32; font-weight:bold;">
                                Ver Catálogo Actualizado ➜
                            </a>
                        </p>
                    @endif
                </div>
            @endif

            @if (!empty($csvErrors))
                <div class="resumen-caja resumen-error">
                    <strong>⚠️ Errores Críticos:</strong>
                    <ul>
                        @foreach ($csvErrors as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($rowErrors))
                <div class="resumen-caja resumen-error"
                    style="background-color: #fff3e0; border-color: #ffcc80; color: #e65100;">
                    <strong>⚠️ Advertencias (Filas ignoradas):</strong>
                    <ul style="max-height: 100px; overflow-y: auto;">
                        @foreach ($rowErrors as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario Laravel -->
            <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grupo-input">
                    <label for="arxiuCsv">Seleccionar archivo (.csv):</label>
                    <p class="info-columnas">Columnas requeridas: id, nombre, descripcion, precio, img, estoc, categoria</p>

                    <input type="file" name="arxiuCsv" id="arxiuCsv" required accept=".csv" class="input-fichero">
                    @error('arxiuCsv')
                        <small style="color:red;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-enviar">Importar Catálogo</button>
            </form>
        </div>
    </main>

    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"
                            style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Importar</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection