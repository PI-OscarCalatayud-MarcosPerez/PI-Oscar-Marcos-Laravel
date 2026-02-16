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
                target: "http://web",
                changeOrigin: true,
                secure: false,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            },
            "/sanctum": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: "",
            },
            "/login": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: "",
            },
            "/logout": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
            },
            "/register": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
            },
            "/user": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
            },
            "/reviews": {
                target: "http://web",
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
