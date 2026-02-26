<script setup>
import { ref, reactive, computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { useRouter } from 'vue-router';
import { useUiStore } from '@/stores/uiStore';
import { useAuthStore } from '../../auth/store/authStore';
import http from '@/services/http';
import { usePrefsStore } from '../../../stores/prefsStore';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';

const cartStore = useCartStore();
const router = useRouter();
const ui = useUiStore();
const authStore = useAuthStore();
const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);

// Valores iniciales (se llenan después si hay usuario autenticado)
const initialValues = ref({
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

const schema = yup.object({
    firstName: yup.string().required(t.value?.checkout?.nameRequired || 'El nombre es obligatorio').min(2, 'Min 2 caracteres'),
    lastName: yup.string().required(t.value?.checkout?.lastNameRequired || 'El apellido es obligatorio').min(2, 'Min 2 caracteres'),
    email: yup.string().email('Email inválido').required(t.value?.checkout?.emailRequired || 'Obligatorio'),
    address: yup.string().required(t.value?.checkout?.addressRequired || 'La dirección es obligatoria'),
    city: yup.string().required(t.value?.checkout?.cityRequired || 'La ciudad es obligatoria'),
    zip: yup.string().required(t.value?.checkout?.zipRequired || 'El CP es obligatorio'),
    cardName: yup.string().required(t.value?.checkout?.cardNameRequired || 'El titular es obligatorio'),
    cardNumber: yup.string()
        .matches(/^\d{16}$/, 'Debe contener 16 dígitos')
        .required(t.value?.checkout?.cardNumberRequired || 'La tarjeta es obligatoria'),
    expDate: yup.string()
        .matches(/^(0[1-9]|1[0-2])\/\d{2}$/, 'Formato MM/YY')
        .required(t.value?.checkout?.expDateRequired || 'La caducidad es obligatoria'),
    cvv: yup.string()
        .matches(/^\d{3,4}$/, 'CVV inválido')
        .required(t.value?.checkout?.cvvRequired || 'CVV es obligatorio')
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
    initialValues.value.email = authStore.user.email || '';
    initialValues.value.firstName = authStore.user.name || '';
    initialValues.value.lastName = authStore.user.last_name || '';
}

const submitOrder = async (values) => {
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

            <Form class="checkout-grid" @submit="submitOrder" :validation-schema="schema"
                :initial-values="initialValues" v-slot="{ isSubmitting, errors }">
                <!-- Columna Izquierda: Formulario -->
                <div class="checkout-form">
                    <section class="form-section">
                        <h2>{{ t.checkout.shippingInfo }}</h2>
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label>{{ t.checkout.name }}</label>
                                <Field name="firstName" type="text" :class="{ 'is-invalid': errors.firstName }" />
                                <ErrorMessage name="firstName" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.lastName }}</label>
                                <Field name="lastName" type="text" :class="{ 'is-invalid': errors.lastName }" />
                                <ErrorMessage name="lastName" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ t.checkout.email }}</label>
                            <Field name="email" type="email" :class="{ 'is-invalid': errors.email }" />
                            <ErrorMessage name="email" class="error-text" style="color: #fa4841; font-size: 0.85rem;" />
                        </div>

                        <div class="form-group">
                            <label>{{ t.checkout.address }}</label>
                            <Field name="address" type="text" :class="{ 'is-invalid': errors.address }" />
                            <ErrorMessage name="address" class="error-text"
                                style="color: #fa4841; font-size: 0.85rem;" />
                        </div>

                        <div class="form-row three-col">
                            <div class="form-group">
                                <label>{{ t.checkout.city }}</label>
                                <Field name="city" type="text" :class="{ 'is-invalid': errors.city }" />
                                <ErrorMessage name="city" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.zip }}</label>
                                <Field name="zip" type="text" :class="{ 'is-invalid': errors.zip }" />
                                <ErrorMessage name="zip" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h2>{{ t.checkout.paymentInfo }}</h2>
                        <div class="form-group">
                            <label>{{ t.checkout.cardName }}</label>
                            <Field name="cardName" type="text" :class="{ 'is-invalid': errors.cardName }" />
                            <ErrorMessage name="cardName" class="error-text"
                                style="color: #fa4841; font-size: 0.85rem;" />
                        </div>
                        <div class="form-group">
                            <label>{{ t.checkout.cardNumber }}</label>
                            <Field name="cardNumber" type="text" placeholder="0000 1111 2222 3333"
                                :class="{ 'is-invalid': errors.cardNumber }" />
                            <ErrorMessage name="cardNumber" class="error-text"
                                style="color: #fa4841; font-size: 0.85rem;" />
                        </div>
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label>{{ t.checkout.expDate }}</label>
                                <Field name="expDate" type="text" placeholder="MM/YY"
                                    :class="{ 'is-invalid': errors.expDate }" />
                                <ErrorMessage name="expDate" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
                            </div>
                            <div class="form-group">
                                <label>{{ t.checkout.cvv }}</label>
                                <Field name="cvv" type="text" :class="{ 'is-invalid': errors.cvv }" />
                                <ErrorMessage name="cvv" class="error-text"
                                    style="color: #fa4841; font-size: 0.85rem;" />
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

                        <button type="submit" class="btn-confirm"
                            :disabled="processing || isSubmitting || Object.keys(errors).length > 0">
                            <span v-if="processing || isSubmitting">⏳ {{ t.checkout.processing }}</span>
                            <span v-else>{{ t.checkout.confirmBtn }} {{ total }}€</span>
                        </button>

                        <p class="secure-payment">
                            <span class="lock-icon">🔒</span> {{ t.checkout.securePayment }}
                        </p>
                    </div>
                </div>
            </Form>
        </template>
    </div>
</template>

<style>
@import '../../../assets/css/checkout.css';
</style>
