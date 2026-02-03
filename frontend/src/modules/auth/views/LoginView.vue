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
                    ¿No tienes cuenta? <a href="/register" style="color: #fa4841; font-weight: bold;">Regístrate
                        aquí.</a>
                </p>

                <p v-if="error" class="error">{{ error }}</p>
            </form>
        </div>
    </div>
</template>

<style>
/* Global styles for auth page */
body {
    background-color: #eaf2f2;
}

.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 20px;
}

.login-card {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(14, 39, 63, 0.15);
    width: 100%;
    max-width: 400px;
}

.login-card h2 {
    text-align: center;
    color: #0e273f;
    margin-top: 0;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #0e273f;
    font-weight: bold;
    font-size: 0.9rem;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

/* SPECIFIC FIX FOR CHECKBOX */
.checkbox-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    /* Reset width for checkbox */
    margin-right: 10px;
    flex-shrink: 0;
    /* Prevent shrinking */
}

.checkbox-group label {
    margin-bottom: 0;
    /* Align perfectly with checkbox */
}

.form-group input:focus {
    border-color: #0e273f;
    outline: none;
}

button[type="submit"] {
    width: 100%;
    padding: 14px;
    background-color: #fa4841;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 10px;
}

button:hover {
    background-color: #d63a34;
}

.error {
    color: red;
    text-align: center;
    margin-top: 15px;
}
</style>
