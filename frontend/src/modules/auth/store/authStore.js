import { defineStore } from "pinia";
import { apiLogin, apiGetUser, apiLogout } from "../services/authService";
import http from "@/services/http";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("token"),
        bootstrapped: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        role: (state) => state.user?.role ?? null,
    },

    actions: {
        async login(credentials) {
            try {
                const { data } = await apiLogin(credentials);
                const response = data;
                if (response && response.token) {
                    this.token = response.token;
                    localStorage.setItem("token", response.token);
                }

                if (!response.user) {
                    this.user = await apiGetUser();
                } else {
                    this.user = response.user;
                }

                if (!this.token) {
                    this.token = "session";
                    localStorage.setItem("token", "session");
                }
            } catch (error) {
                console.error("Login error:", error);
                throw error;
            }
        },

        async register(userData) {
            await http.get("/sanctum/csrf-cookie");
            const { data } = await http.post("/api/register", userData);
            const { token, user } = data;
            this.token = token || "session";
            this.user = user;
            localStorage.setItem("token", this.token);
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
                this.user = await apiGetUser();
            } catch (error) {
                if (error.response?.status === 401) {
                    this.user = null;
                    this.token = null;
                    localStorage.removeItem("token");
                }
            } finally {
                this.bootstrapped = true;
            }
        },

        async updateProfile(data) {
            const { data: updatedUser } = await http.put(
                "/api/user/profile",
                data,
            );
            this.user = updatedUser;
            return updatedUser;
        },

        async deleteAccount() {
            await http.delete("/api/user/delete");
            this.hardLogout();
        },
    },
});
