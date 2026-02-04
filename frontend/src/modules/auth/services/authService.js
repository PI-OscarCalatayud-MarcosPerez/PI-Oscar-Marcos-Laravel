import http from "@/services/http";

export default {
    login(credentials) {
        return http.get("/sanctum/csrf-cookie").then(() => {
            return http.post("/login", credentials);
        });
    },
    register(userData) {
        return http.get("/sanctum/csrf-cookie").then(() => {
            return http.post("/api/register", userData);
        });
    },
    logout() {
        return http.post("/logout");
    },
    getUser() {
        return http.get("/user");
    },
};
