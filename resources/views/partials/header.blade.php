<header>
  <div class="container-fluid px-md-5">
    <div class="nav-container">
      <img
        src="{{ asset('img/imagencolor.webp') }}"
        alt="Logotipo MOKeys"
        class="logo-img"
      />

      <nav id="navLinks" aria-label="Menú principal">
        <ul class="enlaces_navegacion">
          <li><a href="{{ url('/') }}">Inicio</a></li>
          <li><a href="#">Comprar</a></li>
          <li><a href="#">Vender</a></li>
          <li><a href="{{ url('/contacto') }}">Contacto</a></li>
          <li><a href="{{ url('/formulario') }}">Subir</a></li>
        </ul>
      </nav>

      <div class="d-flex align-items-center gap-3">
        <form class="busqueda d-none d-md-block" action="#" method="get" role="search">
          <input
            type="text"
            placeholder="Buscar..."
            name="q"
            aria-label="Buscar producto"
          />
        </form>
        <a href="/backend/auth/cuenta.php" class="users" aria-label="Perfil"></a>
        <a href="#" class="carro" aria-label="Carrito"></a>
        <button class="menu-toggle" id="menuToggle" aria-label="Menú">
          ☰
        </button>
      </div>
    </div>
  </div>
</header>

<script>
  // Script para el menú hamburguesa que el usuario indicó que "ya pasó".
  // Lo incluimos aquí inline o nos aseguramos que inicio.js lo cubra.
  // Dado que es un partial, es mejor tener el script de toggle controlado.
  // Pero el usuario dijo "el js te lo he pasado antes", refiriendose probablemente a lo que ya estaba.
  // Dejaré que `inicio.js` maneje esto si ya lo hacía, o añadiré el listener simple si hace falta.
  // Revisando layouts/app.blade.php, se carga inicio.js.
  // El usuario pasojs en step 129 ("script src asset js inicio.js").
  // code snippet de JS: 
  /* 
      const menuToggle = document.getElementById("menuToggle");
      const navLinks = document.querySelector(".enlaces_navegacion");
      menuToggle.addEventListener("click", () => { ... });
  */
  // Esto funcionará si los ID y clases coinciden.
</script>