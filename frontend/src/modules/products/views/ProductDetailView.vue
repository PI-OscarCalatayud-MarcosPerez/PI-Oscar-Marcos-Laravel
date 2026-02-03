<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import ProductService from '../modules/products/services/ProductService';
import { useAuthStore } from '../modules/auth/store/authStore';

const route = useRoute();
const authStore = useAuthStore();
const product = ref(null);
const loading = ref(true);
const error = ref(null);

const fetchProduct = async () => {
  try {
    const response = await ProductService.getProduct(route.params.id);
    product.value = response.data;
  } catch (err) {
    console.error(err);
    error.value = "Producto no encontrado.";
  } finally {
    loading.value = false;
  }
};

const getImage = (prod) => {
    if (!prod) return '';
    let img = prod.imagen_url || prod.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('/')) {
        img = '/' + img;
    }
    return img;
};

// Reviews logic placeholders
const reviewForm = ref({
    estrellas: 5,
    comentario: ''
});

const submitReview = async () => {
    // Need to implement review service interaction
    alert("Pronto podrás enviar reseñas. (Simulado)");
};

const deleteReview = (id) => {
    if(confirm('¿Seguro que quieres borrar este comentario?')) {
        // Implement delete logic
        alert("Borrado simulado");
    }
};

onMounted(() => {
    fetchProduct();
});
</script>

<template>
<div v-if="loading" class="text-center p-5">Cargando...</div>
<div v-else-if="error" class="text-center p-5 text-danger">{{ error }}</div>
<div v-else>
    <div class="container-fluid breadcrumb-container"
        style="background-color: #f8f9fa; padding: 10px 0; margin-bottom: 20px;">
        <div class="container px-md-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="margin: 0;">
                    <li class="breadcrumb-item"><RouterLink to="/" style="color:#fa4841; text-decoration:none;">Inicio</RouterLink></li>
                    <li class="breadcrumb-item"><RouterLink to="/products" style="color:#fa4841; text-decoration:none;">Tienda</RouterLink></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ product.nombre }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="product-main">
        <main class="product-detail-container">
            <div class="product-image">
                <img id="p-image" :src="getImage(product)" :alt="product.nombre">
            </div>
            <div class="product-info">
                <h1>{{ product.nombre }}</h1>
                <span class="product-sku">REF: {{ product.sku }}</span>
                <p class="product-price">{{ parseFloat(product.precio).toFixed(2) }} €</p>
                <span class="product-stock">Stock: {{ product.stock }}</span>
                <p class="product-desc">{{ product.descripcion }}</p>
                <button class="btn-buy-large">Añadir al Carrito 🛒</button>
            </div>
        </main>

        <section class="reviews-container">
            <div class="reviews-header">
                <h2>Opiniones de la Comunidad</h2>
                <div class="rating-summary">
                    <!-- Placeholder for avg stars if available in API response -->
                    <div class="rating-number">{{ product.media_estrellas || 0 }}</div>
                    <div class="rating-stars">
                         <!-- Simplified star rendering -->
                        <span v-for="i in 5" :key="i" :style="{ color: i <= Math.round(product.media_estrellas || 0) ? '#ffc700' : '#ccc' }">★</span>
                    </div>
                    <div class="rating-count">Basado en {{ product.reviews ? product.reviews.length : 0 }} opiniones</div>
                </div>
            </div>
            
            <div v-if="authStore.isAuthenticated">
                <!-- Check if user already reviewed logic would go here -->
                <div class="review-form-card">
                    <h3>Deja tu valoración</h3>
                    <form @submit.prevent="submitReview">
                        <div class="rate">
                            <input type="radio" id="star5" v-model="reviewForm.estrellas" value="5" /><label for="star5">★</label>
                            <input type="radio" id="star4" v-model="reviewForm.estrellas" value="4" /><label for="star4">★</label>
                            <input type="radio" id="star3" v-model="reviewForm.estrellas" value="3" /><label for="star3">★</label>
                            <input type="radio" id="star2" v-model="reviewForm.estrellas" value="2" /><label for="star2">★</label>
                            <input type="radio" id="star1" v-model="reviewForm.estrellas" value="1" /><label for="star1">★</label>
                        </div>
                        <textarea v-model="reviewForm.comentario" placeholder="¿Qué te ha parecido el juego?" required></textarea>
                        <button type="submit" class="btn-submit-review">Publicar Opinión</button>
                    </form>
                </div>
            </div>
            <div v-else class="review-form-card" style="text-align: center;">
                 <p>Debes <RouterLink to="/login" style="color:#fa4841; font-weight:bold;">iniciar sesión</RouterLink> para dejar tu valoración.</p>
            </div>

            <div class="reviews-list">
                <div v-for="review in product.reviews" :key="review.id" class="comment-item">
                     <div class="comment-header">
                        <div>
                            <span class="user-name">{{ review.user ? review.user.name : 'Usuario' }}</span>
                            <span class="comment-stars">
                                <span v-for="j in 5" :key="j" :style="{ color: j <= review.estrellas ? '#ffc700' : '#ccc' }">★</span>
                            </span>
                        </div>
                        <span class="comment-date">{{ new Date(review.created_at).toLocaleDateString() }}</span>
                    </div>
                    <div class="comment-body">
                        {{ review.comentario }}
                        <button v-if="authStore.user && authStore.user.role === 'admin'" 
                                class="btn btn-sm btn-danger" 
                                style="float:right;"
                                @click="deleteReview(review.id)">Borrar</button>
                    </div>
                </div>
                <p v-if="!product.reviews || product.reviews.length === 0" style="text-align: center; color: #999;">Aún no hay opiniones.</p>
            </div>
        </section>
    </div>
</div>
</template>

<style scoped>
@import '@/assets/css/productos.css';
</style>
