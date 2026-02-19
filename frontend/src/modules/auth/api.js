import http from "@/services/http";

export async function apiLogin(credentials) {
    const { data } = await http.post("/login", credentials);
    return data;
}

export async function apiLogout() {
    await http.post("/logout");
}

export async function apiGetUser() {
    const { data } = await http.get("/api/user");
    return data;
}
