<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $product->nombre }} | MOKeys</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}" /> <link rel="icon" type="image/png" href="{{ asset('img/icono.png') }}" />
</head>
<body>

    @include('partials.header')

    <div class="product-main">
        <main class="product-detail-container">
            <div class="product-image">
                <img id="p-image" src="{{ asset($product->imagen_url) }}" alt="{{ $product->nombre }}">
            </div>
            <div class="product-info">
                <h1>{{ $product->nombre }}</h1>
                <span class="product-sku">REF: {{ $product->sku }}</span>
                <p class="product-price">{{ $product->precio }} €</p>
                <span class="product-stock">Stock: {{ $product->stock }}</span>
                <p class="product-desc">{{ $product->descripcion }}</p>
                <button class="btn-buy-large">Añadir al Carrito 🛒</button>
            </div>
        </main>

        <section class="reviews-container">
            <div class="reviews-header">
                <h2>Opiniones de la Comunidad</h2>
                <div class="rating-summary">
                    <div class="rating-number">{{ $product->media_estrellas }}</div>
                    
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->media_estrellas))
                                <span class="filled" style="color: #ffc700;">★</span>
                            @else
                                <span style="color: #ccc;">★</span>
                            @endif
                        @endfor
                    </div>
                    
                    <div class="rating-count">Basado en {{ $product->reviews->count() }} opiniones</div>
                </div>
            </div>

            @auth
                <div class="review-form-card">
                    <h3>Deja tu valoración</h3>
                    <form action="{{ route('review.store', $product->id) }}" method="POST">
                        @csrf <div class="rate">
                            <input type="radio" id="star5" name="rate" value="5" /><label for="star5">★</label>
                            <input type="radio" id="star4" name="rate" value="4" /><label for="star4">★</label>
                            <input type="radio" id="star3" name="rate" value="3" /><label for="star3">★</label>
                            <input type="radio" id="star2" name="rate" value="2" /><label for="star2">★</label>
                            <input type="radio" id="star1" name="rate" value="1" /><label for="star1">★</label>
                        </div>
                        
                        <textarea name="comentario" placeholder="¿Qué te ha parecido el juego?" required></textarea>
                        <button type="submit" class="btn-submit-review">Publicar Opinión</button>
                    </form>
                </div>
            @else
                <div class="review-form-card" style="text-align: center;">
                    <p>Debes <a href="{{ route('login') }}" style="color:#fa4841; font-weight:bold;">iniciar sesión</a> para dejar tu valoración.</p>
                </div>
            @endauth
        
            <div class="reviews-list">
                @foreach($product->reviews as $review)
                    <div class="comment-item">
                        <div class="comment-header">
                            <div>
                                <span class="user-name">{{ $review->user->name }}</span>
                                <span class="comment-stars">
                                    @for($j = 1; $j <= 5; $j++)
                                        <span style="color: {{ $j <= $review->estrellas ? '#ffc700' : '#ccc' }};">★</span>
                                    @endfor
                                </span>
                            </div>
                            <span class="comment-date">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="comment-body">
                            {{ $review->comentario }}
                        </div>
                    </div>
                @endforeach

                @if($product->reviews->isEmpty())
                    <p style="text-align: center; color: #999;">Aún no hay opiniones. ¡Sé el primero!</p>
                @endif
            </div>
        </section>
    </div>

    @include('partials.footer')

</body>
</html>