<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

const error = ref(null);
const authStore = useAuthStore();
const router = useRouter();

// Validation Schema
const schema = yup.object({
    name: yup.string().required('El nombre es obligatorio'),
    last_name: yup.string(),
    email: yup.string().email('Email inválido').required('El email es obligatorio'),
    password: yup.string().required('Contraseña obligatoria').min(8, 'Mínimo 8 caracteres'),
    password_confirmation: yup.string()
        .oneOf([yup.ref('password'), null], 'Las contraseñas no coinciden')
        .required('Confirma tu contraseña')
})

const handleRegister = async (values) => {
    try {
        error.value = null; // Clear previous errors
        await authStore.register(values);
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

            <Form @submit="handleRegister" :validation-schema="schema" class="login-form" v-slot="{ errors, isSubmitting }">
                <div class="form-group">
                    <label>Nombre:</label>
                    <Field name="name" type="text" placeholder="Tu nombre" :class="{ 'is-invalid': errors.name }" />
                    <ErrorMessage name="name" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>Apellidos:</label>
                    <Field name="last_name" type="text" placeholder="Tus apellidos" />
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <Field name="email" type="email" placeholder="tucorreo@ejemplo.com" :class="{ 'is-invalid': errors.email }" />
                    <ErrorMessage name="email" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>Contraseña:</label>
                    <Field name="password" type="password" placeholder="Mínimo 8 caracteres" :class="{ 'is-invalid': errors.password }" />
                    <ErrorMessage name="password" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña:</label>
                    <Field name="password_confirmation" type="password" placeholder="Repite tu contraseña" :class="{ 'is-invalid': errors.password_confirmation }" />
                    <ErrorMessage name="password_confirmation" class="error-feedback" />
                </div>

                <button type="submit" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Registrando...' : 'Registrarse' }}
                </button>

                <p class="error" v-if="error" style="white-space: pre-wrap;">{{ error }}</p>

                <p class="register-link">
                    ¿Ya tienes cuenta? <RouterLink to="/login" style="color: #fa4841; font-weight: bold;">Inicia sesión
                        aquí</RouterLink>.
                </p>
            </Form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/register.css';
</style>
