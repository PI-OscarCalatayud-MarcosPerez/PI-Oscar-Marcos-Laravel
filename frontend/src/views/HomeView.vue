<!--
  HomeView: Página principal de MOKeys.
  Muestra el hero con video, tres carruseles de productos
  (comprados, ofertas, software) y una sección de categorías.
-->
<script setup>
import { ref, onMounted, computed } from 'vue';
import ProductService from '../modules/products/services/ProductService';
import ProductCarousel from '../components/ProductCarousel.vue';
import { useRouter } from 'vue-router';
import { usePrefsStore } from '../stores/prefsStore';

const router = useRouter();
const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);
const products = ref([]);
const loading = ref(true);

/** Carga todos los productos desde la API */
const fetchProducts = async () => {
    try {
        const response = await ProductService.getProducts({ per_page: 100 });
        const data = response.data;
        products.value = Array.isArray(data) ? data : (data.products || data.data || []);
    } catch (error) {
        console.error('Error al cargar productos:', error);
    } finally {
        loading.value = false;
    }
};

// Computed: listas filtradas por sección, triplicadas para efecto infinito
const comprados = computed(() => {
    const list = products.value.filter(p => p.seccion === 'comprados');
    return [...list, ...list, ...list];
});

const ofertas = computed(() => {
    const list = products.value.filter(p => p.porcentaje_descuento > 0);
    return [...list, ...list, ...list];
});

const software = computed(() => {
    const list = products.value.filter(p => p.seccion === 'software');
    return [...list, ...list, ...list];
});

/** Navega al detalle del producto */
const goToProduct = (id) => router.push(`/products/${id}`);

/** Lista de categorías disponibles */
const categorias = [
    'Acción', 'Puzzle', 'Deportes', 'RPG', 'Arcade', 'Online',
    'Carreras', 'Estrategia', 'Terror', 'Simulación', 'Survival',
    'Shooter', 'Plataformas', 'Musical', 'Educativo', 'Retro'
];

onMounted(fetchProducts);
</script>

<template>
    <!-- Hero con video de fondo -->
    <div class="inicio-container" style="min-height: 50vh;">
        <div class="video-background">
            <video autoplay muted loop playsinline aria-label="Video ambiental de introducción a videojuegos">
                <source src="/videos/introduccion.mp4" type="video/mp4" />
                Tu navegador no soporta la etiqueta de video.
            </video>
        </div>
        <div class="inicio" style="margin-top: 0;">
            <p class="textoI" style="margin-bottom: 1rem;">
                {{ t.home.heroText }}
            </p>
            <div class="oferta">
                <p>{{ t.home.cheapest }}</p>
                <button class="btnOferta" @click="router.push('/products?offers=true')">{{ t.home.offersBtn }}</button>
            </div>
        </div>
    </div>

    <!-- Carruseles de productos -->
    <main class="container-fluid px-0" id="main-content" role="main">
        <ProductCarousel :title="t.home.mostBought" track-id="lista-comprados" :products="comprados"
            @product-click="goToProduct" />

        <ProductCarousel :title="t.home.bestOffers" track-id="lista-ofertas" :products="ofertas"
            @product-click="goToProduct" />

        <ProductCarousel :title="t.home.software" track-id="lista-nuevos" :products="software"
            @product-click="goToProduct" />

        <!-- Sección de categorías -->
        <section class="categorias-section" aria-label="Categorías">
            <div class="container">
                <h2 class="text-center mb-5 fs-1">{{ t.home.categoriesTitle }}</h2>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-4">
                    <div class="col" v-for="cat in categorias" :key="cat">
                        <button class="categoria-btn"
                            @click="router.push({ path: '/products', query: { category: cat } })">
                            {{ cat }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<style>
@import '../assets/css/home.css';
</style>
