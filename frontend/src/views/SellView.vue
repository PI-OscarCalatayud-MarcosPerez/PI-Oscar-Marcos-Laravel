<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import http from '../services/http';
import CategoryService from '../modules/products/services/CategoryService';
import { usePrefsStore } from '../stores/prefsStore';

const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);

const router = useRouter();
const loading = ref(false);
const error = ref(null);
const success = ref(false);
const successMessage = ref('');
const categories = ref([]);
const allProducts = ref([]);
const searchQuery = ref('');
const selectedExistingProduct = ref(null);
const isNewProduct = ref(false);

const form = ref({
    nombre: '',
    descripcion: '',
    precio: '',
    codigo: '',
    category_id: null,
    seccion: '',
    plataforma: 'PC',
    imagen: null
});

// Productos filtrados por búsqueda
const filteredProducts = computed(() => {
    if (!searchQuery.value || searchQuery.value.length < 2) return [];
    const q = searchQuery.value.toLowerCase();
    return allProducts.value.filter(p => p.nombre.toLowerCase().includes(q));
});

const fetchCategories = async () => {
    try {
        const response = await CategoryService.getCategories();
        categories.value = response.data;
    } catch (err) {
        console.error("Error fetching categories:", err);
    }
};

const fetchProducts = async () => {
    try {
        const response = await http.get('/api/products?per_page=100');
        allProducts.value = response.data.data || [];
    } catch (err) {
        console.error("Error fetching products:", err);
    }
};

const selectExistingProduct = (product) => {
    selectedExistingProduct.value = product;
    form.value.nombre = product.nombre;
    form.value.descripcion = product.descripcion || '';
    form.value.precio = product.precio;
    form.value.plataforma = product.plataforma || 'PC';
    form.value.category_id = product.category_id;
    searchQuery.value = '';
    isNewProduct.value = false;
};

const clearSelection = () => {
    selectedExistingProduct.value = null;
    form.value.nombre = '';
    form.value.descripcion = '';
    form.value.precio = '';
    form.value.plataforma = 'PC';
    form.value.category_id = null;
    isNewProduct.value = false;
};

const setNewProduct = () => {
    selectedExistingProduct.value = null;
    form.value.nombre = searchQuery.value;
    isNewProduct.value = true;
    searchQuery.value = '';
};

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
        formData.append('codigo', form.value.codigo);
        formData.append('plataforma', form.value.plataforma || 'PC');

        if (form.value.category_id) {
            formData.append('category_id', form.value.category_id);
        }
        formData.append('seccion', form.value.seccion || '');

        if (form.value.imagen) {
            formData.append('imagen', form.value.imagen);
        }

        const response = await http.post('/api/products', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        success.value = true;
        successMessage.value = response.data.message || 'Producto publicado correctamente';
        setTimeout(() => {
            router.push('/products');
        }, 2500);

    } catch (err) {
        console.error(err);
        if (err.response && err.response.status === 401) {
            error.value = "Tu sesión ha expirado. Por favor, inicia sesión nuevamente.";
        } else if (err.response?.data?.errors) {
            const errors = err.response.data.errors;
            error.value = Object.values(errors).flat().join(', ');
        } else {
            error.value = err.response?.data?.message || "Error al crear el producto.";
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchCategories();
    fetchProducts();
});
</script>

