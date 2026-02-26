<script setup>
import { ref, reactive, computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { useRouter } from 'vue-router';
import { useUiStore } from '@/stores/uiStore';
import { useAuthStore } from '../../auth/store/authStore';
import http from '@/services/http';
import { usePrefsStore } from '../../../stores/prefsStore';

const cartStore = useCartStore();
const router = useRouter();
const ui = useUiStore();
const authStore = useAuthStore();
const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);

const form = reactive({
    firstName: '',
    lastName: '',
    email: '',
    address: '',
    city: '',
    zip: '',
    cardName: '',
    cardNumber: '',
    expDate: '',
    cvv: ''
});

const processing = ref(false);
const purchaseComplete = ref(false);
const purchasedCodes = ref([]);
const purchaseErrors = ref([]);

const total = computed(() => {
    return (cartStore.totalPrice * 1.21).toFixed(2);
});

// Pre-fill email from authenticated user
if (authStore.user) {
    form.email = authStore.user.email || '';
    form.firstName = authStore.user.name || '';
    form.lastName = authStore.user.last_name || '';
}

const submitOrder = async () => {
    processing.value = true;
    purchasedCodes.value = [];
    purchaseErrors.value = [];

    for (const item of cartStore.items) {
        for (let i = 0; i < item.quantity; i++) {
            try {
                const response = await http.post('/api/compra', {
                    product_id: item.id
                });
                purchasedCodes.value.push({
                    juego: response.data.juego,
                    codigo: response.data.codigo,
                    message: response.data.message
                });
            } catch (err) {
                const errorMsg = err.response?.data?.error || 'Error al procesar la compra';
                purchaseErrors.value.push({
                    juego: item.title,
                    error: errorMsg
                });
            }
        }
    }

    processing.value = false;

    if (purchasedCodes.value.length > 0) {
        purchaseComplete.value = true;
        cartStore.clearCart();
        ui.showToast('success', '¡Compra realizada con éxito! Revisa tu correo.');
    } else {
        ui.showToast('error', 'No se pudo procesar ningún producto.');
    }
};
</script>

<template>
    <div class="checkout-container">
        <!-- Pantalla de confirmación tras compra exitosa -->
        <div v-if="purchaseComplete" class="purchase-success">
            <div class="success-card">
                <div class="success-icon">🎉</div>
                <h1>{{ t.checkout.successTitle }}</h1>
                <p class="success-subtitle">{{ t.checkout.successSubtitle }} <strong>{{
                        authStore.user?.email }}</strong></p>

                <div class="codes-list">
                    <div v-for="(item, index) in purchasedCodes" :key="index" class="code-card">
                        <div class="code-game-name">{{ item.juego }}</div>
                        <div class="code-value">{{ item.codigo }}</div>
                        <span class="code-status">{{ item.message }}</span>
                    </div>
                </div>

                <div v-if="purchaseErrors.length > 0" class="errors-section">
                    <h3>{{ t.checkout.errorsTitle }}</h3>
                    <div v-for="(err, index) in purchaseErrors" :key="index" class="error-item">
                        <strong>{{ err.juego }}</strong>: {{ err.error }}
                    </div>
                </div>

                <button class="btn-back-home" @click="router.push('/')">
                    {{ t.checkout.backHome }}
                </button>
            </div>
        </div>

        <!-- Formulario de checkout -->
        <template v-else>
            <h1 class="checkout-title">{{ t.checkout.title }}</h1>

            <div class="checkout-grid">
                <!-- Columna Izquierda: Formulario -->
                <div class="checkout-form">
                    <section class="form-section">
                        <h2>{{ t.checkout.shippingInfo }}</h2>
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label>{{ t.checkout.name }}</label>
                                <input type="text" v-model="form.firstName" required />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.lastName }}</label>
                                <input type="text" v-model="form.lastName" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ t.checkout.email }}</label>
                            <input type="email" v-model="form.email" required />
                        </div>

                        <div class="form-group">
                            <label>{{ t.checkout.address }}</label>
                            <input type="text" v-model="form.address" required />
                        </div>

                        <div class="form-row three-col">
                            <div class="form-group">
                                <label>{{ t.checkout.city }}</label>
                                <input type="text" v-model="form.city" required />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.zip }}</label>
                                <input type="text" v-model="form.zip" required />
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h2>{{ t.checkout.paymentInfo }}</h2>
                        <div class="form-group">
                            <label>{{ t.checkout.cardName }}</label>
                            <input type="text" v-model="form.cardName" required />
                        </div>
                        <div class="form-group">
                            <label>{{ t.checkout.cardNumber }}</label>
                            <input type="text" v-model="form.cardNumber" placeholder="0000 0000 0000 0000" required />
                        </div>
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label>{{ t.checkout.expDate }}</label>
                                <input type="text" v-model="form.expDate" placeholder="MM/YY" required />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.cvv }}</label>
                                <input type="text" v-model="form.cvv" required />
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Columna Derecha: Resumen -->
                <div class="order-summary">
                    <div class="summary-card">
                        <h2>{{ t.checkout.summary }}</h2>
                        <div class="order-items">
                            <div v-for="item in cartStore.items" :key="item.id" class="order-item">
                                <div class="item-thumb">
                                    <img :src="item.image" :alt="item.title">
                                </div>
                                <div class="item-details">
                                    <span class="item-title">{{ item.title }}</span>
                                    <span class="item-qty">{{ t.cart.quantity }}: {{ item.quantity }}</span>
                                </div>
                                <div class="item-price">
                                    {{ (item.price * item.quantity).toFixed(2) }}€
                                </div>
                            </div>
                        </div>

                        <div class="price-breakdown">
                            <div class="breakdown-row">
                                <span>{{ t.checkout.subtotal }}</span>
                                <span>{{ cartStore.totalPrice.toFixed(2) }}€</span>
                            </div>
                            <div class="breakdown-row">
                                <span>{{ t.checkout.tax }}</span>
                                <span>{{ (cartStore.totalPrice * 0.21).toFixed(2) }}€</span>
                            </div>
                            <div class="total-row">
                                <span>{{ t.checkout.total }}</span>
                                <span>{{ total }}€</span>
                            </div>
                        </div>

                        <button class="btn-confirm" @click="submitOrder" :disabled="processing">
                            <span v-if="processing">⏳ {{ t.checkout.processing }}</span>
                            <span v-else>{{ t.checkout.confirmBtn }} {{ total }}€</span>
                        </button>

                        <p class="secure-payment">
                            <span class="lock-icon">🔒</span> {{ t.checkout.securePayment }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<style>
@import '../../../assets/css/checkout.css';
</style>
