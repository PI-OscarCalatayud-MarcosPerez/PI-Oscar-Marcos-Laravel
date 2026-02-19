<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import http from '../services/http'; // Usamos la instancia configurada con interceptores

const router = useRouter();
const loading = ref(false);
const error = ref(null);
const success = ref(false);

const form = ref({
    nombre: '',
    descripcion: '',
    precio: '',
    stock: '',
    categoria: '',
    seccion: '',
    imagen: null
});

const handleFileUpload = (event) => {
    form.value.imagen = event.target.files[0];
};

const submitForm = async () => {
    loading.value = true;
    error.value = null;
    success.value = false;

    try {
        const formData = new FormData();
        formData.append('nombre', form.value.nombre);
        formData.append('descripcion', form.value.descripcion);
        formData.append('precio', form.value.precio);
        formData.append('stock', form.value.stock);
        formData.append('categoria', form.value.categoria);
        formData.append('seccion', form.value.seccion || ''); // Opcional

        if (form.value.imagen) {
            formData.append('imagen', form.value.imagen);
        }

        await http.post('/api/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        success.value = true;
        setTimeout(() => {
            router.push('/products');
        }, 2000);

    } catch (err) {
        console.error(err);
        if (err.response && err.response.status === 401) {
            error.value = "Tu sesión ha expirado. Por favor, inicia sesión nuevamente.";
            // Opcional: router.push('/login');
        } else {
            error.value = err.response?.data?.message || "Error al crear el producto.";
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="container my-5">
        <h2 class="text-center mb-4">Vender Producto</h2>

        <div v-if="success" class="alert alert-success">
            Producto creado exitosamente. Redirigiendo...
        </div>

        <div v-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <form @submit.prevent="submitForm" class="card p-4 shadow-sm mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <input type="text" id="nombre" v-model="form.nombre" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" v-model="form.descripcion" class="form-control" rows="3" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="precio" class="form-label">Precio (€)</label>
                    <input type="number" id="precio" v-model="form.precio" class="form-control" step="0.01" min="0"
                        required />
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" id="stock" v-model="form.stock" class="form-control" min="0" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select id="categoria" v-model="form.categoria" class="form-select">
                        <option value="">Seleccionar...</option>
                        <option value="videojuegos">Videojuegos</option>
                        <option value="consolas">Consolas</option>
                        <option value="accesorios">Accesorios</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="seccion" class="form-label">Sección (Opcional)</label>
                    <input type="text" id="seccion" v-model="form.seccion" class="form-control"
                        placeholder="Ej. RPG, Acción" />
                </div>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto</label>
                <input type="file" id="imagen" @change="handleFileUpload" class="form-control" accept="image/*" />
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                {{ loading ? 'Publicando...' : 'Publicar Producto' }}
            </button>
        </form>
    </div>
</template>

<style>
@import '../assets/css/sell.css';
</style>
