<script setup>
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import Breadcrumbs from './components/Breadcrumbs.vue'
import UiToast from './components/UiToast.vue'
import ScrollButton from './components/ScrollButton.vue'
</script>

<template>
    <UiToast />
    <header>
        <Navbar />
    </header>

    <main>
        <Breadcrumbs />
        <RouterView />
    </main>

    <ScrollButton />
    <Footer />
</template>

<style>
@import './assets/css/style.css';
</style>
