import http from "@/services/http";

export default {
    getCategories() {
        return http.get("/api/categories");
    },

    getCategory(id) {
        return http.get(`/api/categories/${id}`);
    },

    createCategory(data) {
        return http.post("/api/categories", data);
    },

    updateCategory(id, data) {
        return http.put(`/api/categories/${id}`, data);
    },

    deleteCategory(id) {
        return http.delete(`/api/categories/${id}`);
    },
};
