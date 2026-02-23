<script setup>
import { ref, computed } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../modules/auth/store/authStore';
import { useRole } from '../modules/roles/composables/useRole';
import { useCartStore } from '../modules/cart/store/cartStore';

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

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/products', query: { q: searchQuery.value } });
        searchQuery.value = ''; // Optional: clear after search
    }
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
                        <RouterLink to="/products">Comprar</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/products?offers=true" :class="{ 'router-link-active': $route.query.offers }">
                            Ofertas</RouterLink>
                    </li>
                    <li v-if="can('sell')">
                        <RouterLink to="/sell">Vender</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/contacto">Contacto</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/about">Nosotros</RouterLink>
                    </li>
                </ul>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <form class="busqueda d-none d-md-block" @submit.prevent="handleSearch" role="search">
                    <input type="text" placeholder="Buscar..." v-model="searchQuery" aria-label="Buscar producto" />
                </form>

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
