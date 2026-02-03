<script setup>
import { ref, onMounted, computed } from 'vue';
import ProductService from '../modules/products/services/ProductService';
import { useRouter } from 'vue-router';

const router = useRouter();
const products = ref([]);
const loading = ref(true);

const fetchProducts = async () => {
  try {
    const response = await ProductService.getProducts();
    // API might return array directly or object with data property
    const data = response.data;
    products.value = Array.isArray(data) ? data : (data.products || data.data || []);
    console.log("Productos cargados:", products.value);
  } catch (error) {
    console.error("Error loading products", error);
  } finally {
    loading.value = false;
  }
};

const comprados = computed(() => products.value.filter(p => p.seccion === 'comprados'));
const ofertas = computed(() => products.value.filter(p => p.seccion === 'ofertas'));
const software = computed(() => products.value.filter(p => p.seccion === 'software'));

// Fallback logic for images
const getImage = (product) => {
    let img = product.imagen_url || product.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('/')) {
        img = '/' + img;
    }
    return img;
};

// Carousel Logic
const scrollCarousel = (id, direction) => {
    const track = document.getElementById(id);
    if(track) {
        const item = track.querySelector(".item");
        const scrollAmount = item ? item.offsetWidth + 20 : 300;
        track.scrollBy({
            left: direction * scrollAmount,
            behavior: "smooth",
        });
    }
};

const goToProduct = (id) => {
    router.push(`/products/${id}`);
};

onMounted(() => {
    fetchProducts();
});
</script>

<template>
  <div class="inicio-container">
    <div class="video-background">
      <video autoplay muted loop playsinline aria-label="Video ambiental de introducción a videojuegos">
        <source src="/videos/introduccion.mp4" type="video/mp4" />
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
    <!-- Carousel 1: Comprados -->
    <h2 class="section-title">Juegos más comprados</h2>
    <div class="custom-carousel-container" role="region" aria-label="Juegos más comprados">
      <button class="nav-btn prev-btn" @click="scrollCarousel('lista-comprados', -1)" aria-label="Ver anteriores">
        <img src="/img/izquierda.png" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-comprados" tabindex="-1">
          <div v-for="product in comprados" :key="product.id" class="item" @click="goToProduct(product.id)" role="button">
            <div class="item-imagen">
                <img :src="getImage(product)" :alt="product.nombre" loading="lazy" />
            </div>
            <div class="item-info">
                <p class="item-titulo">{{ product.nombre }}</p>
                <p class="item-descripcion-hover">{{ product.descripcion }}</p>
                <p class="price">{{ parseFloat(product.precio).toFixed(2) }}€</p>
            </div>
          </div>
      </div>

      <button class="nav-btn next-btn" @click="scrollCarousel('lista-comprados', 1)" aria-label="Ver siguientes">
        <img src="/img/derecha.png" alt="" />
      </button>
    </div>

    <!-- Carousel 2: Ofertas -->
    <h2 class="section-title">Mejores Ofertas</h2>
    <div class="custom-carousel-container" role="region" aria-label="Mejores ofertas">
      <button class="nav-btn prev-btn" @click="scrollCarousel('lista-ofertas', -1)" aria-label="Ver anteriores">
        <img src="/img/izquierda.png" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-ofertas" tabindex="-1">
          <div v-for="product in ofertas" :key="product.id" class="item" @click="goToProduct(product.id)" role="button">
            <div class="item-imagen">
                <img :src="getImage(product)" :alt="product.nombre" loading="lazy" />
            </div>
            <div class="item-info">
                <p class="item-titulo">{{ product.nombre }}</p>
                <p class="item-descripcion-hover">{{ product.descripcion }}</p>
                <p class="price">{{ parseFloat(product.precio).toFixed(2) }}€</p>
            </div>
          </div>
      </div>

      <button class="nav-btn next-btn" @click="scrollCarousel('lista-ofertas', 1)" aria-label="Ver siguientes">
        <img src="/img/derecha.png" alt="" />
      </button>
    </div>

    <!-- Carousel 3: Software/Nuevos -->
    <h2 class="section-title">Software Clave y Plataformas de IA</h2>
    <div class="custom-carousel-container" role="region" aria-label="Software e IA">
      <button class="nav-btn prev-btn" @click="scrollCarousel('lista-nuevos', -1)" aria-label="Ver anteriores">
        <img src="/img/izquierda.png" alt="" />
      </button>

      <div class="carousel-track carousel" id="lista-nuevos" tabindex="-1">
           <div v-for="product in software" :key="product.id" class="item" @click="goToProduct(product.id)" role="button">
            <div class="item-imagen">
                <img :src="getImage(product)" :alt="product.nombre" loading="lazy" />
            </div>
            <div class="item-info">
                <p class="item-titulo">{{ product.nombre }}</p>
                <p class="item-descripcion-hover">{{ product.descripcion }}</p>
                <p class="price">{{ parseFloat(product.precio).toFixed(2) }}€</p>
            </div>
          </div>
      </div>

      <button class="nav-btn next-btn" @click="scrollCarousel('lista-nuevos', 1)" aria-label="Ver siguientes">
        <img src="/img/derecha.png" alt="" />
      </button>
    </div>

    <section class="categorias-section" aria-label="Categorías">
      <div class="container">
        <h2 class="text-center mb-5 fs-1">
          Lo mejor del gaming, por categorías
        </h2>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-4">
           <!-- Hardcoded categories as per Blade -->
          <div class="col"><button class="categoria-btn">Acción 🎮</button></div>
          <div class="col"><button class="categoria-btn">Puzzle 🧩</button></div>
          <div class="col"><button class="categoria-btn">Deportes ⚽</button></div>
          <div class="col"><button class="categoria-btn">RPG 🧙‍♂️</button></div>
          <div class="col"><button class="categoria-btn">Arcade 🕹️</button></div>
          <div class="col"><button class="categoria-btn">Online 👥</button></div>
          <div class="col"><button class="categoria-btn">Carreras 🏎️</button></div>
          <div class="col"><button class="categoria-btn">Estrategia ♟️</button></div>
          <div class="col"><button class="categoria-btn">Terror 👻</button></div>
          <div class="col"><button class="categoria-btn">Simulación 🛩️</button></div>
          <div class="col"><button class="categoria-btn">Survival 🧟‍♂️</button></div>
          <div class="col"><button class="categoria-btn">Shooter 🔫</button></div>
          <div class="col"><button class="categoria-btn">Platf. 🧗</button></div>
          <div class="col"><button class="categoria-btn">Musical 🎵</button></div>
          <div class="col"><button class="categoria-btn">Educa. 📚</button></div>
          <div class="col"><button class="categoria-btn">Retro 👾</button></div>
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
</template>

<style scoped>
/* Scoped styles mainly handled by global style.css, but we can override here */
</style>
