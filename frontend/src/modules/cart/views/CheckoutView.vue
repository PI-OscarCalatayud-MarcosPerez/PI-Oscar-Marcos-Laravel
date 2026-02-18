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
/* Checkout CSS - Grid Layout */
.checkout-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: 'Inter', sans-serif;
}

.checkout-title {
    font-size: 32px;
    margin-bottom: 30px;
    color: #0e273f;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 2fr 1.2fr;
    gap: 40px;
    align-items: start;
}

.checkout-form {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #0e273f;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #555;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    box-sizing: border-box;
}

.form-row {
    display: grid;
    gap: 20px;
}

.two-col {
    grid-template-columns: 1fr 1fr;
}

.three-col {
    grid-template-columns: 2fr 1fr;
}

/* Order Summary Enhanced */
.order-summary {
    position: sticky;
    top: 100px;
}

.summary-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
}

.summary-card h2 {
    font-size: 22px;
    margin-top: 0;
    margin-bottom: 25px;
    color: #0e273f;
    text-align: center;
}

.order-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 25px;
    padding-right: 10px;
}

.order-item {
    display: grid;
    grid-template-columns: 60px 1fr auto;
    gap: 15px;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f5f5f5;
}

.item-thumb img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #eee;
}

.item-details {
    display: flex;
    flex-direction: column;
}

.item-title {
    font-weight: 600;
    font-size: 14px;
    color: #0e273f;
    margin-bottom: 2px;
}

.item-qty {
    font-size: 12px;
    color: #888;
}

.item-price {
    font-weight: 600;
    color: #fa4841;
}

.price-breakdown {
    margin-bottom: 25px;
}

.breakdown-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
    color: #666;
}

.shipping-free {
    color: #2ecc71;
    font-weight: bold;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding-top: 15px;
    margin-top: 15px;
    border-top: 2px solid #0e273f;
    font-size: 22px;
    font-weight: 800;
    color: #0e273f;
}

.btn-confirm {
    width: 100%;
    background: #fa4841;
    color: white;
    border: none;
    padding: 18px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(250, 72, 65, 0.3);
}

.btn-confirm:active {
    transform: translateY(0);
}

.secure-payment {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: #999;
}

.lock-icon {
    margin-right: 5px;
}

/* Custom Scrollbar */
.order-items::-webkit-scrollbar {
    width: 6px;
}

.order-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.order-items::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

@media (max-width: 900px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
}
</style>
