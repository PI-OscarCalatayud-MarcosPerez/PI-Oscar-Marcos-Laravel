import { defineStore } from "pinia";
import { apiLogin, apiLogout, apiGetUser } from "../api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("token"),
        bootstrapped: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        role: (state) => state.user?.role ?? null,
    },

    actions: {
        async login(credentials) {
            try {
                const { token, user } = await apiLogin(credentials);
                this.token = token;
                this.user = user;
                localStorage.setItem("token", token);
                console.log("Login successful:", user);
            } catch (error) {
                console.error("Login error:", error);
                throw error;
            }
        },

        async register(userData) {
            // ... (mantener igual si no se usa mucho aun)
            const { data } = await http.post("/api/register", userData);
            const { token, user } = data;
            this.token = token;
            this.user = user;
            localStorage.setItem("token", token);
        },

        async logout() {
            try {
                await apiLogout();
            } catch (e) {
                console.error("Logout API error", e);
            }
            this.hardLogout();
        },

        hardLogout() {
            console.log("Hard logout executed");
            this.user = null;
            this.token = null;
            localStorage.removeItem("token");
        },

        async bootstrap() {
            if (!this.token) {
                this.bootstrapped = true;
                return;
            }

            try {
                console.log("Bootstrapping user...");
                this.user = await apiGetUser();
                console.log("Bootstrap success:", this.user);
            } catch (error) {
                console.error("Bootstrap error:", error);
                // Solo hacer logout si es error de autenticación (401)
                if (error.response?.status === 401) {
                    this.hardLogout();
                }
            } finally {
                this.bootstrapped = true;
            }
        },
    },
});
