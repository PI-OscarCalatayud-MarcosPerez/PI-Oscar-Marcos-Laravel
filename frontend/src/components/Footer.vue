<template>
  <footer>
    <div class="footer-contenido">
      <div class="footer-col footer-logo-col">
        <img src="/img/imagensincolor.png" alt="MOKeys" class="footer-logo-img" />
        <p class="footer-desc">Tu tienda de claves de juegos y software de confianza.</p>
      </div>

      <div class="footer-col">
        <h6>Navegación</h6>
        <RouterLink to="/products">Comprar</RouterLink>
        <RouterLink to="/products?offers=true">Ofertas</RouterLink>
        <RouterLink to="/about">Sobre Nosotros</RouterLink>
        <RouterLink to="/contacto">Contacto</RouterLink>
      </div>

      <div class="footer-col">
        <h6>Mi cuenta</h6>
        <RouterLink to="/login">Iniciar sesión</RouterLink>
        <RouterLink to="/register">Registrarse</RouterLink>
        <RouterLink to="/cart">Mi carrito</RouterLink>
        <RouterLink to="/profile">Mi perfil</RouterLink>
      </div>

      <div class="footer-col">
        <h6>Contacto</h6>
        <span>📧 soporte@mokeys.com</span>
        <span>📍 Valencia, España</span>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© 2025 MOKeys · Todos los derechos reservados</span>
    </div>
  </footer>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>

<style>
@import '../assets/css/footer.css';
</style>
