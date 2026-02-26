<script setup>
import { ref, computed, watch } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../modules/auth/store/authStore';
import { useRole } from '../modules/roles/composables/useRole';
import { useCartStore } from '../modules/cart/store/cartStore';
import ProductService from '../modules/products/services/ProductService';
import { usePrefsStore } from '../stores/prefsStore';

const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);

const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();
const { can, hasRole } = useRole();
const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};

const userName = computed(() => {
    return authStore.user?.name || authStore.user?.email?.split('@')[0] || 'Usuario';
});

const searchQuery = ref('');
const suggestions = ref([]);
const showSuggestions = ref(false);
let searchTimeout = null;

const getImage = (product) => {
    let img = product.imagen_url || product.img;
    if (!img || img === '') return 'https://placehold.co/40x40/1a1a2e/ffffff?text=M';
    if (!img.startsWith('http') && !img.startsWith('data:')) {
        if (img.startsWith('/')) img = img.substring(1);
        if (!img.startsWith('img/')) img = 'img/' + img;
        img = '/' + img;
    }
    return img;
};

const getFinalPrice = (product) => {
    const price = parseFloat(product.precio || 0);
    if (product.porcentaje_descuento > 0) {
        return (price * (1 - product.porcentaje_descuento / 100)).toFixed(2);
    }
    return price.toFixed(2);
};

watch(searchQuery, (val) => {
    clearTimeout(searchTimeout);
    if (!val || val.trim().length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }
    searchTimeout = setTimeout(async () => {
        try {
            const response = await ProductService.getProducts({ q: val.trim(), per_page: 6 });
            const data = response.data;
            const items = data.data || data || [];
            suggestions.value = Array.isArray(items) ? items.filter(p => p.stock > 0).slice(0, 6) : [];
            showSuggestions.value = suggestions.value.length > 0;
        } catch (err) {
            suggestions.value = [];
            showSuggestions.value = false;
        }
    }, 300);
});

const goToProduct = (product) => {
    searchQuery.value = '';
    suggestions.value = [];
    showSuggestions.value = false;
    router.push(`/products/${product.id}`);
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        suggestions.value = [];
        showSuggestions.value = false;
        router.push({ path: '/products', query: { q: searchQuery.value } });
        searchQuery.value = '';
    }
};

const hideSuggestions = () => {
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};
</script>

<template>
    <div class="container-fluid px-md-5">
        <div class="nav-container" :class="{ 'admin-nav': hasRole('admin') }">
            <RouterLink to="/">
                <img src="/img/imagencolor.webp" alt="Logotipo MOKeys" class="logo-img" />
            </RouterLink>

            <nav id="navLinks" aria-label="Menú principal">
                <ul class="enlaces_navegacion" :class="{ active: isMenuOpen }">
                    <li>
                        <RouterLink to="/products">{{ t.nav.buy }}</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/products?offers=true" :class="{ 'router-link-active': $route.query.offers }">
                            {{ t.nav.offers }}</RouterLink>
                    </li>
                    <li v-if="can('sell')">
                        <RouterLink to="/sell">{{ t.nav.sell }}</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/contacto">{{ t.nav.contact }}</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/about">{{ t.nav.about }}</RouterLink>
                    </li>
                </ul>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <div class="busqueda d-none d-md-block search-wrapper">
                    <form @submit.prevent="handleSearch" role="search">
                        <input type="text" :placeholder="t.nav.search" v-model="searchQuery"
                            aria-label="Buscar producto" @focus="showSuggestions = suggestions.length > 0"
                            @blur="hideSuggestions" autocomplete="off" />
                    </form>
                    <div v-if="showSuggestions" class="search-suggestions">
                        <div v-for="product in suggestions" :key="product.id"
                            class="suggestion-item" @mousedown.prevent="goToProduct(product)">
                            <img :src="getImage(product)" :alt="product.nombre" class="suggestion-img" />
                            <div class="suggestion-info">
                                <span class="suggestion-name">{{ product.nombre }}</span>
                                <span class="suggestion-price">
                                    <template v-if="product.porcentaje_descuento > 0">
                                        <span class="suggestion-price-old">{{ parseFloat(product.precio).toFixed(2) }}€</span>
                                        <span class="suggestion-price-discount">{{ getFinalPrice(product) }}€</span>
                                    </template>
                                    <template v-else>
                                        {{ getFinalPrice(product) }}€
                                    </template>
                                </span>
                            </div>
                            <span v-if="product.porcentaje_descuento > 0" class="suggestion-badge">-{{ product.porcentaje_descuento }}%</span>
                        </div>
                        <div class="suggestion-footer" @mousedown.prevent="handleSearch">
                            {{ t.nav.allResults }}
                        </div>
                    </div>
                </div>

                <RouterLink to="/cart" class="carro" aria-label="Carrito">
                    <span v-if="cartStore.itemCount > 0" class="cart-badge">{{ cartStore.itemCount }}</span>
                </RouterLink>

                <RouterLink v-if="authStore.isAuthenticated" to="/profile" class="users" aria-label="Perfil">
                </RouterLink>
                <RouterLink v-else to="/login" class="users" aria-label="Login"></RouterLink>

                <button class="menu-toggle" id="menuToggle" aria-label="Menú" @click="toggleMenu">
                    {{ isMenuOpen ? '✕' : '☰' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../assets/css/navbar.css';
</style>
