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
        isAuthenticated: (state) => !!state.token || !!state.user,
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

                console.log("Login successful:", this.user);
            } catch (error) {
                console.error("Login error:", error);
                throw error;
            }
        },

        async register(userData) {
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
                if (error.response?.status === 401) {
                    this.hardLogout();
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
