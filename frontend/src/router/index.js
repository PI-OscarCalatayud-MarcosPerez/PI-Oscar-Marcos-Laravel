import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import { useAuthStore } from "../modules/auth/store/authStore";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
        },
        {
            path: "/login",
            name: "login",
            component: () => import("../modules/auth/views/LoginView.vue"),
        },
        {
            path: "/register",
            name: "register",
            component: () => import("../modules/auth/views/RegisterView.vue"),
        },
        {
            path: "/profile",
            name: "profile",
            component: () => import("../modules/auth/views/ProfileView.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/contacto",
            name: "contacto",
            component: () => import("../views/ContactView.vue"),
        },
        {
            path: "/products/:id",
            name: "product-detail",
            component: () =>
                import("../modules/products/views/ProductDetailView.vue"),
        },
        {
            path: "/products",
            name: "products-list",
            component: () =>
                import("../modules/products/views/ProductListView.vue"),
        },
        {
            path: "/import",
            name: "import-products",
            component: () => import("../views/ImportView.vue"),
            meta: { requiresAuth: true, roles: ["admin", "gerent", "venedor"] },
        },
        {
            path: "/forbidden",
            name: "forbidden",
            component: () => import("../views/ForbiddenView.vue"),
        },
    ],
});

// Guard global para proteger rutas
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    // Si la ruta requiere autenticación
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next("/login");
        return;
    }

    // Si la ruta requiere roles específicos
    if (to.meta.roles) {
        const userRole = authStore.user?.role;
        if (!userRole || !to.meta.roles.includes(userRole)) {
            next("/forbidden");
            return;
        }
    }

    next();
});

export default router;
