<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import ProductService from '../services/ProductService';
import { useAuthStore } from '../../auth/store/authStore';
import { useRole } from '../../roles/composables/useRole';
import http from '@/services/http';
import RoleGuard from '../../roles/components/RoleGuard.vue';

const route = useRoute();
const authStore = useAuthStore();
const { hasRole } = useRole();
const product = ref(null);
const loading = ref(true);
const error = ref(null);

const rating = ref(5);
const comment = ref('');
const submitting = ref(false);
const reviewError = ref(null);

const fetchProduct = async () => {
    loading.value = true;
    try {
        const response = await ProductService.getProduct(route.params.id);
        product.value = response.data;
    } catch (err) {
        error.value = "Error cargando producto";
        console.error(err);
    } finally {
        loading.value = false;
    }
}

const getImage = (prod) => {
    if (!prod) return "img/placeholder.jpg";
    let img = prod.imagen_url || prod.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('data:')) {
        if (img.startsWith('/')) img = img.substring(1);
        if (!img.startsWith('img/')) img = 'img/' + img;
        img = '/' + img;
    }
    return img;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES');
}

const userHasReview = computed(() => {
    if (!product.value || !authStore.user) return false;
    return product.value.reviews?.some(r => r.user_id === authStore.user.id);
});

const userReview = computed(() => {
    if (!userHasReview.value) return null;
    return product.value.reviews.find(r => r.user_id === authStore.user.id);
});

const submitReview = async () => {
    submitting.value = true;
    reviewError.value = null;
    try {
        const payload = {
            product_id: product.value.id,
            estrellas: rating.value,
            comentario: comment.value
        };
        const response = await http.post('/api/reviews', payload);

        if (product.value.reviews) {
            product.value.reviews.push(response.data);
        } else {
            product.value.reviews = [response.data];
        }
        await fetchProduct();

    } catch (err) {
        reviewError.value = err.response?.data?.message || "Error al enviar reseña";
    } finally {
        submitting.value = false;
    }
};

const deleteReview = async (reviewId) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) return;

    try {
        await http.delete(`/api/reviews/${reviewId}`);
        product.value.reviews = product.value.reviews.filter(r => r.id !== reviewId);
    } catch (err) {
        alert(err.response?.data?.message || 'Error al eliminar comentario');
    }
};

onMounted(async () => {
    fetchProduct();
    if (!authStore.bootstrapped) {
        await authStore.bootstrap();
    }
});
</script>

<template>
    <div class="product-main container">
        <div v-if="loading">Cargando detalles...</div>
        <div v-else-if="error">{{ error }}</div>

        <div v-else class="product-detail-layout">
            <main class="product-detail-container">
                <div class="product-image">
                    <img :src="getImage(product)" :alt="product.nombre">
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
                        <div class="rating-number">{{product.media_estrellas || (product.reviews &&
                            product.reviews.length ?
                            (product.reviews.reduce((a, b) => a + b.estrellas, 0) / product.reviews.length).toFixed(1) :
                            0)
                        }}
                        </div>

                        <div class="rating-stars">
                            <span v-for="i in 5" :key="i"
                                :style="{ color: i <= Math.round(product.media_estrellas || 0) ? '#ffc700' : '#ccc' }">★</span>
                        </div>

                        <div class="rating-count">Basado en {{ product.reviews ? product.reviews.length : 0 }} opiniones
                        </div>
                    </div>
                </div>

                <div v-if="authStore.isAuthenticated">
                    <div v-if="userHasReview && userReview" class="review-form-card"
                        style="text-align: center; background-color: #f8f9fa; border-left: 5px solid #4CAF50;">
                        <h3>¡Gracias por tu opinión!</h3>
                        <p>Ya has valorado este juego con <strong>{{ userReview.estrellas }} estrellas</strong>.</p>
                        <p><i>"{{ userReview.comentario }}"</i></p>
                    </div>

                    <div v-else class="review-form-card">
                        <h3>Deja tu valoración</h3>
                        <form @submit.prevent="submitReview">
                            <div class="rate">
                                <input type="radio" id="star5" value="5" v-model="rating" /><label for="star5">★</label>
                                <input type="radio" id="star4" value="4" v-model="rating" /><label for="star4">★</label>
                                <input type="radio" id="star3" value="3" v-model="rating" /><label for="star3">★</label>
                                <input type="radio" id="star2" value="2" v-model="rating" /><label for="star2">★</label>
                                <input type="radio" id="star1" value="1" v-model="rating" /><label for="star1">★</label>
                            </div>

                            <textarea v-model="comment" placeholder="¿Qué te ha parecido el juego?" required></textarea>

                            <div v-if="reviewError" class="alert alert-danger mt-2">{{ reviewError }}</div>

                            <button type="submit" class="btn-submit-review" :disabled="submitting">
                                {{ submitting ? 'Enviando...' : 'Publicar Opinión' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div v-else class="review-form-card" style="text-align: center;">
                    <p>Debes <RouterLink to="/login" style="color:#fa4841; font-weight:bold;">iniciar sesión
                        </RouterLink> para dejar tu valoración.</p>
                </div>

                <div class="reviews-list">
                    <div v-for="review in product.reviews" :key="review.id" class="comment-item">
                        <div class="comment-header">
                            <div>
                                <span class="user-name">{{ review.user?.name || 'Usuario' }}</span>
                                <span class="comment-stars">
                                    <span v-for="j in 5" :key="j"
                                        :style="{ color: j <= review.estrellas ? '#ffc700' : '#ccc' }">★</span>
                                </span>
                            </div>
                            <span class="comment-date">{{ formatDate(review.created_at) }}</span>
                        </div>
                        <div class="comment-body">
                            {{ review.comentario }}
                            <button v-if="hasRole('admin', 'gerent')" class="btn-delete-comment"
                                @click="deleteReview(review.id)" title="Eliminar comentario">
                                <img src="/img/borrar.png" alt="Eliminar" class="icon-delete" />
                            </button>
                        </div>
                    </div>

                    <p v-if="!product.reviews || product.reviews.length === 0" style="text-align: center; color: #999;">
                        Aún no hay opiniones. ¡Sé el primero!</p>
                </div>
            </section>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/product-detail.css';
</style>
