import http from '@/services/http';

export default {
    getProducts() {
        return http.get('/products');
    },
    getProduct(id) {
        return http.get(`/products/${id}`);
    }
}
