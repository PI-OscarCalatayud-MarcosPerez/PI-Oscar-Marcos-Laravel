import { storeToRefs } from "pinia";
import { useAuthStore } from "@/modules/auth/store/authStore";

const RULES = {
    admin: [
        "create",
        "edit",
        "delete",
        "moderate",
        "manage_users",
        "delete-comments",
    ],
    gerent: ["create", "edit", "delete", "moderate", "sell"],
    venedor: ["create", "edit", "sell"],
    editor: ["moderate", "edit"],
    user: ["read", "comment"],
};

export function useRole() {
    const { user } = storeToRefs(useAuthStore());

    const can = (permission) => {
        const role = user.value?.role ?? "user";
        return RULES[role]?.includes(permission) ?? false;
    };

    const hasRole = (...roles) => roles.includes(user.value?.role);

    return { can, hasRole };
}
