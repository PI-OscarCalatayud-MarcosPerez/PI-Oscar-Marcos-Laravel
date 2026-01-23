<header>
  <div class="container-fluid px-md-5">
    <div class="nav-container">
      <img src="{{ asset('img/imagencolor.webp') }}" alt="Logotipo MOKeys" class="logo-img" />

      <nav id="navLinks" aria-label="Menú principal">
        <ul class="enlaces_navegacion">
          <li><a href="{{ route('home') }}">Inicio</a></li>
          <li><a href="{{ route('products.buy') }}">Comprar</a></li>
          <li><a href="#">Vender</a></li>
          <li><a href="{{ route('contacto') }}">Contacto</a></li>
          <li><a href="{{ route('formulario') }}">Subir</a></li>
        </ul>
      </nav>

      <div class="d-flex align-items-center gap-3">
        <form class="busqueda d-none d-md-block" action="#" method="get" role="search">
          <input type="text" placeholder="Buscar..." name="q" aria-label="Buscar producto" />
        </form>
        @auth
          <a href="{{ route('dashboard') }}" class="users" aria-label="Perfil"></a>
        @else
          <a href="{{ route('login') }}" class="users" aria-label="Login"></a>
        @endauth
        <a href="#" class="carro" aria-label="Carrito"></a>
        <button class="menu-toggle" id="menuToggle" aria-label="Menú">
          ☰
        </button>
      </div>
    </div>
  </div>
</header>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.querySelector(".enlaces_navegacion");

    if (menuToggle && navLinks) {
      menuToggle.addEventListener("click", () => {
        navLinks.classList.toggle("active");
        menuToggle.textContent = navLinks.classList.contains("active") ? "✕" : "☰";
      });
    }
  });
</script>