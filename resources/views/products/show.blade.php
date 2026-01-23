@extends('layouts.app')

@section('title', $product->nombre . ' | MOKeys')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}" />
@endpush

@section('content')
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
                @php
                    $userReview = $product->reviews->where('user_id', auth()->id())->first();
                @endphp

                @if($userReview)
                    <div class="review-form-card" style="text-align: center; background-color: #f8f9fa; border-left: 5px solid #4CAF50;">
                        <h3>¡Gracias por tu opinión!</h3>
                        <p>Ya has valorado este juego con <strong>{{ $userReview->estrellas }} estrellas</strong>.</p>
                        <p><i>"{{ $userReview->comentario }}"</i></p>
                    </div>
                @else
                    <div class="review-form-card">
                        <h3>Deja tu valoración</h3>

                        @if ($errors->has('review'))
                            <div class="alert alert-danger">
                                {{ $errors->first('review') }}
                            </div>
                        @endif

                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="rate">
                                <input type="radio" id="star5" name="estrellas" value="5" /><label for="star5">★</label>
                                <input type="radio" id="star4" name="estrellas" value="4" /><label for="star4">★</label>
                                <input type="radio" id="star3" name="estrellas" value="3" /><label for="star3">★</label>
                                <input type="radio" id="star2" name="estrellas" value="2" /><label for="star2">★</label>
                                <input type="radio" id="star1" name="estrellas" value="1" /><label for="star1">★</label>
                            </div>

                            <textarea name="comentario" placeholder="¿Qué te ha parecido el juego?" required></textarea>
                            <button type="submit" class="btn-submit-review">Publicar Opinión</button>
                        </form>
                    </div>
                @endif
            @else
                <div class="review-form-card" style="text-align: center;">
                    <p>Debes <a href="{{ route('login') }}" style="color:#fa4841; font-weight:bold;">iniciar sesión</a> para
                        dejar tu valoración.</p>
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

                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                                    style="display:inline; float:right;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Seguro que quieres borrar este comentario?')">Borrar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($product->reviews->isEmpty())
                    <p style="text-align: center; color: #999;">Aún no hay opiniones. ¡Sé el primero!</p>
                @endif
            </div>
        </section>
    </div>
@endsection
