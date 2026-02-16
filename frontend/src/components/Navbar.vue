<script setup>
import { ref, computed } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../modules/auth/store/authStore';
import { useRole } from '../modules/roles/composables/useRole';

const authStore = useAuthStore();
const router = useRouter();
const { can } = useRole();
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
</script>

<template>
    <div class="container-fluid px-md-5">
        <div class="nav-container">
            <img src="/img/imagencolor.webp" alt="Logotipo MOKeys" class="logo-img" />

            <nav id="navLinks" aria-label="Menú principal">
                <ul class="enlaces_navegacion" :class="{ active: isMenuOpen }">
                    <li>
                        <RouterLink to="/">Inicio</RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/products">Comprar</RouterLink>
                    </li>
                    <li><a href="#">Vender</a></li>
                    <li>
                        <RouterLink to="/contacto">Contacto</RouterLink>
                    </li>
                    <li v-if="can('create')">
                        <RouterLink to="/import">Subir</RouterLink>
                    </li>
                </ul>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <form class="busqueda d-none d-md-block" action="#" method="get" role="search">
                    <input type="text" placeholder="Buscar..." name="q" aria-label="Buscar producto" />
                </form>

                <span v-if="authStore.isAuthenticated" class="user-name d-none d-lg-inline">
                    {{ userName }}
                </span>
                <RouterLink v-if="authStore.isAuthenticated" to="/profile" class="users" aria-label="Perfil">
                </RouterLink>
                <RouterLink v-else to="/login" class="users" aria-label="Login"></RouterLink>

                <a href="#" class="carro" aria-label="Carrito"></a>
                <button class="menu-toggle" id="menuToggle" aria-label="Menú" @click="toggleMenu">
                    {{ isMenuOpen ? '✕' : '☰' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Inherits from style.css imported globally */
.nav-container {
    justify-content: space-between !important;
    /* Force space-between to push items to edges */
}

.user-name {
    color: #0e273f;
    font-weight: 600;
    font-size: 0.95rem;
    margin-right: 8px;
}
</style>
