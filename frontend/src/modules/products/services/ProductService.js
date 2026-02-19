import http from "@/services/http";

export default {
    getProducts(params = {}) {
        return http.get("/api/products", { params });
    },
    getProduct(id) {
        return http.get(`/api/products/${id}`);
    },
};
