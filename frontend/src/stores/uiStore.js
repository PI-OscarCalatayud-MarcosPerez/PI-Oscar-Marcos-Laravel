import { defineStore } from "pinia";

export const useUiStore = defineStore("ui", {
    state: () => ({
        toast: null,
    }),

    actions: {
        showToast(type, message) {
            this.toast = { type, message };
            setTimeout(() => (this.toast = null), 2500);
        },
    },
});
