import { defineStore } from "pinia";
import ProductService from "../services/ProductService";

export const useProductStore = defineStore("products", {
    state: () => ({
        products: [],
        loading: false,
        error: null,
        currentPage: 1,
        lastPage: 1,
        total: 0,
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

                // Incluir página actual
                if (!queryParams.page) queryParams.page = this.currentPage;

                const response = await ProductService.getProducts(queryParams);
                const data = response.data;

                // Respuesta paginada de Laravel
                if (data && data.data && data.last_page !== undefined) {
                    this.products = data.data;
                    this.currentPage = data.current_page;
                    this.lastPage = data.last_page;
                    this.total = data.total;
                } else {
                    // Fallback para respuestas no paginadas
                    this.products = Array.isArray(data)
                        ? data
                        : data.products || data.data || [];
                    this.lastPage = 1;
                    this.total = this.products.length;
                }
            } catch (err) {
                console.error("Error fetching products:", err);
                this.error = "Error al cargar los productos";
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.fetchProducts({ page });
            }
        },

        setCategory(category) {
            this.filters.category = category;
            this.currentPage = 1;
            this.fetchProducts();
        },

        toggleOffers(active) {
            this.filters.offers = active;
            this.currentPage = 1;
            this.fetchProducts();
        },

        clearFilters() {
            this.filters.category = null;
            this.filters.offers = false;
            this.currentPage = 1;
            this.fetchProducts();
        },
    },
});
