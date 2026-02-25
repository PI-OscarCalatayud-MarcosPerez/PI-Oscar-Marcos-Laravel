<script setup>
import { ref } from 'vue';
import http from '@/services/http.js';


const form = ref({
    nombre: '',
    correo: '',
    asunto: '',
    telefono: '',
    mensaje: '',
    consentimiento: false
});

const errors = ref({});
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const validate = () => {
    errors.value = {};
    let isValid = true;

    if (!form.value.nombre) {
        errors.value.nombre = "Pon tu nombre.";
        isValid = false;
    }

    if (!form.value.correo || !form.value.correo.includes('@')) {
        errors.value.correo = "Correo no válido.";
        isValid = false;
    }

    if (!form.value.asunto) {
        errors.value.asunto = "Elige un asunto.";
        isValid = false;
    }

    if (form.value.telefono && isNaN(form.value.telefono)) {
        errors.value.telefono = "Solo números.";
        isValid = false;
    }

    if (!form.value.mensaje) {
        errors.value.mensaje = "Escribe tu consulta.";
        isValid = false;
    }

    if (!form.value.consentimiento) {
        errors.value.consentimiento = "Debes aceptar el tratamiento de datos.";
        isValid = false;
    }

    return isValid;
};

const resetForm = () => {
    form.value = {
        nombre: '',
        correo: '',
        asunto: '',
        telefono: '',
        mensaje: '',
        consentimiento: false
    };
    errors.value = {};
};

const handleSubmit = async () => {
    successMessage.value = '';
    errorMessage.value = '';

    if (!validate()) return;

    loading.value = true;

    // Construir el motivo combinando asunto + teléfono + mensaje
    const asuntoTexto = form.value.asunto.charAt(0).toUpperCase() + form.value.asunto.slice(1);
    let motivo = `[${asuntoTexto}] ${form.value.mensaje}`;
    if (form.value.telefono) {
        motivo += ` (Tel: ${form.value.telefono})`;
    }

    try {
        const response = await http.post('/api/contacto', {
            nombre: form.value.nombre,
            email: form.value.correo,
            motivo: motivo
        });

        if (response.data.success) {
            successMessage.value = '✅ Tu incidencia ha sido registrada correctamente. Te enviaremos un correo de confirmación.';
            resetForm();
        } else {
            errorMessage.value = '❌ Hubo un error al enviar tu mensaje. Inténtalo de nuevo.';
        }
    } catch (err) {
        errorMessage.value = '❌ No se pudo conectar con el servidor. Comprueba que el servicio esté activo.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <main class="contenedor-formulario-principal" id="main-content">
        <div class="caja-formulario">
            <h1>Contacta con nosotros</h1>
            <p class="subtitulo-form">¿Tienes dudas con tu clave? Envíanos un mensaje.</p>

            <!-- Mensajes de feedback -->
            <div v-if="successMessage" class="mensaje-exito">{{ successMessage }}</div>
            <div v-if="errorMessage" class="mensaje-error">{{ errorMessage }}</div>

            <form @submit.prevent="handleSubmit" id="formulario">
                <div class="grupo-input">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" v-model="form.nombre" placeholder="Tu nombre completo" />
                    <span class="error" id="error-nom">{{ errors.nombre }}</span>
                </div>
                <div class="grupo-input">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" v-model="form.correo" placeholder="tucorreo@email.com" />
                    <span class="error" id="error-correu">{{ errors.correo }}</span>
                </div>
                <div class="grupo-input">
                    <label for="asunto">Asunto:</label>
                    <select id="asunto" v-model="form.asunto">
                        <option value="">Seleccione un asunto</option>
                        <option value="informacion">Información General</option>
                        <option value="soporte">Soporte Técnico</option>
                        <option value="ventas">Ventas</option>
                    </select>
                    <span class="error" id="error-cicle">{{ errors.asunto }}</span>
                </div>
                <div class="grupo-input">
                    <label for="Telefono">Teléfono:</label>
                    <input type="tel" id="Telefono" v-model="form.telefono" placeholder="+34 600 000 000" />
                    <span class="error" id="error-telefono">{{ errors.telefono }}</span>
                </div>
                <div class="grupo-input">
                    <label for="mensaje">Mensaje / Consulta:</label>
                    <textarea id="mensaje" v-model="form.mensaje" rows="5"
                        placeholder="Escribe aquí tu consulta..."></textarea>
                    <span class="error">{{ errors.mensaje }}</span>
                </div>
                <div class="grupo-checkbox">
                    <label>
                        <input type="checkbox" id="consentimiento" v-model="form.consentimiento" />
                        Acepto el tratamiento de los datos
                    </label>
                    <span class="error" id="error-consentimiento">{{ errors.consentimiento }}</span>
                </div>
                <button type="submit" class="btn-enviar" :disabled="loading">
                    <span v-if="loading">Enviando...</span>
                    <span v-else>Enviar Mensaje</span>
                </button>
            </form>
        </div>
    </main>
</template>

<style>
@import '@/assets/css/contact.css';
</style>
