import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../modules/auth/views/LoginView.vue')
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../modules/auth/views/RegisterView.vue')
        },
        {
            path: '/contacto',
            name: 'contacto',
            component: () => import('../views/ContactView.vue')
        },
        {
            path: '/products/:id',
            name: 'product-detail',
            component: () => import('../modules/products/views/ProductDetailView.vue')
        },
        {
            path: '/products',
            name: 'products-list',
            component: () => import('../modules/products/views/ProductListView.vue')
        },
        {
            path: '/import',
            name: 'import-products',
            component: () => import('../views/ImportView.vue')
        }
    ]
})

export default router
