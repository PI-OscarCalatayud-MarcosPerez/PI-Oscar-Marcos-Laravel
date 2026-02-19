<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import http from '@/services/http';

const props = defineProps({
    productId: {
        type: [Number, String],
        required: true
    }
});

const recommendations = ref([]);
const loading = ref(true);
const router = useRouter();

const fetchRecommendations = async () => {
    loading.value = true;
    try {
        const response = await http.get(`/api/products/${props.productId}/recommendations`);
        recommendations.value = response.data;
    } catch (error) {
        console.error("Error fetching recommendations:", error);
    } finally {
        loading.value = false;
    }
};

const getImage = (product) => {
    let img = product.imagen_url || product.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('data:')) {
        if (img.startsWith('/')) img = img.substring(1);
        if (!img.startsWith('img/')) img = 'img/' + img;
        img = '/' + img;
    }
    return img;
};

const goToProduct = (id) => {
    router.push(`/products/${id}`);
};

watch(() => props.productId, () => {
    fetchRecommendations();
});

onMounted(() => {
    fetchRecommendations();
});
</script>

<template>
    <div v-if="recommendations.length > 0" class="recommendations-container mt-5">
        <h3 class="mb-4">También te podría gustar</h3>
        <div class="row">
            <div v-for="product in recommendations" :key="product.id" class="col-md-4 mb-3">
                <div class="card h-100" @click="goToProduct(product.id)" style="cursor: pointer;">
                    <img :src="getImage(product)" class="card-img-top" :alt="product.nombre" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                    <div class="card-body">
                        <h5 class="card-title">{{ product.nombre }}</h5>
                        <p class="card-text text-muted">{{ parseFloat(product.precio).toFixed(2) }}€</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.recommendations-container {
    border-top: 1px solid #eaeaea;
    padding-top: 2rem;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
