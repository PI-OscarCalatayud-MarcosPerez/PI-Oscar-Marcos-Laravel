import { defineStore } from "pinia";

export const useCartStore = defineStore("cart", {
    state: () => ({
        items: [],
    }),

    getters: {
        totalPrice: (state) => {
            return state.items.reduce(
                (total, item) => total + item.price * item.quantity,
                0,
            );
        },
        itemCount: (state) => {
            return state.items.reduce(
                (total, item) => total + item.quantity,
                0,
            );
        },
    },

    actions: {
        addItem(product) {
            const existingItem = this.items.find(
                (item) => item.id === product.id,
            );
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.items.push({ ...product, quantity: 1 });
            }
        },
        removeItem(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
        increaseQuantity(id) {
            const item = this.items.find((item) => item.id === id);
            if (item) item.quantity++;
        },
        decreaseQuantity(id) {
            const item = this.items.find((item) => item.id === id);
            if (item && item.quantity > 1) {
                item.quantity--;
            } else {
                this.removeItem(id);
            }
        },
        clearCart() {
            this.items = [];
        },
    },
});
