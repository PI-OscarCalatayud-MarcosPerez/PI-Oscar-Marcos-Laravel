import axios from "axios";

const http = axios.create({
    baseURL: "http://localhost", // Basic URL, avoiding advanced proxies if possible, or just let empty if using Proxy
    withCredentials: true,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// Request interceptor
http.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response interceptor
http.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            // Handle unauthorized (e.g., logout)
            localStorage.removeItem("token");
            // Optional: redirect to login
        }
        return Promise.reject(error);
    },
);

export default http;
