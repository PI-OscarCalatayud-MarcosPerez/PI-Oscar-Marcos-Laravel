<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'MOKeys')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="icon" type="image/png" href="{{ asset('img/icono.png') }}" />
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    @stack('styles')
  </head>
  <body class="@yield('body-class')">
    
    <div class="aviso-slider">
      <div class="aviso-slider-content">
        <div>Clave de juegos con un 70% de descuento</div>
        <div>¡Nuevas ofertas cada día!</div>
        <div>¡Las claves más baratas de la web!</div>
        <div>Clave de juegos con un 70% de descuento</div>
      </div>
    </div>

    <!-- Header Partial -->
    @include('partials.header')

    <!-- Main Content -->
    @yield('content')

    <!-- Footer Partial -->
    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
