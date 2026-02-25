import http from "@/services/http";

export const apiLogin = async (credentials) => {
    await http.get("/sanctum/csrf-cookie");
    return http.post("/login", credentials);
};

export const apiRegister = async (userData) => {
    await http.get("/sanctum/csrf-cookie");
    return http.post("/api/register", userData);
};

export const apiLogout = () => {
    return http.post("/logout");
};

export const apiGetUser = async () => {
    const { data } = await http.get("/api/user");
    return data;
};
