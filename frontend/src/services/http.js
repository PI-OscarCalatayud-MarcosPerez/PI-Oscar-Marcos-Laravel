import axios from "axios";
import { useAuthStore } from "@/modules/auth/store/authStore";

const http = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "",
    withCredentials: true,
    withXSRFToken: true,
    timeout: 10000,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
    },
});

http.interceptors.request.use((config) => {
    const auth = useAuthStore();
    if (auth.token) config.headers.Authorization = `Bearer ${auth.token}`;

    const xsrf = document.cookie
        .split("; ")
        .find((row) => row.startsWith("XSRF-TOKEN="));
    if (xsrf) {
        config.headers["X-XSRF-TOKEN"] = decodeURIComponent(
            xsrf.split("=")[1],
        );
    }

    return config;
});

http.interceptors.response.use(
    (res) => res,
    (err) => {
        const auth = useAuthStore();
        const status = err?.response?.status;
        const url = err?.config?.url || "";

        if (status === 401 && !url.includes("/login") && !url.includes("/sanctum/csrf-cookie")) {
            auth.hardLogout();
        }

        return Promise.reject(err);
    },
);

export default http;