<template>
    <div class="contenedor-formulario-principal">
        <div class="caja-formulario-vender">
            <h1>{{ t.sell.title }}</h1>
            <p class="subtitulo-form">{{ t.sell.searchLabel }}</p>

            <div v-if="success" class="alerta-exito">
                ✅ {{ successMessage }}
            </div>

            <div v-if="error" class="alerta-error">
                ❌ {{ error }}
            </div>

            <form @submit.prevent="submitForm">
                <!-- Buscar producto existente o crear nuevo -->
                <div class="grupo-input" v-if="!selectedExistingProduct && !isNewProduct">
                    <label>{{ t.sell.searchLabel }}</label>
                    <input type="text" v-model="searchQuery" :placeholder="t.sell.searchPlaceholder"
                        autocomplete="off" />

                    <!-- Resultados de búsqueda -->
                    <div v-if="filteredProducts.length > 0" class="search-results">
                        <div v-for="product in filteredProducts" :key="product.id" class="search-result-item"
                            @click="selectExistingProduct(product)">
                            <img v-if="product.imagen_url" :src="product.imagen_url" :alt="product.nombre"
                                class="search-thumb" />
                            <div class="search-info">
                                <strong>{{ product.nombre }}</strong>
                                <span>{{ product.plataforma || 'PC' }} · {{ product.precio }}€ · Stock: {{
                                    product.stock }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Opción de crear nuevo si hay búsqueda -->
                    <div v-if="searchQuery.length >= 2 && filteredProducts.length === 0" class="nuevo-producto-hint">
                        <p>{{ t.sell.newProductHint }}</p>
                        <button type="button" class="btn-nuevo" @click="setNewProduct">
                            {{ t.sell.newProductBtn }} "{{ searchQuery }}"
                        </button>
                    </div>

                    <div v-if="searchQuery.length >= 2 && filteredProducts.length > 0" class="nuevo-producto-hint">
                        <button type="button" class="btn-nuevo-alt" @click="setNewProduct">
                            {{ t.sell.newProductHint }} "{{ searchQuery }}" {{ t.sell.new }}
                        </button>
                    </div>
                </div>

                <!-- Producto seleccionado (existente) -->
                <div v-if="selectedExistingProduct" class="producto-seleccionado">
                    <div class="producto-sel-header">
                        <div class="producto-sel-info">
                            <span class="badge-existente">{{ t.sell.existing }}</span>
                            <h3>{{ selectedExistingProduct.nombre }}</h3>
                            <p>{{ selectedExistingProduct.plataforma || 'PC' }} · {{
                                selectedExistingProduct.precio }}€ · Stock actual: {{ selectedExistingProduct.stock }}
                            </p>
                        </div>
                        <button type="button" class="btn-cambiar" @click="clearSelection">{{ t.sell.changeProduct }}</button>
                    </div>
                </div>

                <!-- Producto nuevo: campos adicionales -->
                <div v-if="isNewProduct" class="producto-nuevo-header">
                    <div class="producto-sel-header">
                        <div class="producto-sel-info">
                            <span class="badge-nuevo">{{ t.sell.new }}</span>
                            <h3>{{ form.nombre }}</h3>
                        </div>
                        <button type="button" class="btn-cambiar" @click="clearSelection">{{ t.sell.changeProduct }}</button>
                    </div>
                </div>

                <!-- Campos de producto nuevo -->
                <template v-if="isNewProduct">
                    <div class="grupo-input">
                        <label>{{ t.sell.descLabel }}</label>
                        <textarea v-model="form.descripcion" rows="3" required
                            :placeholder="t.sell.descLabel"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input">
                            <label>{{ t.sell.priceLabel }}</label>
                            <input type="number" v-model="form.precio" step="0.01" min="0" required />
                        </div>
                        <div class="grupo-input">
                            <label>{{ t.sell.platformLabel }}</label>
                            <select v-model="form.plataforma">
                                <option value="PC">PC</option>
                                <option value="PS5">PS5</option>
                                <option value="Xbox">Xbox</option>
                                <option value="Switch">Switch</option>
                                <option value="Web">Web</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input">
                            <label>Categoría</label>
                            <select v-model="form.category_id">
                                <option :value="null">Seleccionar...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                        <div class="grupo-input">
                            <label>Sección</label>
                            <select v-model="form.seccion">
                                <option value="">Seleccionar...</option>
                                <option value="RPG">RPG</option>
                                <option value="Acción">Acción</option>
                                <option value="Aventura">Aventura</option>
                                <option value="Deportes">Deportes</option>
                                <option value="Estrategia">Estrategia</option>
                                <option value="comprados">Destacado</option>
                                <option value="software">Software</option>
                            </select>
                        </div>
                    </div>

                    <div class="grupo-input">
                        <label>Imagen del Producto</label>
                        <input type="file" @change="handleFileUpload" accept="image/*" />
                    </div>
                </template>

                <!-- Campo de código (siempre visible cuando hay producto seleccionado) -->
                <div v-if="selectedExistingProduct || isNewProduct" class="grupo-input codigo-input">
                    <label>🔑 {{ t.sell.codeLabel }}</label>
                    <input type="text" v-model="form.codigo" required :placeholder="t.sell.codePlaceholder"
                        class="input-codigo" />
                    <small class="codigo-ayuda">{{ t.sell.codeHelp }}</small>
                </div>

                <!-- Botón enviar -->
                <button v-if="selectedExistingProduct || isNewProduct" type="submit" class="btn-enviar"
                    :disabled="loading">
                    {{ loading ? t.sell.processing : t.sell.sellBtn }}
                </button>
            </form>
        </div>
    </div>
</template>

<style>
@import '../assets/css/sell.css';
</style>
