<script setup>
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useRouter } from 'vue-router'
import { useRole } from '@/modules/roles/composables/useRole'
import RoleBadge from '@/modules/roles/components/RoleBadge.vue'
import UiToast from '@/components/UiToast.vue'

const auth = useAuthStore()
const router = useRouter()
const { hasRole } = useRole()

const handleLogout = async () => {
    await auth.logout()
    router.push('/login')
}

const getRoleLabel = (role) => {
    const labels = {
        admin: 'Administrador',
        gerent: 'Gerente',
        venedor: 'Vendedor',
        editor: 'Editor',
        user: 'Usuario'
    }
    return labels[role] || 'Usuario'
}
</script>

<template>
    <UiToast />
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h1 class="profile-name">{{ auth.user?.name || 'Usuario' }}</h1>
                <RoleBadge :role="auth.user?.role" />
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <label>📧 Email</label>
                    <p>{{ auth.user?.email || 'No disponible' }}</p>
                </div>

                <div class="info-item" v-if="auth.user?.last_name">
                    <label>👤 Apellidos</label>
                    <p>{{ auth.user?.last_name }}</p>
                </div>

                <div class="info-item" v-if="auth.user?.username">
                    <label>🔖 Usuario</label>
                    <p>{{ auth.user?.username }}</p>
                </div>
            </div>

            <div class="profile-actions">
                <button v-if="hasRole('venedor', 'admin', 'gerent')" class="btn-action btn-upload"
                    @click="router.push('/import')">
                    <img src="/img/boton-circular-plus.png" alt="Subir" class="action-icon" />
                    <span>Subir Productos</span>
                </button>

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
