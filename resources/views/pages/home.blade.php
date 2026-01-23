@extends('layouts.app')

@section('title', 'MOKeys | Inicio')

@section('content')
  <div class="inicio-container">
    <div class="video-background">
      <video autoplay muted loop playsinline aria-label="Video ambiental de introducción a videojuegos">
        <source src="{{ asset('videos/introduccion.mp4') }}" type="video/mp4" />
        Tu navegador no soporta la etiqueta de video.
      </video>
    </div>
    <div class="inicio">
      <p class="textoI">
        MOKeys es un ecommerce especializado en la venta segura y rápida de
        claves digitales para juegos, software y suscripciones. Compra, recibe
        y activa — todo en minutos.
      </p>
      <div class="oferta">
        <p>LOS JUEGOS MÁS BARATOS</p>
        <button class="btnOferta">OFERTAS</button>
      </div>
    </div>
  </div>

  <main class="container-fluid px-0" id="main-content" role="main">
    <h2 class="section-title">Juegos más comprados</h2>
    <div class="custom-carousel-container" role="region" aria-label="Juegos más comprados">
      <button class="nav-btn prev-btn" onclick="scrollCarousel('lista-comprados', -1)" aria-label="Ver anteriores">
        <img src="{{ asset('img/izquierda.png') }}" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-comprados" tabindex="-1"></div>

      <button class="nav-btn next-btn" onclick="scrollCarousel('lista-comprados', 1)" aria-label="Ver siguientes">
        <img src="{{ asset('img/derecha.png') }}" alt="" />
      </button>
    </div>

    <h2 class="section-title">Mejores Ofertas</h2>
    <div class="custom-carousel-container" role="region" aria-label="Mejores ofertas">
      <button class="nav-btn prev-btn" onclick="scrollCarousel('lista-ofertas', -1)" aria-label="Ver anteriores">
        <img src="{{ asset('img/izquierda.png') }}" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-ofertas" tabindex="-1"></div>

      <button class="nav-btn next-btn" onclick="scrollCarousel('lista-ofertas', 1)" aria-label="Ver siguientes">
        <img src="{{ asset('img/derecha.png') }}" alt="" />
      </button>
    </div>

    <h2 class="section-title">Software Clave y Plataformas de IA</h2>
    <div class="custom-carousel-container" role="region" aria-label="Software e IA">
      <button class="nav-btn prev-btn" onclick="scrollCarousel('lista-nuevos', -1)" aria-label="Ver anteriores">
        <img src="{{ asset('img/izquierda.png') }}" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-nuevos" tabindex="-1"></div>

      <button class="nav-btn next-btn" onclick="scrollCarousel('lista-nuevos', 1)" aria-label="Ver siguientes">
        <img src="{{ asset('img/derecha.png') }}" alt="" />
      </button>
    </div>

    <section class="categorias-section" aria-label="Categorías">
      <div class="container">
        <h2 class="text-center mb-5 fs-1">
          Lo mejor del gaming, por categorías
        </h2>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-4">
          <div class="col">
            <button class="categoria-btn">Acción 🎮</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Puzzle 🧩</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Deportes ⚽</button>
          </div>
          <div class="col"><button class="categoria-btn">RPG 🧙♂️</button></div>
          <div class="col">
            <button class="categoria-btn">Arcade 🕹️</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Online 👥</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Carreras 🏎️</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Estrategia ♟️</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Terror 👻</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Simulación 🛩️</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Survival 🧟♂️</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Shooter 🔫</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Platf. 🧗</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Musical 🎵</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Educa. 📚</button>
          </div>
          <div class="col">
            <button class="categoria-btn">Retro 👾</button>
          </div>
        </div>
      </div>
    </section>
  </main>

  <div class="container-fluid breadcrumb-container">
    <div class="container px-md-5">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb" id="breadcrumbs-list">
          <li class="breadcrumb-item active" aria-current="page">Inicio</li>
        </ol>
      </nav>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/inicio.js') }}"></script>
  <script>
    function scrollCarousel(trackId, direction) {
      const track = document.getElementById(trackId);
      const item = track.querySelector(".item");
      const scrollAmount = item ? item.offsetWidth + 20 : 300;

      track.scrollBy({
        left: direction * scrollAmount,
        behavior: "smooth",
      });
    }
  </script>
@endpush