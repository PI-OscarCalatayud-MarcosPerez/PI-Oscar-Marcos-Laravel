<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { usePrefsStore } from '../../../stores/prefsStore'

const error = ref(null);
const authStore = useAuthStore();
const router = useRouter();
const prefsStore = usePrefsStore()
const t = computed(() => prefsStore.t)

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
            <h2>{{ t.auth.registerTitle }}</h2>
            <p class="subtitulo-form">{{ t.about.joinDesc }}</p>

            <Form @submit="handleRegister" :validation-schema="schema" class="login-form" v-slot="{ errors, isSubmitting }">
                <div class="form-group">
                    <label>{{ t.auth.name }}:</label>
                    <Field name="name" type="text" :placeholder="t.auth.name" :class="{ 'is-invalid': errors.name }" />
                    <ErrorMessage name="name" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>{{ t.auth.lastName }}:</label>
                    <Field name="last_name" type="text" :placeholder="t.auth.lastName" />
                </div>

                <div class="form-group">
                    <label>{{ t.auth.email }}:</label>
                    <Field name="email" type="email" placeholder="tucorreo@ejemplo.com" :class="{ 'is-invalid': errors.email }" />
                    <ErrorMessage name="email" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>{{ t.auth.password }}:</label>
                    <Field name="password" type="password" :placeholder="t.auth.password" :class="{ 'is-invalid': errors.password }" />
                    <ErrorMessage name="password" class="error-feedback" />
                </div>

                <div class="form-group">
                    <label>{{ t.auth.confirmPassword }}:</label>
                    <Field name="password_confirmation" type="password" :placeholder="t.auth.confirmPassword" :class="{ 'is-invalid': errors.password_confirmation }" />
                    <ErrorMessage name="password_confirmation" class="error-feedback" />
                </div>

                <button type="submit" :disabled="isSubmitting">
                    {{ isSubmitting ? t.cart.processing : t.auth.registerBtn }}
                </button>

                <p class="error" v-if="error" style="white-space: pre-wrap;">{{ error }}</p>

                <p class="register-link">
                    {{ t.auth.haveAccount }} <RouterLink to="/login" style="color: #fa4841; font-weight: bold;">{{ t.auth.loginHere }}</RouterLink>.
                </p>
            </Form>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/register.css';
</style>
