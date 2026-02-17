<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';

const form = ref({
    name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const error = ref(null);
const authStore = useAuthStore();
const router = useRouter();

const handleRegister = async () => {
    try {
        error.value = null; // Clear previous errors
        await authStore.register(form.value);
        router.push('/');
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            // Join all error messages
            error.value = Object.values(err.response.data.errors).flat().join('\n');
        } else {
            // DEBUG MODE: Show full error to understand what's happening
            console.error(err);
            error.value = "Error: " + (err.response?.status || 'Unknown') + " - " + (err.response?.data?.message || JSON.stringify(err.response?.data) || err.message);
        }
    }
};
</script>

<template>
    <div class="login-container">
        <div class="login-card">
            <h2>Crear Cuenta</h2>
            <p class="subtitulo-form">Únete a MOKeys y empieza a ahorrar.</p>

            <form @submit.prevent="handleRegister" class="login-form">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" v-model="form.name" placeholder="Tu nombre" required autofocus />
                </div>

                <div class="form-group">
                    <label>Apellidos:</label>
                    <input type="text" v-model="form.last_name" placeholder="Tus apellidos" />
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" v-model="form.email" placeholder="tucorreo@ejemplo.com" required />
                </div>

                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" v-model="form.password" placeholder="Mínimo 8 caracteres" required />
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña:</label>
                    <input type="password" v-model="form.password_confirmation" placeholder="Repite tu contraseña"
                        required />
                </div>

                <button type="submit">Registrarse</button>

                <p class="error" v-if="error" style="white-space: pre-wrap;">{{ error }}</p>

                <p class="register-link">
                    ¿Ya tienes cuenta? <RouterLink to="/login" style="color: #fa4841; font-weight: bold;">Inicia sesión
                        aquí</RouterLink>.
                </p>
            </form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/register.css';
</style>
