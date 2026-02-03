<script setup>
import { ref } from 'vue';

const form = ref({
  nombre: '',
  correo: '',
  asunto: '',
  telefono: '',
  mensaje: '',
  consentimiento: false
});

const errors = ref({});

const validate = () => {
  errors.value = {};
  let isValid = true;

  if (!form.value.nombre) {
    errors.value.nombre = "Pon tu nombre.";
    isValid = false;
  }
  
  if (!form.value.correo || !form.value.correo.includes('@')) {
    errors.value.correo = "Correo mal.";
    isValid = false;
  }

  if (!form.value.asunto) {
    errors.value.asunto = "Elige asunto.";
    isValid = false;
  }

  if (form.value.telefono && isNaN(form.value.telefono)) {
    errors.value.telefono = "Solo números.";
    isValid = false;
  }

  if (!form.value.consentimiento) {
    errors.value.consentimiento = "Acepta los datos.";
    isValid = false;
  }

  return isValid;
};

const handleSubmit = () => {
  if (validate()) {
    alert("Formulario enviado correctamente (Simulado)");
    // In strict SPA, we would send this to an API endpoint
  }
};
</script>

<template>
  <main class="contenedor-formulario-principal" id="main-content">
      <div class="caja-formulario">
        <h1>Contacta con nosotros</h1>
        <p class="subtitulo-form">¿Tienes dudas con tu clave? Envíanos un mensaje.</p>
        
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
                <textarea id="mensaje" v-model="form.mensaje" rows="5" required placeholder="Escribe aquí tu consulta..."></textarea>
            </div>
            <div class="grupo-checkbox">
                <label>
                    <input type="checkbox" id="consentimiento" v-model="form.consentimiento" />
                    Acepto el tratamiento de los datos
                </label>
                <span class="error" id="error-consentimiento">{{ errors.consentimiento }}</span>
            </div>
            <button type="submit" class="btn-enviar">Enviar Mensaje</button>
        </form>
      </div>
    </main>
    
    <div class="container-fluid breadcrumb-container">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><RouterLink to="/" style="color:#fa4841; text-decoration:none;">Inicio</RouterLink></li>
                    <li class="breadcrumb-item active" aria-current="page">Contacto</li>
                </ol>
            </nav>
        </div>
    </div>
</template>

<style scoped>
@import '@/assets/css/estilos.css'; 
/* We import estilos.css here because it's specific to the contact page */
</style>
