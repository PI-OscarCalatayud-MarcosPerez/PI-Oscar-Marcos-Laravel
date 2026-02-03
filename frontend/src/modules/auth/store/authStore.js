import { defineStore } from 'pinia';
import authService from '../services/authService';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        isAuthenticated: !!localStorage.getItem('token')
    }),
    actions: {
        async login(credentials) {
            try {
                const response = await authService.login(credentials);
                const token = response.data.access_token;
                const user = response.data.user;

                this.token = token;
                this.user = user;
                this.isAuthenticated = true;

                localStorage.setItem('token', token);

                return user;
            } catch (error) {
                console.error("Login failed", error);
                throw error;
            }
        },
        async register(userData) {
            try {
                const response = await authService.register(userData);
                const token = response.data.access_token;
                const user = response.data.user;

                this.token = token;
                this.user = user;
                this.isAuthenticated = true;

                localStorage.setItem('token', token);
                return user;
            } catch (error) {
                console.error("Registration failed", error);
                throw error;
            }
        },
        async logout() {
            try {
                await authService.logout();
            } catch (err) {
                console.error("Logout failed on server", err);
            } finally {
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;
                localStorage.removeItem('token');
            }
        },
        async fetchUser() {
            if (this.token) {
                try {
                    const response = await authService.getUser();
                    this.user = response.data;
                    this.isAuthenticated = true;
                } catch (error) {
                    this.logout();
                }
            }
        }
    }
});
