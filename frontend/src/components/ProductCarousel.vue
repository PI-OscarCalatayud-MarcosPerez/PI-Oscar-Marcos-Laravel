<!--
  ProductCarousel: Componente reutilizable de carrusel de productos.
  Muestra una lista de productos con scroll horizontal y botones de navegación.
  Props:
    - products: Array de productos a mostrar
    - title: Título de la sección
    - trackId: ID único del track para el scroll
    - ariaLabel: Etiqueta de accesibilidad
  Emits:
    - product-click: Emitido al hacer clic en un producto (payload: product.id)
-->
<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    products: { type: Array, required: true },
    title: { type: String, required: true },
    trackId: { type: String, required: true },
    ariaLabel: { type: String, default: '' },
});

const emit = defineEmits(['product-click']);

/** Obtiene la URL de imagen del producto con fallback */
const getImage = (product) => {
    let img = product.imagen_url || product.img;
    if (!img || img === '') {
        return 'https://placehold.co/400x600/1a1a2e/ffffff?text=MOKeys';
    }
    if (!img.startsWith('http') && !img.startsWith('/')) {
        img = '/' + img;
    }
    return img;
};

/** Calcula el precio final aplicando el descuento */
const precioFinal = (product) => {
    const precio = parseFloat(product.precio);
    const descuento = product.porcentaje_descuento || 0;
    return (precio * (1 - descuento / 100)).toFixed(2);
};

/** Controla el scroll del carrusel con loop infinito */
const scrollCarousel = (direction) => {
    const track = document.getElementById(props.trackId);
    if (!track) return;

    const item = track.querySelector('.item');
    const scrollAmount = item ? item.offsetWidth + 20 : 300;

    if (direction === 1 && track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
        track.scrollTo({ left: 0, behavior: 'smooth' });
    } else if (direction === -1 && track.scrollLeft <= 0) {
        track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
    } else {
        track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }
};
</script>

<template>
    <h2 class="section-title">{{ title }}</h2>
    <div class="custom-carousel-container" role="region" :aria-label="ariaLabel || title">
        <button class="nav-btn prev-btn" @click="scrollCarousel(-1)" aria-label="Ver anteriores">
            <img src="/img/izquierda.png" alt="" />
        </button>

        <div class="carousel-track carousel" :id="trackId" tabindex="-1">
            <div v-for="product in products" :key="product.id" class="item" @click="emit('product-click', product.id)"
                role="button">
                <div class="item-imagen" style="position: relative;">
                    <img :src="getImage(product)" :alt="product.nombre" loading="lazy" />
                    <span v-if="product.porcentaje_descuento > 0"
                        class="badge badge-discount position-absolute top-0 end-0 m-2">
                        -{{ product.porcentaje_descuento }}%
                    </span>
                </div>
                <div class="item-info">
                    <p class="item-titulo">{{ product.nombre }}</p>
                    <p class="item-descripcion-hover">{{ product.descripcion }}</p>
                    <p class="price">
                        <span v-if="product.porcentaje_descuento > 0" class="text-decoration-line-through me-2"
                            style="font-size: 0.8em; color: #aaa;">
                            {{ parseFloat(product.precio).toFixed(2) }}€
                        </span>
                        <span :class="{ 'text-danger': product.porcentaje_descuento > 0 }">
                            {{ precioFinal(product) }}€
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <button class="nav-btn next-btn" @click="scrollCarousel(1)" aria-label="Ver siguientes">
            <img src="/img/derecha.png" alt="" />
        </button>
    </div>
</template>
