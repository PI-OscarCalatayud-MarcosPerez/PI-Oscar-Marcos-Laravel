import { storeToRefs } from 'pinia';
import { useAuthStore } from '../../auth/store/authStore';

export function useRole() {
    const authStore = useAuthStore();
    const { user } = storeToRefs(authStore);

    const can = (permission) => {
        const role = user.value?.role;
        // Simple logic: admin has all permissions. 
        // This should match the table in C3.
        const rules = {
            admin: ['create', 'edit', 'delete', 'moderate', 'admin-panel'],
            gerent: ['create', 'edit', 'delete', 'moderate', 'admin-panel'],
            venedor: ['create', 'edit', 'delete'],
            editor: ['moderate'],
            user: ['read', 'comment']
        };

        // If role is missing, default to empty
        if (!role) return false;

        // Admin/Gerent access all
        if (role === 'admin' || role === 'gerent') return true;

        return rules[role]?.includes(permission) ?? false;
    };

    const is = (roleName) => {
        return user.value?.role === roleName;
    }

    return { can, is };
}
