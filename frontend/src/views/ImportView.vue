<script setup>
import { ref } from 'vue';
import http from '@/services/http';

const file = ref(null);
const isLoading = ref(false);
const result = ref(null);
const error = ref(null);

const handleFileUpload = (event) => {
    file.value = event.target.files[0];
    result.value = null;
    error.value = null;
};

const submitFile = async () => {
    if (!file.value) return;

    isLoading.value = true;
    const formData = new FormData();
    formData.append('arxiuCsv', file.value);

    try {
        const response = await http.post('/api/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        result.value = response.data;
    } catch (err) {
        console.error(err);
        if (err.response && err.response.data) {
            error.value = err.response.data.message || 'Error al subir el archivo.';
            if (err.response.data.errors) {
                result.value = { errors: Object.values(err.response.data.errors).flat() };
            }
        } else {
            error.value = 'Ocurrió un error inesperado.';
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Importación de Productos (CSV)</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Sube un archivo CSV para actualizar el catálogo.
                            <br>
                            <small>Columnas requeridas: id, nombre, descripcion, precio, img, estoc, categoria</small>
                        </p>

                        <div class="mb-4">
                            <label for="csvFile" class="form-label">Seleccionar archivo</label>
                            <input type="file" class="form-control" id="csvFile" accept=".csv"
                                @change="handleFileUpload">
                        </div>

                        <button class="btn btn-success w-100" @click="submitFile" :disabled="!file || isLoading">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            {{ isLoading ? 'Procesando...' : 'Importar Catálogo' }}
                        </button>

                        <!-- Resultados -->
                        <div v-if="error" class="alert alert-danger mt-4">
                            {{ error }}
                        </div>

                        <div v-if="result" class="mt-4">
                            <!-- Mensajes de éxito -->
                            <div v-if="result.messages && result.messages.length" class="alert alert-success">
                                <ul class="mb-0">
                                    <li v-for="(msg, index) in result.messages" :key="index">{{ msg }}</li>
                                </ul>
                            </div>

                            <!-- Errores del CSV -->
                            <div v-if="result.errors && result.errors.length" class="alert alert-danger">
                                <strong>Errores Críticos:</strong>
                                <ul class="mb-0">
                                    <li v-for="(err, index) in result.errors" :key="index">{{ err }}</li>
                                </ul>
                            </div>

                            <!-- Advertencias por fila -->
                            <div v-if="result.row_warnings && result.row_warnings.length" class="alert alert-warning">
                                <strong>Advertencias (Filas ignoradas):</strong>
                                <ul class="mb-0" style="max-height: 200px; overflow-y: auto;">
                                    <li v-for="(warn, index) in result.row_warnings" :key="index">{{ warn }}</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@import '../assets/css/import.css';
</style>
