<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { useAuthStore } from './modules/auth/store/authStore'

const authStore = useAuthStore()

// Restaurar sesión al cargar la aplicación
onMounted(async () => {
  if (authStore.isAuthenticated) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.error('Error al restaurar sesión:', error)
    }
  }
})
</script>

<template>
  <header>
    <Navbar />
  </header>

  <main>
    <RouterView />
  </main>

  <Footer />
</template>

<style>
/* Import legacy styles globally if needed, or rely on scoped styles */
@import './assets/css/style.css';
@import './assets/css/estilos.css';
</style>
