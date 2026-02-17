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
@import './assets/css/style.css';
</style>
