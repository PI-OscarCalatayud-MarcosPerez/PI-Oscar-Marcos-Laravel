<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../store/authStore';
import { useUiStore } from '@/stores/uiStore';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const uiStore = useUiStore();

onMounted(async () => {
    const token = route.query.token;
    const isAdmin = route.query.is_admin === 'true';

    if (token) {
        try {
            // Manually set session
            localStorage.setItem('token', token);
            localStorage.setItem('user_role', isAdmin ? 'admin' : 'user');
            
            // Update store state
            authStore.token = token;
            authStore.role = isAdmin ? 'admin' : 'user';
            authStore.isAuthenticated = true;

            // Fetch full user profile to ensure everything is synced
            // You might need to implement a method in authStore like fetchUser()
            // await authStore.fetchUser(); 
            
            uiStore.showToast('success', 'Inicio de sesión con Google exitoso');
            router.push('/profile');
        } catch (error) {
            console.error("Google Auth Error:", error);
            uiStore.showToast('error', 'Error al procesar el inicio de sesión con Google');
            router.push('/login');
        }
    } else {
        uiStore.showToast('error', 'No se recibió el token de autenticación');
        router.push('/login');
    }
});
</script>

<template>
    <div class="callback-container">
        <h2>Procesando inicio de sesión...</h2>
        <div class="spinner"></div>
    </div>
</template>

<style scoped>
.callback-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 60vh;
}
.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #fa4841;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-top: 20px;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
