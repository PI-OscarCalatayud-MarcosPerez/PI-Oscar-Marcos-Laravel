import axios from "axios";
import { useAuthStore } from "@/modules/auth/store/authStore";

const http = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "",
    withCredentials: true,
    timeout: 8000,
});

http.interceptors.request.use((config) => {
    const auth = useAuthStore();
    if (auth.token) config.headers.Authorization = `Bearer ${auth.token}`;
    return config;
});

http.interceptors.response.use(
    (res) => res,
    (err) => {
        const auth = useAuthStore();
        if (err?.response?.status === 401) auth.hardLogout();
        return Promise.reject(err);
    },
);

export default http;
