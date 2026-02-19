import { defineStore } from "pinia";
import ProductService from "../services/ProductService"; // Adjust path if needed

export const useProductStore = defineStore("products", {
    state: () => ({
        products: [],
        loading: false,
        error: null,
        filters: {
            category: null,
            offers: false,
        },
    }),

    actions: {
        async fetchProducts(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const queryParams = { ...this.filters, ...params };

                if (!queryParams.offers) delete queryParams.offers;
                if (!queryParams.category) delete queryParams.category;
                if (!queryParams.platform) delete queryParams.platform;
                if (!queryParams.q) delete queryParams.q;

                const response = await ProductService.getProducts(queryParams);
                const data = response.data;
                this.products = Array.isArray(data)
                    ? data
                    : data.products || data.data || [];
            } catch (err) {
                console.error("Error fetching products:", err);
                this.error = "Error al cargar los productos";
            } finally {
                this.loading = false;
            }
        },

        setCategory(category) {
            this.filters.category = category;
            this.fetchProducts();
        },

        toggleOffers(active) {
            this.filters.offers = active;
            this.fetchProducts();
        },

        clearFilters() {
            this.filters.category = null;
            this.filters.offers = false;
            this.fetchProducts();
        },
    },
});
