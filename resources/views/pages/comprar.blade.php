@extends('layouts.app')

@section('title', 'Tienda | MOKeys')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tienda.css') }}" />
@endpush

@section('content')
    <div class="container-fluid breadcrumb-container"
        style="background-color: #f8f9fa; padding: 10px 0; margin-bottom: 20px;">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tienda</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shop-container container-fluid px-md-5">
        <aside class="sidebar-filtros">
            <div class="filter-header">
                <h3>Filtrar por</h3>
                <button id="btn-limpiar" class="btn-clean">Limpiar</button>
            </div>

            <div class="filter-group">
                <h4>Género</h4>
                <label><input type="checkbox" name="cat" value="accion" /> Acción</label>
                <label><input type="checkbox" name="cat" value="rpg" /> RPG</label>
                <label><input type="checkbox" name="cat" value="deportes" />
                    Deportes</label>
                <label><input type="checkbox" name="cat" value="estrategia" />
                    Estrategia</label>
                <label><input type="checkbox" name="cat" value="aventura" />
                    Aventura</label>
                <label><input type="checkbox" name="cat" value="terror" /> Terror</label>
                <label><input type="checkbox" name="cat" value="software" />
                    Software</label>
            </div>

            <div class="filter-group">
                <h4>Plataforma</h4>
                <label><input type="checkbox" name="plat" value="pc" /> PC</label>
                <label><input type="checkbox" name="plat" value="ps5" /> PlayStation
                    5</label>
                <label><input type="checkbox" name="plat" value="xbox" /> Xbox</label>
                <label><input type="checkbox" name="plat" value="switch" /> Switch</label>
            </div>

            <div class="filter-group">
                <h4>Precio Máximo</h4>
                <input type="range" id="priceRange" min="0" max="100" value="100" style="width: 100%" />
                <p>Hasta: <span id="priceValue">100</span>€</p>
            </div>
        </aside>

        <section class="products-section">
            <div class="products-header">
                <span id="result-count">Cargando productos...</span>
                <select id="sortOrder">
                    <option value="default">Orden por defecto</option>
                    <option value="price-asc">Precio: Menor a Mayor</option>
                    <option value="price-desc">Precio: Mayor a Menor</option>
                    <option value="az">Nombre: A-Z</option>
                </select>
            </div>

            <div class="products-grid" id="contenedor-productos"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/tienda.js') }}"></script>
    {{-- El script del menú hamburguesa ya está en el header partial, no hace falta repetirlo --}}
@endpush