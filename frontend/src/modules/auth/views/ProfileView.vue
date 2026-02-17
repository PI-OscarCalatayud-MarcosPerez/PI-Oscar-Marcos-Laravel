<script setup>
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';
import { useRole } from '../../roles/composables/useRole';

const authStore = useAuthStore();
const router = useRouter();
const { is } = useRole();

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
            <!-- Nombre grande arriba -->
            <div class="profile-header">
                <h1 class="profile-name">{{ authStore.user?.name || 'Usuario' }}</h1>
                <span :class="['role-badge', getRoleBadgeClass(authStore.user?.role)]">
                    {{ getRoleLabel(authStore.user?.role) }}
                </span>
            </div>

            <!-- Información del usuario -->
            <div class="profile-info">
                <div class="info-item">
                    <label>📧 Email</label>
                    <p>{{ authStore.user?.email || 'No disponible' }}</p>
                </div>

                <div class="info-item" v-if="authStore.user?.last_name">
                    <label>👤 Apellidos</label>
                    <p>{{ authStore.user?.last_name }}</p>
                </div>

                <div class="info-item" v-if="authStore.user?.username">
                    <label>🔖 Usuario</label>
                    <p>{{ authStore.user?.username }}</p>
                </div>
            </div>

            <!-- Acciones según rol -->
            <div class="profile-actions">
                <!-- Vendedor/Admin: Subir productos -->
                <button v-if="is('venedor') || is('admin')" class="btn-action btn-upload"
                    @click="router.push('/import')">
                    <img src="/img/boton-circular-plus.png" alt="Subir" class="action-icon" />
                    <span>Subir Productos</span>
                </button>

                <!-- Todos: Cerrar sesión -->
                <button class="btn-action btn-logout" @click="handleLogout">
                    <img src="/img/puerta-abierta.png" alt="Salir" class="action-icon" />
                    <span>Cerrar Sesión</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/profile.css';
</style>
