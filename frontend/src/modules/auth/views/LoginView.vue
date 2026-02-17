<script setup>
import { reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useUiStore } from '@/stores/uiStore'
import UiToast from '@/components/UiToast.vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()
const route = useRoute()

const form = reactive({ email: '', password: '' })

async function handleLogin() {
    try {
        await auth.login(form)
        ui.showToast('success', 'Sesión iniciada')
        router.push(route.query.redirect || '/')
    } catch (err) {
        ui.showToast('error', 'Credenciales incorrectas')
    }
}
</script>

<template>
    <UiToast />
    <div class="login-container">
        <div class="login-card">
            <h2>Iniciar Sesión</h2>
            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" v-model="form.email" placeholder="tu@email.com" required />
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" v-model="form.password" placeholder="Tu contraseña" required />
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember" />
                    <label for="remember"
                        style="display:inline; margin-left: 5px; font-weight: normal;">Recordarme</label>
                </div>

                <button type="submit">Entrar</button>

                <p class="register-link">
                    ¿No tienes cuenta?
                    <RouterLink to="/register" style="color: #fa4841; font-weight: bold;">Regístrate aquí.</RouterLink>
                </p>
            </form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/login.css';
</style>
