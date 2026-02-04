import { defineStore } from "pinia";
import { ref } from "vue";
import authService from "../services/authService";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const isAuthenticated = ref(!!localStorage.getItem("token")); // Simplified without token storage since we rely on cookie

    async function login(credentials) {
        try {
            const response = await authService.login(credentials);
            user.value = response.data.user;
            isAuthenticated.value = true;
            localStorage.setItem("isAuthenticated", "true");
        } catch (error) {
            console.error("Login failed", error);
            throw error;
        }
    }

    async function register(userData) {
        try {
            const response = await authService.register(userData);
            user.value = response.data.user;
            isAuthenticated.value = true;
            localStorage.setItem("isAuthenticated", "true");
        } catch (error) {
            console.error("Registration failed", error);
            throw error;
        }
    }

    async function logout() {
        try {
            await authService.logout();
        } catch (err) {
            console.error("Logout failed on server", err);
        } finally {
            user.value = null;
            isAuthenticated.value = false;
            localStorage.removeItem("isAuthenticated");
        }
    }

    async function fetchUser() {
        try {
            const response = await authService.getUser();
            user.value = response.data;
            isAuthenticated.value = true;
            localStorage.setItem("isAuthenticated", "true");
        } catch (error) {
            user.value = null;
            isAuthenticated.value = false;
            localStorage.removeItem("isAuthenticated");
        }
    }

    return {
        user,
        isAuthenticated,
        login,
        register,
        logout,
        fetchUser,
    };
});
