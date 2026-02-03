<script setup>
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
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
