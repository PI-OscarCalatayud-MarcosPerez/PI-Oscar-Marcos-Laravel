<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useRouter } from 'vue-router'
import { useRole } from '@/modules/roles/composables/useRole'
import RoleBadge from '@/modules/roles/components/RoleBadge.vue'
import UiToast from '@/components/UiToast.vue'

const auth = useAuthStore()
const router = useRouter()
const { hasRole } = useRole()

// Sección activa del menú
const activeSection = ref('info')

// Formulario de edición
const editForm = ref({
    name: auth.user?.name || '',
    last_name: auth.user?.last_name || '',
    username: auth.user?.username || '',
})
const saving = ref(false)
const saveMessage = ref('')

// Estado para eliminar cuenta
const deleteConfirm = ref('')
const deleting = ref(false)

const handleLogout = async () => {
    await auth.logout()
    router.push('/login')
}

const handleSaveProfile = async () => {
    saving.value = true
    saveMessage.value = ''
    try {
        await auth.updateProfile(editForm.value)
        saveMessage.value = '✓ Datos guardados correctamente'
        // Actualizar formulario con datos del servidor
        editForm.value.name = auth.user?.name || ''
        editForm.value.last_name = auth.user?.last_name || ''
        editForm.value.username = auth.user?.username || ''
    } catch (err) {
        saveMessage.value = '✗ Error al guardar los datos'
        console.error(err)
    } finally {
        saving.value = false
        setTimeout(() => saveMessage.value = '', 3000)
    }
}

const handleDeleteAccount = async () => {
    if (deleteConfirm.value !== 'ELIMINAR') return
    deleting.value = true
    try {
        await auth.deleteAccount()
        router.push('/login')
    } catch (err) {
        console.error(err)
        deleting.value = false
    }
}

const canDelete = computed(() => deleteConfirm.value === 'ELIMINAR')
</script>

<template>
    <UiToast />
    <div class="profile-page">
        <div class="profile-layout">
            <!-- Sidebar -->
            <aside class="profile-sidebar">
                <div class="sidebar-avatar">
                    <div class="avatar-circle">
                        {{ (auth.user?.name || 'U').charAt(0).toUpperCase() }}
                    </div>
                    <h3 class="sidebar-name">{{ auth.user?.name || 'Usuario' }}</h3>
                    <RoleBadge :role="auth.user?.role" />
                </div>

                <nav class="sidebar-menu">
                    <button class="menu-item" :class="{ active: activeSection === 'info' }"
                        @click="activeSection = 'info'">
                        <img src="/img/estrella_roja.png" alt="" class="menu-icon" />
                        <span>Mi Perfil</span>
                    </button>

                    <button class="menu-item" :class="{ active: activeSection === 'edit' }"
                        @click="activeSection = 'edit'">
                        <img src="/img/lapiz.png" alt="" class="menu-icon" />
                        <span>Editar Datos</span>
                    </button>

                    <button v-if="hasRole('venedor', 'admin', 'gerent')" class="menu-item"
                        @click="router.push('/import')">
                        <img src="/img/boton-circular-plus2.png" alt="" class="menu-icon" />
                        <span>Subir Productos</span>
                    </button>

                    <button class="menu-item" :class="{ active: activeSection === 'delete' }"
                        @click="activeSection = 'delete'">
                        <img src="/img/borrar2.png" alt="" class="menu-icon" />
                        <span>Eliminar Cuenta</span>
                    </button>

                    <div class="menu-divider"></div>

                    <button class="menu-item menu-item-logout" @click="handleLogout">
                        <img src="/img/puerta-abierta2.png" alt="" class="menu-icon" />
                        <span>Cerrar Sesión</span>
                    </button>
                </nav>
            </aside>

            <!-- Contenido principal -->
            <main class="profile-content">

                <!-- Sección: Mi Perfil -->
                <section v-if="activeSection === 'info'" class="content-section">
                    <h2 class="section-title">Mi Perfil</h2>
                    <p class="section-subtitle">Información de tu cuenta</p>

                    <div class="info-grid">
                        <div class="info-card">
                            <img src="/img/usuario.png" alt="Nombre" class="info-card-icon" />
                            <div>
                                <label>Nombre</label>
                                <p>{{ auth.user?.name || 'No disponible' }}</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <img src="/img/usuario1.png" alt="Apellidos" class="info-card-icon" />
                            <div>
                                <label>Apellidos</label>
                                <p>{{ auth.user?.last_name || 'No disponible' }}</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <img src="/img/sobre_rojo.png" alt="Email" class="info-card-icon" />
                            <div>
                                <label>Email</label>
                                <p>{{ auth.user?.email || 'No disponible' }}</p>
                            </div>
                        </div>

                        <div class="info-card" v-if="auth.user?.username">
                            <img src="/img/estrella_roja.png" alt="Usuario" class="info-card-icon" />
                            <div>
                                <label>Nombre de usuario</label>
                                <p>{{ auth.user?.username }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sección: Editar Datos -->
                <section v-if="activeSection === 'edit'" class="content-section">
                    <h2 class="section-title">Editar Datos</h2>
                    <p class="section-subtitle">Modifica tu información personal</p>

                    <form @submit.prevent="handleSaveProfile" class="edit-form">
                        <div class="form-group">
                            <label for="edit-name">Nombre</label>
                            <input type="text" id="edit-name" v-model="editForm.name" placeholder="Tu nombre" />
                        </div>

                        <div class="form-group">
                            <label for="edit-last-name">Apellidos</label>
                            <input type="text" id="edit-last-name" v-model="editForm.last_name"
                                placeholder="Tus apellidos" />
                        </div>

                        <div class="form-group">
                            <label for="edit-username">Nombre de usuario</label>
                            <input type="text" id="edit-username" v-model="editForm.username"
                                placeholder="Tu nombre de usuario" />
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save" :disabled="saving">
                                {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                            <span v-if="saveMessage" class="save-feedback"
                                :class="{ success: saveMessage.includes('✓'), error: saveMessage.includes('✗') }">
                                {{ saveMessage }}
                            </span>
                        </div>
                    </form>
                </section>

                <!-- Sección: Eliminar Cuenta -->
                <section v-if="activeSection === 'delete'" class="content-section">
                    <h2 class="section-title section-title-danger">Eliminar Cuenta</h2>
                    <p class="section-subtitle">Esta acción es irreversible</p>

                    <div class="danger-zone">
                        <div class="danger-warning">
                            <img src="/img/borrar2.png" alt="" class="danger-icon" />
                            <div>
                                <h4>¿Estás seguro?</h4>
                                <p>Al eliminar tu cuenta se borrarán todos tus datos, reseñas y compras. Esta acción
                                    <strong>no se puede deshacer</strong>.
                                </p>
                            </div>
                        </div>

                        <div class="confirm-delete">
                            <label>Escribe <strong>ELIMINAR</strong> para confirmar:</label>
                            <input type="text" v-model="deleteConfirm" placeholder="ELIMINAR" class="delete-input" />
                            <button class="btn-delete" :disabled="!canDelete || deleting" @click="handleDeleteAccount">
                                {{ deleting ? 'Eliminando...' : 'Eliminar mi cuenta permanentemente' }}
                            </button>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/profile.css';
</style>
