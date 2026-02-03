import http from "@/services/http";

export default {
    login(credentials) {
        return http.get("/sanctum/csrf-cookie", { baseURL: "/" }).then(() => {
            return http.post("/login", credentials, { baseURL: "/" });
        });
    },
    register(userData) {
        return http.get("/sanctum/csrf-cookie", { baseURL: "/" }).then(() => {
            return http.post("/register", userData);
        });
    },
    logout() {
        return http.post("/logout", {}, { baseURL: "/" });
    },
    getUser() {
        return http.get("/user");
    },
};
