<script setup>
import { useCartStore } from '../store/cartStore';
import { usePrefsStore } from '../../../stores/prefsStore';
import { computed } from 'vue';

const cartStore = useCartStore();
const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);

</script>

<template>
    <div class="cart-container">
        <h1 class="cart-title">{{ t.cart.title }}</h1>

        <div class="cart-grid">
            <!-- Columna Izquierda: Productos -->
            <div class="cart-items">
                <div v-if="cartStore.items.length === 0" class="empty-cart">
                    <p>{{ t.cart.empty }}</p>
                </div>

                <div v-else v-for="item in cartStore.items" :key="item.id" class="cart-item">
                    <div class="item-image">
                        <img :src="item.image" :alt="item.title" />
                    </div>
                    <div class="item-details">
                        <h3>{{ item.title }}</h3>
                        <div class="item-quantity">
                            <button @click="cartStore.decreaseQuantity(item.id)">-</button>
                            <span>{{ item.quantity }}</span>
                            <button @click="cartStore.increaseQuantity(item.id)">+</button>
                        </div>
                    </div>
                    <div class="item-price">
                        <p>{{ (item.price * item.quantity).toFixed(2) }}€</p>
                        <button class="remove-btn" @click="cartStore.removeItem(item.id)">
                            <img src="/img/borrar.png" alt="Eliminar" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Resumen -->
            <div class="cart-summary">
                <h2>{{ t.cart.summary }}</h2>

                <div class="summary-row">
                    <span>{{ t.cart.subtotal }}</span>
                    <span>{{ cartStore.totalPrice.toFixed(2) }}€</span>
                </div>

                <div class="summary-row">
                    <span>{{ t.cart.tax }}</span>
                    <span>{{ (cartStore.totalPrice * 0.21).toFixed(2) }}€</span>
                </div>

                <div class="coupon-section">
                    <input type="text" :placeholder="t.cart.coupon" />
                    <button>{{ t.cart.apply }}</button>
                </div>

                <div class="terms-section">
                    <label>
                        <input type="checkbox" />
                        {{ t.cart.terms }}
                    </label>
                </div>

                <div class="total-row">
                    <span>{{ t.cart.total }}</span>
                    <span>{{ (cartStore.totalPrice * 1.21).toFixed(2) }}€</span>
                </div>

                <button class="checkout-btn" @click="$router.push('/checkout')">{{ t.cart.checkoutLine }}</button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/cart.css';
</style>
