import { storeToRefs } from "pinia";
import { useAuthStore } from "../../auth/store/authStore";

export function useRole() {
    const authStore = useAuthStore();
    const { user } = storeToRefs(authStore);

    const can = (permission) => {
        const role = user.value?.role;

        const rules = {
            admin: [
                "create",
                "edit",
                "delete",
                "moderate",
                "admin-panel",
                "sell",
                "delete-comments",
            ],
            gerent: [
                "create",
                "edit",
                "delete",
                "moderate",
                "admin-panel",
                "sell",
            ],
            venedor: ["sell", "create", "edit"],
            editor: ["moderate"],
            user: ["read", "comment"],
        };

        // Si no hay role, return false
        if (!role) return false;

        // Admin y Gerent tienen acceso a todo
        if (role === "admin" || role === "gerent") return true;

        return rules[role]?.includes(permission) ?? false;
    };

    const is = (roleName) => {
        return user.value?.role === roleName;
    };

    return { can, is };
}
