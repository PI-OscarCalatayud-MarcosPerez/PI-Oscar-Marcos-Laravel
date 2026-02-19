import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

import { fileURLToPath, URL } from "node:url";

// https://vite.dev/config/
export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
        },
    },
    server: {
        proxy: {
            "/api": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            },
            "/sanctum": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: "",
            },
            "/login": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: "",
            },
            "/logout": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
            },
            "/register": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
            },
            "/user": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
            },
            "/reviews": {
                target: "http://backend:8000",
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
