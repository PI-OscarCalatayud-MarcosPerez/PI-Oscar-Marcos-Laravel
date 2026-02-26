<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import http from '../services/http';
import CategoryService from '../modules/products/services/CategoryService';
import { usePrefsStore } from '../stores/prefsStore';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';

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

const imagenRef = ref(null);
const newProductName = ref('');

const schema = computed(() => {
    return yup.object({
        codigo: yup.string().required(t.value?.sell?.codeRequired || 'El código es obligatorio'),
        ...(isNewProduct.value ? {
            descripcion: yup.string().required(t.value?.sell?.descRequired || 'La descripción es obligatoria'),
            precio: yup.number()
                .typeError('El precio debe ser un número')
                .required(t.value?.sell?.priceRequired || 'El precio es obligatorio')
                .positive('El precio debe ser mayor a 0')
                .min(1.01, 'El mínimo es 1.01'),
            plataforma: yup.string().required('La plataforma es obligatoria'),
            category_id: yup.number().nullable().notRequired(),
            seccion: yup.string().nullable().notRequired(),
        } : {})
    });
});

const initialValues = computed(() => {
    if (selectedExistingProduct.value) {
        return {
            codigo: '',
        };
    }
    return {
        descripcion: '',
        precio: '',
        plataforma: 'PC',
        category_id: null,
        seccion: '',
        codigo: ''
    };
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
    searchQuery.value = '';
    isNewProduct.value = false;
};

const clearSelection = () => {
    selectedExistingProduct.value = null;
    isNewProduct.value = false;
    newProductName.value = '';
};

const setNewProduct = () => {
    selectedExistingProduct.value = null;
    isNewProduct.value = true;
    newProductName.value = searchQuery.value;
    searchQuery.value = '';
};

const handleFileUpload = (event) => {
    imagenRef.value = event.target.files[0];
};

const submitForm = async (values) => {
    loading.value = true;
    error.value = null;
    success.value = false;

    try {
        const formData = new FormData();
        formData.append('codigo', values.codigo);

        if (isNewProduct.value) {
            formData.append('nombre', newProductName.value);
            formData.append('descripcion', values.descripcion);
            formData.append('precio', values.precio);
            formData.append('plataforma', values.plataforma || 'PC');

            if (values.category_id) {
                formData.append('category_id', values.category_id);
            }
            formData.append('seccion', values.seccion || '');

            if (imagenRef.value) {
                formData.append('imagen', imagenRef.value);
            }
        } else {
            formData.append('nombre', selectedExistingProduct.value.nombre);
            formData.append('descripcion', selectedExistingProduct.value.descripcion || '');
            formData.append('precio', selectedExistingProduct.value.precio);
            formData.append('plataforma', selectedExistingProduct.value.plataforma || 'PC');
            if (selectedExistingProduct.value.category_id) {
                formData.append('category_id', selectedExistingProduct.value.category_id);
            }
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

            <Form @submit="submitForm" :validation-schema="schema" :initial-values="initialValues"
                v-slot="{ isSubmitting, errors }">
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
                        <button type="button" class="btn-cambiar" @click="clearSelection">{{ t.sell.changeProduct
                        }}</button>
                    </div>
                </div>

                <!-- Producto nuevo: campos adicionales -->
                <div v-if="isNewProduct" class="producto-nuevo-header">
                    <div class="producto-sel-header">
                        <div class="producto-sel-info">
                            <span class="badge-nuevo">{{ t.sell.new }}</span>
                            <h3>{{ newProductName }}</h3>
                        </div>
                        <button type="button" class="btn-cambiar" @click="clearSelection">{{ t.sell.changeProduct
                        }}</button>
                    </div>
                </div>

                <!-- Campos de producto nuevo -->
                <template v-if="isNewProduct">
                    <div class="grupo-input">
                        <label>{{ t.sell.descLabel }}</label>
                        <Field name="descripcion" as="textarea" rows="3" :placeholder="t.sell.descLabel"
                            :class="{ 'is-invalid': errors.descripcion }" />
                        <ErrorMessage name="descripcion" class="error-text"
                            style="color: #fa4841; font-size: 0.85rem;" />
                    </div>

                    <div class="form-row">
                        <div class="grupo-input">
                            <label>{{ t.sell.priceLabel }}</label>
                            <Field name="precio" type="number" step="0.01" min="0" :placeholder="t.sell.priceLabel"
                                :class="{ 'is-invalid': errors.precio }" />
                            <ErrorMessage name="precio" class="error-text"
                                style="color: #fa4841; font-size: 0.85rem;" />
                        </div>
                        <div class="grupo-input">
                            <label>{{ t.sell.platformLabel }}</label>
                            <Field name="plataforma" as="select" :class="{ 'is-invalid': errors.plataforma }">
                                <option value="PC">PC</option>
                                <option value="PS5">PS5</option>
                                <option value="Xbox">Xbox</option>
                                <option value="Switch">Switch</option>
                                <option value="Web">Web</option>
                            </Field>
                            <ErrorMessage name="plataforma" class="error-text"
                                style="color: #fa4841; font-size: 0.85rem;" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input">
                            <label>Categoría</label>
                            <Field name="category_id" as="select">
                                <option :value="null">Seleccionar...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </Field>
                        </div>
                        <div class="grupo-input">
                            <label>Sección</label>
                            <Field name="seccion" as="select">
                                <option value="">Seleccionar...</option>
                                <option value="RPG">RPG</option>
                                <option value="Acción">Acción</option>
                                <option value="Aventura">Aventura</option>
                                <option value="Deportes">Deportes</option>
                                <option value="Estrategia">Estrategia</option>
                                <option value="comprados">Destacado</option>
                                <option value="software">Software</option>
                            </Field>
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
                    <Field name="codigo" type="text" :placeholder="t.sell.codePlaceholder" class="input-codigo"
                        :class="{ 'is-invalid': errors.codigo }" />
                    <small class="codigo-ayuda">{{ t.sell.codeHelp }}</small>
                    <ErrorMessage name="codigo" class="error-text"
                        style="display: block; margin-top: 5px; color: #fa4841; font-size: 0.85rem;" />
                </div>

                <!-- Botón enviar -->
                <button v-if="selectedExistingProduct || isNewProduct" type="submit" class="btn-enviar"
                    :disabled="loading || isSubmitting">
                    {{ loading || isSubmitting ? t.sell.processing : t.sell.sellBtn }}
                </button>
            </Form>
        </div>
    </div>
</template>

<style>
@import '../assets/css/sell.css';
</style>
