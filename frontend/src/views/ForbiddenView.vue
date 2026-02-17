<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<template>
    <div class="forbidden-container">
        <div class="forbidden-card">
            <div class="icon">🚫</div>
            <h1>Acceso Denegado</h1>
            <p>No tienes permisos suficientes para acceder a esta página.</p>
            <p class="subtitle">Si crees que esto es un error, contacta con el administrador.</p>

            <div class="actions">
                <button @click="router.push('/')" class="btn-home">
                    🏠 Volver al Inicio
                </button>
                <button @click="router.back()" class="btn-back">
                    ← Volver Atrás
                </button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../assets/css/forbidden.css';
</style>
