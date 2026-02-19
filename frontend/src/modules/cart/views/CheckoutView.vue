<script setup>
import { reactive, computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { useRouter } from 'vue-router';
import { useUiStore } from '@/stores/uiStore';

const cartStore = useCartStore();
const router = useRouter();
const ui = useUiStore();

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

const total = computed(() => {
    return (cartStore.totalPrice * 1.21).toFixed(2);
});

const submitOrder = () => {
    // Aquí iría la lógica real de envío
    ui.showToast('success', '¡Pedido relizado con éxito!');
    cartStore.clearCart();
    setTimeout(() => {
        router.push('/');
    }, 2000);
};
</script>

<template>
    <div class="checkout-container">
        <h1 class="checkout-title">Tramitar Pedido</h1>

        <div class="checkout-grid">
            <!-- Columna Izquierda: Formulario -->
            <div class="checkout-form">
                <section class="form-section">
                    <h2>Datos de Envío</h2>
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" v-model="form.firstName" required />
                        </div>
                        <div class="form-group">
                            <label>Apellidos</label>
                            <input type="text" v-model="form.lastName" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" v-model="form.email" required />
                    </div>

                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" v-model="form.address" required />
                    </div>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" v-model="form.city" required />
                        </div>
                        <div class="form-group">
                            <label>Código Postal</label>
                            <input type="text" v-model="form.zip" required />
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <h2>Pago</h2>
                    <div class="form-group">
                        <label>Nombre en la tarjeta</label>
                        <input type="text" v-model="form.cardName" required />
                    </div>
                    <div class="form-group">
                        <label>Número de tarjeta</label>
                        <input type="text" v-model="form.cardNumber" placeholder="0000 0000 0000 0000" required />
                    </div>
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label>Fecha Exp.</label>
                            <input type="text" v-model="form.expDate" placeholder="MM/YY" required />
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" v-model="form.cvv" required />
                        </div>
                    </div>
                </section>
            </div>

            <!-- Columna Derecha: Resumen -->
            <div class="order-summary">
                <div class="summary-card">
                    <h2>Resumen del Pedido</h2>
                    <div class="order-items">
                        <div v-for="item in cartStore.items" :key="item.id" class="order-item">
                            <div class="item-thumb">
                                <img :src="item.image" :alt="item.title">
                            </div>
                            <div class="item-details">
                                <span class="item-title">{{ item.title }}</span>
                                <span class="item-qty">Cantidad: {{ item.quantity }}</span>
                            </div>
                            <div class="item-price">
                                {{ (item.price * item.quantity).toFixed(2) }}€
                            </div>
                        </div>
                    </div>

                    <div class="price-breakdown">
                        <div class="breakdown-row">
                            <span>Subtotal</span>
                            <span>{{ cartStore.totalPrice.toFixed(2) }}€</span>
                        </div>
                        <div class="breakdown-row">
                            <span>IVA (21%)</span>
                            <span>{{ (cartStore.totalPrice * 0.21).toFixed(2) }}€</span>
                        </div>
                        <div class="total-row">
                            <span>Total</span>
                            <span>{{ total }}€</span>
                        </div>
                    </div>

                    <button class="btn-confirm" @click="submitOrder">
                        Confirmar Pago de {{ total }}€
                    </button>

                    <p class="secure-payment">
                        <span class="lock-icon">🔒</span> Pago 100% Seguro
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/checkout.css';
</style>
