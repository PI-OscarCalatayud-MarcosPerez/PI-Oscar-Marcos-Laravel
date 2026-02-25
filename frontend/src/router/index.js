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
            path: "/auth/callback",
            name: "google-callback",
            component: () => import("../modules/auth/views/GoogleCallback.vue"),
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
            path: "/about",
            name: "about",
            component: () => import("../views/AboutView.vue"),
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
            path: "/sell",
            name: "sell",
            component: () => import("../views/SellView.vue"),
            meta: {
                requiresAuth: true,
                roles: ["admin", "gerent", "venedor", "user"],
            }, // User can become seller? Assuming 'user' or specific role.
        },
        {
            path: "/cart",
            name: "cart",
            component: () => import("../modules/cart/views/CartView.vue"),
        },
        {
            path: "/checkout",
            name: "checkout",
            component: () => import("../modules/cart/views/CheckoutView.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/forbidden",
            name: "forbidden",
            component: () => import("../views/ForbiddenView.vue"),
        },
    ],
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    console.log(`[Router] Navigate to: ${to.fullPath}`);
    console.log(
        `[Router] Auth state: token=${!!auth.token}, user=${auth.user?.email}`,
    );

    if (!auth.bootstrapped) {
        console.log("[Router] Bootstrapping...");
        await auth.bootstrap();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        console.warn("[Router] Redirecting to login");
        return { name: "login", query: { redirect: to.fullPath } };
    }

    if (to.meta.roles) {
        const userRole = auth.role;
        console.log(
            `[Router] Checking roles: ${to.meta.roles} vs User: ${userRole}`,
        );
        if (!userRole || !to.meta.roles.includes(userRole)) {
            return { name: "forbidden" };
        }
    }
});

export default router;
