<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const breadcrumbs = computed(() => {
    const matched = route.matched;
    const crumbs = [];

    // Siempre inicio
    crumbs.push({
        label: 'Inicio',
        path: '/'
    });

    matched.forEach(match => {
        // Ignorar ruta raíz duplicada si ya la agregamos manual
        if (match.path === '' || match.path === '/') return;

        let label = match.name;
        let path = match.path;

        if (match.name === 'product-detail') {
            label = 'Detalles del Producto';
        } else if (match.name === 'cart') {
            label = 'Carrito de Compra';
        } else if (match.name === 'import-products') {
            label = 'Importar Productos';
        } else if (match.name === 'products-list') {
            label = 'Productos';
        } else if (match.name === 'profile') {
            label = 'Mi Perfil';
        } else if (match.name === 'checkout') {
            label = 'Finalizar Compra';
        } else if (match.name === 'contact') {
            label = 'Contacto';
        }

        // Reemplazar params en path (ej: :id)
        Object.keys(route.params).forEach(param => {
            path = path.replace(':' + param, route.params[param]);
        });

        crumbs.push({
            label: label || match.name,
            path: path
        });
    });

    return crumbs;
});
</script>

<template>
    <nav v-if="route.path !== '/'" aria-label="breadcrumb" class="breadcrumb-container container-fluid px-md-5 my-3">
        <ol class="breadcrumb">
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item"
                :class="{ active: index === breadcrumbs.length - 1 }">
                <router-link v-if="index < breadcrumbs.length - 1" :to="crumb.path">{{ crumb.label }}</router-link>
                <span v-else>{{ crumb.label }}</span>
            </li>
        </ol>
    </nav>
</template>

<style>
@import '../assets/css/breadcrumbs.css';
</style>
