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

<style scoped>
.forbidden-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 20px;
    background-color: #f8f9fa;
}

.forbidden-card {
    background-color: white;
    padding: 60px 40px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(14, 39, 63, 0.15);
    text-align: center;
    max-width: 500px;
}

.icon {
    font-size: 5rem;
    margin-bottom: 20px;
}

.forbidden-card h1 {
    color: #0e273f;
    margin-bottom: 15px;
    font-size: 2rem;
}

.forbidden-card p {
    color: #555;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.subtitle {
    font-size: 0.9rem;
    color: #888;
    margin-bottom: 30px;
}

.actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.actions button {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-home {
    background-color: #fa4841;
    color: white;
}

.btn-home:hover {
    background-color: #d63a34;
}

.btn-back {
    background-color: #6c757d;
    color: white;
}

.btn-back:hover {
    background-color: #5a6268;
}
</style>
