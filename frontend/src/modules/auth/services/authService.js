import http from "@/services/http";

export const apiLogin = (credentials) => {
    return http.post("/login", credentials);
};

export const apiRegister = (userData) => {
    return http.post("/api/register", userData);
};

export const apiLogout = () => {
    return http.post("/logout");
};

export const apiGetUser = () => {
    return http.get("/user");
};
