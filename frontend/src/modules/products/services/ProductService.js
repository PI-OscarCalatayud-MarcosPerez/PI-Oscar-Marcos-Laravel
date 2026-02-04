import http from "@/services/http";

export default {
    getProducts() {
        return http.get("/api/products");
    },
    getProduct(id) {
        return http.get(`/api/products/${id}`);
    },
};
