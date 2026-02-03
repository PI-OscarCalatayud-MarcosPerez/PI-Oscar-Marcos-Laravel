import axios from 'axios';

const http = axios.create({
    baseURL: '/api', // Proxy in vite.config.js handles this to http://localhost:8000/api or http://localhost/api
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Request interceptor
http.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response interceptor
http.interceptors.response.use(response => {
    return response;
}, error => {
    if (error.response && error.response.status === 401) {
        // Handle unauthorized (e.g., logout)
        localStorage.removeItem('token');
        // Optional: redirect to login
    }
    return Promise.reject(error);
});

export default http;
