<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useUiStore } from '@/stores/uiStore'
import UiToast from '@/components/UiToast.vue'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { onMounted } from 'vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()
const route = useRoute()

// Validation Schema
const schema = yup.object({
    email: yup.string().email('Email inválido').required('El email es obligatorio'),
    password: yup.string().required('La contraseña es obligatoria').min(6, 'Mínimo 6 caracteres')
})

onMounted(() => {
    if (auth.isAuthenticated) {
        router.push('/')
    }
})

async function handleLogin(values) {
    try {
        await auth.login(values)
        ui.showToast('success', 'Sesión iniciada')
        router.push(route.query.redirect || '/profile')
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
            <Form @submit="handleLogin" :validation-schema="schema" class="login-form" v-slot="{ errors, isSubmitting }">
                <div class="form-group">
                    <label>Email:</label>
                    <Field name="email" type="email" placeholder="tu@email.com" :class="{ 'is-invalid': errors.email }" />
                    <ErrorMessage name="email" class="error-feedback" />
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <Field name="password" type="password" placeholder="Tu contraseña"
                        :class="{ 'is-invalid': errors.password }" />
                    <ErrorMessage name="password" class="error-feedback" />
                </div>

                <div class="form-group checkbox-group">
                    <Field name="remember" type="checkbox" :value="true" id="remember" />
                    <label for="remember"
                        style="display:inline; margin-left: 5px; font-weight: normal;">Recordarme</label>
                </div>

                <button type="submit" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Entrando...' : 'Entrar' }}
                </button>

                <!-- Google Login Placeholder (C1) -->
                <div class="google-login mt-3">
                    <a href="http://localhost:8000/api/auth/google/redirect" class="btn-google">
                        <img src="/img/google_logo.svg" alt="Google" style="width:20px;vertical-align:middle;margin-right:8px;">
                        Iniciar con Google
                    </a>
                </div>

                <p class="register-link">
                    ¿No tienes cuenta?
                    <RouterLink to="/register" style="color: #fa4841; font-weight: bold;">Regístrate aquí.</RouterLink>
                </p>
            </Form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/login.css';
</style>
