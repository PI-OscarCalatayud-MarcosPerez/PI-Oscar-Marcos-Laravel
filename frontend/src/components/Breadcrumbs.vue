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

<style scoped>
.breadcrumb-container {
    background-color: transparent;
    padding-top: 10px;
    padding-bottom: 10px;
}

.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    list-style: none;
    background-color: transparent;
}

.breadcrumb-item {
    font-size: 0.9rem;
    color: #6c757d;
}

.breadcrumb-item+.breadcrumb-item {
    padding-left: 0.5rem;
}

.breadcrumb-item+.breadcrumb-item::before {
    display: inline-block;
    padding-right: 0.5rem;
    color: #6c757d;
    content: "/";
}

.breadcrumb-item a {
    color: #0e273f;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-item a:hover {
    color: #fa4841;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #fa4841;
    /* Color activo */
    font-weight: bold;
}
</style>
