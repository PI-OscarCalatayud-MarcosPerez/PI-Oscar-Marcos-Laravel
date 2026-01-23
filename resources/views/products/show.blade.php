@extends('layouts.app')

@section('title', 'Detalle del Producto | MOKeys')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}" />
@endpush

@section('content')
    <div class="product-main" id="main-content">
        <main class="product-detail-container">
            <div class="product-image">
                <img id="p-image" src="" alt="Cargando imagen..." />
            </div>

            <div class="product-info">
                <h1 id="p-title">Cargando...</h1>
                <span id="p-sku" class="product-sku">REF: --</span>
                <p id="p-price" class="product-price">-- €</p>
                <span id="p-stock" class="product-stock">Stock: --</span>
                <p id="p-desc" class="product-desc"></p>
                
                <button class="btn-buy-large">Añadir al Carrito 🛒</button>
            </div>
        </main>

        <section class="reviews-container">
            <div class="reviews-header">
                <h2>Opiniones de la Comunidad</h2>
                <div class="rating-summary">
                    <div class="rating-number" id="average-rating">--</div>
                    <div class="rating-stars">★★★★★</div>
                    <div class="rating-count">Basado en <span id="total-reviews">0</span> opiniones</div>
                </div>
            </div>

            <div id="login-required-message" class="review-form-card" style="display:none;">
                <p style="text-align:center;">Debes <a href="{{ url('/login') }}" style="color:#fa4841;">iniciar sesión</a> para dejar tu valoración.</p>
            </div>

            <div id="comment-form-container" class="review-form-card" style="display:none;">
                <h3>Deja tu valoración</h3>
                <form id="form-comentari">
                    <input type="hidden" id="product_id_hidden" value="">
                    <div class="rate">
                        <input type="radio" id="star5" name="rate" value="5" /><label for="star5" title="5 estrellas">★</label>
                        <input type="radio" id="star4" name="rate" value="4" /><label for="star4" title="4 estrellas">★</label>
                        <input type="radio" id="star3" name="rate" value="3" /><label for="star3" title="3 estrellas">★</label>
                        <input type="radio" id="star2" name="rate" value="2" /><label for="star2" title="2 estrellas">★</label>
                        <input type="radio" id="star1" name="rate" value="1" /><label for="star1" title="1 estrella">★</label>
                    </div>
                    <textarea id="comment-text" placeholder="¿Qué te ha parecido el juego?" required></textarea>
                    <button type="submit" class="btn-submit-review">Publicar Opinión</button>
                </form>
            </div>

            <div class="reviews-list" id="comments-list">
                <p style="text-align: center; color: #777;">Cargando opiniones...</p>
            </div>
        </section>
    </div>

    <div class="container mt-3" style="max-width: 1200px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" style="color:#fa4841; text-decoration:none;">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-name">Producto</li>
            </ol>
        </nav>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/comentarios.js') }}"></script>
    <script src="{{ asset('js/detalle.js') }}"></script>
@endsection
