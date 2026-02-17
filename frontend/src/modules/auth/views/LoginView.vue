<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const error = ref(null);
const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
    try {
        await authStore.login({ email: email.value, password: password.value });
        router.push('/');
    } catch (err) {
        console.error(err);
        error.value = err.response?.data?.message || err.message || "Invalid credentials";
    }
};
</script>

<template>
    <div class="login-container">
        <div class="login-card">
            <h2>Iniciar Sesión</h2>
            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" v-model="email" placeholder="tu@email.com" required />
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" v-model="password" placeholder="Tu contraseña" required />
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember" />
                    <label for="remember"
                        style="display:inline; margin-left: 5px; font-weight: normal;">Recordarme</label>
                </div>

                <button type="submit">Entrar</button>

                <p class="register-link">
                    ¿No tienes cuenta? <RouterLink to="/register" style="color: #fa4841; font-weight: bold;">Regístrate
                        aquí.</RouterLink>
                </p>

                <p v-if="error" class="error">{{ error }}</p>
            </form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/login.css';
</style>
