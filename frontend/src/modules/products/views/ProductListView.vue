<script setup>
import { ref, onMounted } from 'vue';
import ProductService from '../services/ProductService';
import { useRouter } from 'vue-router';

const router = useRouter();
const products = ref([]);
const loading = ref(true);

const fetchProducts = async () => {
    try {
        const response = await ProductService.getProducts();
        const data = response.data;
        products.value = Array.isArray(data) ? data : (data.products || data.data || []);
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const getImage = (product) => {
    let img = product.imagen_url || product.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('/')) {
        img = '/' + img;
    }
    return img;
};

const goToProduct = (id) => {
    router.push(`/products/${id}`);
};

onMounted(() => {
    fetchProducts();
});
</script>

<template>
    <div class="container my-5">
        <h1 class="text-center mb-4">Catálogo de Productos</h1>
        
        <div v-if="loading" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

        <div v-else class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <div v-for="product in products" :key="product.id" class="col">
                <div class="card h-100 shadow-sm item-card" @click="goToProduct(product.id)">
                    <img :src="getImage(product)" class="card-img-top" :alt="product.nombre" 
                         style="height: 200px; object-fit: contain; padding: 10px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ product.nombre }}</h5>
                        <p class="card-text text-truncate">{{ product.descripcion }}</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">{{ parseFloat(product.precio).toFixed(2) }} €</span>
                            <button class="btn btn-danger">Ver más</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="!loading && products.length === 0" class="text-center">
            <p>No hay productos disponibles.</p>
        </div>
    </div>
</template>

<style scoped>
.item-card {
    cursor: pointer;
    transition: transform 0.2s;
}
.item-card:hover {
    transform: translateY(-5px);
}
</style>
