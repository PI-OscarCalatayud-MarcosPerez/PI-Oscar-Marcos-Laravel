<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MOKeys | Inicio</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('img/icono.png') }}" />
  </head>
  <body>
    @include('partials.header') 

    <div class="inicio-container">
       <div class="video-background">
          </div>
       <div class="inicio">
        <p class="textoI">MOKeys: Tu ecommerce de confianza.</p>
       </div>
    </div>

    <div class="carousel-container carrusel-comprados">
      <h2>Juegos más comprados</h2>
      <button class="prev" onclick="moveSlide('lista-comprados', -1)"><img src="{{ asset('img/izquierda.png') }}" /></button>
      
      <div class="carousel" id="lista-comprados">
        @foreach($comprados as $p)
            <div class="item" onclick="window.location.href='{{ route('product.show', $p->id) }}'">
                <div class="item-imagen">
                    <img src="{{ asset($p->imagen_url) }}" alt="{{ $p->nombre }}" loading="lazy" />
                </div>
                <div class="item-info">
                    <p class="item-titulo">{{ $p->nombre }}</p>
                    <p class="price">{{ $p->precio }}€</p>
                </div>
            </div>
        @endforeach
      </div>

      <button class="next" onclick="moveSlide('lista-comprados', 1)"><img src="{{ asset('img/derecha.png') }}" /></button>
    </div>

    <div class="carousel-container carrusel-ofertas">
      <h2>Mejores Ofertas</h2>
       <div class="carousel" id="lista-ofertas">
        @foreach($ofertas as $p)
             <div class="item" onclick="window.location.href='{{ route('product.show', $p->id) }}'">
                <div class="item-imagen"><img src="{{ asset($p->imagen_url) }}" /></div>
                <div class="item-info"><p class="item-titulo">{{ $p->nombre }}</p><p class="price">{{ $p->precio }}€</p></div>
            </div>
        @endforeach
      </div>
      </div>

    <div class="carousel-container carrusel-nuevos">
      <h2>Nuevos Lanzamientos</h2>
      <div class="carousel" id="lista-nuevos">
        @foreach($nuevos as $p)
             <div class="item" onclick="window.location.href='{{ route('product.show', $p->id) }}'">
                <div class="item-imagen"><img src="{{ asset($p->imagen_url) }}" /></div>
                <div class="item-info"><p class="item-titulo">{{ $p->nombre }}</p><p class="price">{{ $p->precio }}€</p></div>
            </div>
        @endforeach
      </div>
    </div>

    @include('partials.footer')

    <script>
        function moveSlide(id, direction) {
            const container = document.getElementById(id);
            const scrollAmount = 300;
            container.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
        }
    </script>
  </body>
</html>