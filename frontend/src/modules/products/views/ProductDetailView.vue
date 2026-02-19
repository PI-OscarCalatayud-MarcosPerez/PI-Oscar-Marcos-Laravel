<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useCartStore } from '../../cart/store/cartStore';
import { useUiStore } from '@/stores/uiStore';
import { useRoute, useRouter } from 'vue-router';
import ProductService from '../services/ProductService';
import { useAuthStore } from '../../auth/store/authStore';
import { useRole } from '../../roles/composables/useRole';
import http from '@/services/http';
import RoleGuard from '../../roles/components/RoleGuard.vue';



const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const ui = useUiStore();
const { hasRole } = useRole();
const product = ref(null);
const loading = ref(true);
const error = ref(null);

const rating = ref(5);
const comment = ref('');
const submitting = ref(false);
const reviewError = ref(null);

const addToCart = () => {
    if (!product.value) return;

    cartStore.addItem({
        id: product.value.id,
        title: product.value.nombre,
        price: parseFloat(product.value.precio),
        image: getImage(product.value),
        quantity: 1
    });

    ui.showToast('success', 'Producto añadido al carrito');
}

const buyNow = () => {
    addToCart();
    router.push('/checkout');
}

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

const averageRating = computed(() => {
    if (!product.value) return '0.0';
    if (!product.value.reviews || product.value.reviews.length === 0) {
        return product.value.media_estrellas ? parseFloat(product.value.media_estrellas).toFixed(1) : '0.0';
    }
    const sum = product.value.reviews.reduce((a, b) => a + b.estrellas, 0);
    return (sum / product.value.reviews.length).toFixed(1);
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

const editingReviewId = ref(null);
const editForm = reactive({
    estrellas: 5,
    comentario: ''
});

const canEditReview = (review) => {
    if (!authStore.user) return false;
    // Admin/Gerent can edit ALL, User can edit OWN
    if (hasRole('admin', 'gerent')) return true;
    return review.user_id === authStore.user.id;
};

const startEdit = (review) => {
    editingReviewId.value = review.id;
    editForm.estrellas = review.estrellas;
    editForm.comentario = review.comentario;
};

const cancelEdit = () => {
    editingReviewId.value = null;
    editForm.comentario = '';
    editForm.estrellas = 5;
};

const updateReview = async (reviewId) => {
    try {
        const payload = {
            estrellas: editForm.estrellas,
            comentario: editForm.comentario
        };
        const response = await http.put(`/api/reviews/${reviewId}`, payload);

        const index = product.value.reviews.findIndex(r => r.id === reviewId);
        if (index !== -1) {
            product.value.reviews[index] = { ...product.value.reviews[index], ...response.data };
        }

        cancelEdit();
        ui.showToast('success', 'Comentario actualizado');
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.message || 'Error al actualizar comentario');
    }
};

const deleteReview = async (reviewId) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) return;

    try {
        await http.delete(`/api/reviews/${reviewId}`);
        product.value.reviews = product.value.reviews.filter(r => r.id !== reviewId);
        ui.showToast('success', 'Comentario eliminado');
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
                    <div class="product-actions">
                        <button class="btn-buy-large" @click="buyNow">Comprar</button>
                        <button class="btn-add-cart" @click="addToCart" title="Añadir al carrito">
                            <img src="/img/carrito1.png" alt="Carrito" />
                        </button>
                    </div>
                </div>
            </main>

            <section class="reviews-container">
                <div class="reviews-header">
                    <h2>Opiniones de la Comunidad</h2>
                    <div class="rating-summary">
                        <div class="rating-number">{{ averageRating }}</div>

                        <div class="rating-stars">
                            <template v-for="i in 5" :key="i">
                                <img :src="i <= Math.round(parseFloat(averageRating)) ? '/img/estrella_roja.png' : '/img/estrella_roja_sinrelleno.png'"
                                    alt="star" style="width: 24px; height: 24px;" />
                            </template>
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
                            <div class="rate-custom">
                                <template v-for="star in 5" :key="star">
                                    <img :src="star <= rating ? '/img/estrella_roja.png' : '/img/estrella_roja_sinrelleno.png'"
                                        @click="rating = star"
                                        style="width: 30px; height: 30px; cursor: pointer; margin-right: 2px;" />
                                </template>
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
                        <div v-if="editingReviewId === review.id" class="edit-review-form">
                            <!-- Edit Mode -->
                            <div class="rate-custom" style="margin-bottom:10px;">
                                <template v-for="n in 5" :key="n">
                                    <img :src="n <= editForm.estrellas ? '/img/estrella_roja.png' : '/img/estrella_roja_sinrelleno.png'"
                                        @click="editForm.estrellas = n"
                                        style="width: 24px; height: 24px; cursor: pointer; margin-right: 2px;" />
                                </template>
                            </div>
                            <textarea v-model="editForm.comentario" class="form-control"
                                style="width:100%; margin-bottom:10px;"></textarea>
                            <div class="edit-actions">
                                <button class="btn-save-edit" @click="updateReview(review.id)">Guardar</button>
                                <button class="btn-cancel-edit" @click="cancelEdit">Cancelar</button>
                            </div>
                        </div>

                        <div v-else>
                            <!-- View Mode -->
                            <div class="comment-header">
                                <div>
                                    <span class="user-name">{{ review.user?.name || 'Usuario' }}</span>
                                    <span class="comment-stars">
                                        <template v-for="j in 5" :key="j">
                                            <img :src="j <= review.estrellas ? '/img/estrella_roja.png' : '/img/estrella_roja_sinrelleno.png'"
                                                alt="star" style="width: 20px; height: 20px; vertical-align: middle;" />
                                        </template>
                                    </span>
                                </div>
                                <span class="comment-date">{{ formatDate(review.created_at) }}</span>
                            </div>
                            <div class="comment-body">
                                {{ review.comentario }}
                                <div class="comment-actions" style="float:right;">
                                    <button v-if="canEditReview(review)" class="btn-action-comment"
                                        @click="startEdit(review)" title="Editar comentario">
                                        <img src="/img/lapiz.png" alt="Editar" class="icon-action"
                                            style="width:20px; vertical-align:middle;" />
                                    </button>
                                    <button
                                        v-if="hasRole('admin', 'gerent') || (authStore.user && authStore.user.id === review.user_id)"
                                        class="btn-action-comment" @click="deleteReview(review.id)"
                                        title="Eliminar comentario">
                                        <img src="/img/borrar.png" alt="Eliminar" class="icon-action"
                                            style="width:20px; vertical-align:middle; filter: brightness(0) saturate(100%) invert(32%) sepia(96%) saturate(1915%) hue-rotate(340deg) brightness(98%) contrast(97%);" />
                                    </button>
                                </div>
                            </div>
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
