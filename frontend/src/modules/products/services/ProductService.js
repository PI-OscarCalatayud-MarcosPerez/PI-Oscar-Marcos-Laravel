import http from "@/services/http";

export default {
    getProducts(params = {}) {
        return http.get("/api/products", { params });
    },
    getProduct(id) {
        return http.get(`/api/products/${id}`);
    },
    updateProduct(id, data) {
        return http.put(`/api/products/${id}`, data);
    },
    getAdminProducts() {
        return http.get("/api/admin/products");
    },
    getProductCodes(id) {
        return http.get(`/api/admin/products/${id}/codes`);
    },
    addProductCodes(id, codes) {
        return http.post(`/api/admin/products/${id}/codes`, { codes });
    },
};
