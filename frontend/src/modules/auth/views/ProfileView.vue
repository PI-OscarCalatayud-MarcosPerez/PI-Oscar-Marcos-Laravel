<script setup>
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';
import RoleGuard from '../../roles/components/RoleGuard.vue';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

const getRoleBadgeClass = (role) => {
    const classes = {
        admin: 'badge-admin',
        gerent: 'badge-admin',
        venedor: 'badge-venedor',
        editor: 'badge-editor',
        user: 'badge-user'
    };
    return classes[role] || 'badge-user';
};

const getRoleLabel = (role) => {
    const labels = {
        admin: 'Administrador',
        gerent: 'Gerente',
        venedor: 'Vendedor',
        editor: 'Editor',
        user: 'Usuario'
    };
    return labels[role] || 'Usuario';
};
</script>

<template>
    <div class="profile-container">
        <div class="profile-card">
            <h2>Mi Perfil</h2>

            <div class="profile-info">
                <div class="info-item">
                    <label>Nombre:</label>
                    <p>{{ authStore.user?.name || 'Usuario' }}</p>
                </div>

                <div class="info-item">
                    <label>Email:</label>
                    <p>{{ authStore.user?.email || 'No disponible' }}</p>
                </div>

                <div class="info-item">
                    <label>Rol:</label>
                    <span :class="['role-badge', getRoleBadgeClass(authStore.user?.role)]">
                        {{ getRoleLabel(authStore.user?.role) }}
                    </span>
                </div>
            </div>

            <div class="profile-actions">
                <!-- Opciones específicas según rol -->
                <RoleGuard permission="admin-panel">
                    <button class="btn-admin" @click="router.push('/import')">
                        🔧 Panel de Administración
                    </button>
                </RoleGuard>

                <RoleGuard permission="create">
                    <button class="btn-secondary" @click="router.push('/import')">
                        ➕ Subir Productos
                    </button>
                </RoleGuard>

                <button class="btn-logout" @click="handleLogout">
                    🚪 Cerrar Sesión
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 20px;
}

.profile-card {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(14, 39, 63, 0.15);
    width: 100%;
    max-width: 500px;
}

.profile-card h2 {
    text-align: center;
    color: #0e273f;
    margin-top: 0;
    margin-bottom: 30px;
}

.profile-info {
    margin-bottom: 30px;
}

.info-item {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item label {
    font-weight: bold;
    color: #0e273f;
    font-size: 0.9rem;
}

.info-item p {
    color: #555;
    margin: 0;
    font-size: 1rem;
}

.role-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 0.9rem;
    text-align: center;
    max-width: fit-content;
}

.badge-admin {
    background-color: #ff6b6b;
    color: white;
}

.badge-venedor {
    background-color: #4ecdc4;
    color: white;
}

.badge-editor {
    background-color: #95e1d3;
    color: #0e273f;
}

.badge-user {
    background-color: #e0e0e0;
    color: #0e273f;
}

.profile-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.profile-actions button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-admin {
    background-color: #fa4841;
    color: white;
}

.btn-admin:hover {
    background-color: #d63a34;
}

.btn-secondary {
    background-color: #4ecdc4;
    color: white;
}

.btn-secondary:hover {
    background-color: #3bb3ab;
}

.btn-logout {
    background-color: #6c757d;
    color: white;
}

.btn-logout:hover {
    background-color: #5a6268;
}
</style>
