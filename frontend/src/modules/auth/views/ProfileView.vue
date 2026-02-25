<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useRouter } from 'vue-router'
import { useRole } from '@/modules/roles/composables/useRole'
import RoleBadge from '@/modules/roles/components/RoleBadge.vue'
import UiToast from '@/components/UiToast.vue'
import ProductService from '@/modules/products/services/ProductService'
import http from '@/services/http'

const auth = useAuthStore()
const router = useRouter()
const { hasRole } = useRole()

const activeSection = ref('info')

const editForm = ref({
    name: auth.user?.name || '',
    last_name: auth.user?.last_name || '',
    username: auth.user?.username || '',
})
const saving = ref(false)
const saveMessage = ref('')

const deleteConfirm = ref('')
const deleting = ref(false)

const adminProducts = ref([])
const adminLoading = ref(false)
const adminSearch = ref('')
const adminMessage = ref({ type: '', text: '' })

const editingProduct = ref(null)
const editProductForm = ref({})
const editSaving = ref(false)

const codesModal = ref(null)
const codesData = ref(null)
const codesLoading = ref(false)
const newCodesText = ref('')
const addingCodes = ref(false)
const codesRevealed = ref({})

const importFile = ref(null)
const importLoading = ref(false)
const importResult = ref(null)
const importError = ref(null)

const filteredAdminProducts = computed(() => {
    if (!adminSearch.value) return adminProducts.value
    const q = adminSearch.value.toLowerCase()
    return adminProducts.value.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        p.sku?.toLowerCase().includes(q) ||
        p.plataforma?.toLowerCase().includes(q)
    )
})

const fetchAdminProducts = async () => {
    adminLoading.value = true
    try {
        const response = await ProductService.getAdminProducts()
        adminProducts.value = response.data
    } catch (err) {
        console.error(err)
        adminMessage.value = { type: 'error', text: 'Error al cargar productos' }
    } finally {
        adminLoading.value = false
    }
}

const openEditModal = (product) => {
    editingProduct.value = product
    editProductForm.value = {
        nombre: product.nombre,
        descripcion: product.descripcion || '',
        precio: product.precio,
        porcentaje_descuento: product.porcentaje_descuento || 0,
        plataforma: product.plataforma || '',
        categoria: product.categoria || '',
        category_id: product.category_id,
        is_eco: product.is_eco || false,
        imagen_url: product.imagen_url || '',
    }
}

const closeEditModal = () => {
    editingProduct.value = null
    editProductForm.value = {}
}

const saveProduct = async () => {
    editSaving.value = true
    try {
        await ProductService.updateProduct(editingProduct.value.id, editProductForm.value)
        adminMessage.value = { type: 'success', text: 'Producto actualizado correctamente' }
        closeEditModal()
        await fetchAdminProducts()
        setTimeout(() => adminMessage.value = { type: '', text: '' }, 3000)
    } catch (err) {
        console.error(err)
        adminMessage.value = { type: 'error', text: 'Error al actualizar el producto' }
    } finally {
        editSaving.value = false
    }
}

const openCodesModal = async (product) => {
    codesModal.value = product
    codesLoading.value = true
    codesRevealed.value = {}
    newCodesText.value = ''
    try {
        const response = await ProductService.getProductCodes(product.id)
        codesData.value = response.data
    } catch (err) {
        console.error(err)
    } finally {
        codesLoading.value = false
    }
}

const closeCodesModal = () => {
    codesModal.value = null
    codesData.value = null
}

const toggleCodeVisibility = (codeId) => {
    codesRevealed.value[codeId] = !codesRevealed.value[codeId]
}

const addNewCodes = async () => {
    if (!newCodesText.value.trim()) return
    addingCodes.value = true
    try {
        const codes = newCodesText.value.split('\n').map(c => c.trim()).filter(c => c.length > 0)
        if (codes.length === 0) return
        await ProductService.addProductCodes(codesModal.value.id, codes)
        adminMessage.value = { type: 'success', text: `${codes.length} código(s) añadido(s)` }
        newCodesText.value = ''
        const response = await ProductService.getProductCodes(codesModal.value.id)
        codesData.value = response.data
        await fetchAdminProducts()
        setTimeout(() => adminMessage.value = { type: '', text: '' }, 3000)
    } catch (err) {
        console.error(err)
        const msg = err.response?.data?.message || 'Error al añadir códigos'
        adminMessage.value = { type: 'error', text: msg }
    } finally {
        addingCodes.value = false
    }
}

const handleFileUpload = (event) => {
    importFile.value = event.target.files[0]
    importResult.value = null
    importError.value = null
}

const submitImportFile = async () => {
    if (!importFile.value) return

    importLoading.value = true
    const formData = new FormData()
    formData.append('arxiuCsv', importFile.value)

    try {
        const response = await http.post('/api/import', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        importResult.value = response.data
    } catch (err) {
        console.error(err)
        if (err.response && err.response.data) {
            importError.value = err.response.data.message || 'Error al subir el archivo.'
            if (err.response.data.errors) {
                importResult.value = { errors: Object.values(err.response.data.errors).flat() }
            }
        } else {
            importError.value = 'Ocurrió un error inesperado.'
        }
    } finally {
        importLoading.value = false
    }
}

const getStockClass = (stock) => {
    if (stock === 0) return 'stock-empty'
    if (stock <= 3) return 'stock-low'
    return 'stock-ok'
}

const getProductImage = (product) => {
    let img = product.imagen_url
    if (!img || img === '') return 'https://placehold.co/45x45/1a1a2e/ffffff?text=M'
    if (!img.startsWith('http') && !img.startsWith('data:')) {
        if (img.startsWith('/')) img = img.substring(1)
        if (!img.startsWith('img/')) img = 'img/' + img
        img = '/' + img
    }
    return img
}

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

onMounted(() => {
    if (hasRole('admin')) {
        fetchAdminProducts()
    }
})
</script>

<template>
    <UiToast />
    <div class="profile-page">
        <div class="profile-layout">
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

                    <button v-if="hasRole('admin')" class="menu-item"
                        :class="{ active: activeSection === 'admin-products' }"
                        @click="activeSection = 'admin-products'; fetchAdminProducts()">
                        <img src="/img/carrito1.png" alt="" class="menu-icon" />
                        <span>Gestión Productos</span>
                    </button>

                    <button v-if="hasRole('venedor', 'admin', 'gerent')" class="menu-item"
                        :class="{ active: activeSection === 'import' }" @click="activeSection = 'import'">
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

            <main class="profile-content">

                <section v-if="activeSection === 'info'" class="content-section">
                    <h2 class="section-title">Mi Perfil</h2>
                    <p class="section-subtitle">Información de tu cuenta</p>

                    <div class="info-grid">
                        <div class="info-card">
                            <img src="/img/usuario1.png" alt="Nombre" class="info-card-icon" />
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

                <section v-if="activeSection === 'admin-products'" class="content-section admin-products-section">
                    <h2 class="section-title">Gestión de Productos</h2>
                    <p class="section-subtitle">Editar productos, ofertas, precios y stock</p>

                    <div v-if="adminMessage.text"
                        :class="adminMessage.type === 'success' ? 'admin-msg-success' : 'admin-msg-error'">
                        {{ adminMessage.text }}
                    </div>

                    <div class="admin-products-header">
                        <input type="text" v-model="adminSearch" placeholder="Buscar producto..."
                            class="admin-search-input" />
                        <span style="color: #888; font-size: 13px;">{{ filteredAdminProducts.length }} productos</span>
                    </div>

                    <div v-if="adminLoading" style="text-align: center; padding: 40px; color: #888;">
                        Cargando productos...
                    </div>

                    <div v-else class="table-responsive-admin">
                        <table class="admin-products-table">
                            <thead>
                                <tr>
                                    <th>Img</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Descuento</th>
                                    <th>Plataforma</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in filteredAdminProducts" :key="product.id">
                                    <td>
                                        <img :src="getProductImage(product)" :alt="product.nombre"
                                            class="admin-product-img" />
                                    </td>
                                    <td>
                                        <strong>{{ product.nombre }}</strong>
                                        <br />
                                        <small style="color: #888;">{{ product.sku }}</small>
                                    </td>
                                    <td>
                                        <template v-if="product.porcentaje_descuento > 0">
                                            <span class="price-original-admin">{{ parseFloat(product.precio).toFixed(2)
                                                }}€</span>
                                            <br />
                                            <span class="price-final-admin" style="color: #c62828;">
                                                {{ (parseFloat(product.precio) * (1 - product.porcentaje_descuento /
                                                    100)).toFixed(2) }}€
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="price-final-admin">{{ parseFloat(product.precio).toFixed(2)
                                                }}€</span>
                                        </template>
                                    </td>
                                    <td>
                                        <span v-if="product.porcentaje_descuento > 0" class="discount-badge-admin">
                                            -{{ product.porcentaje_descuento }}%
                                        </span>
                                        <span v-else style="color: #aaa;">—</span>
                                    </td>
                                    <td>{{ product.plataforma || '—' }}</td>
                                    <td>
                                        <span class="stock-badge" :class="getStockClass(product.stock)">
                                            {{ product.stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-actions-cell">
                                            <button class="btn-admin-action btn-admin-edit"
                                                @click="openEditModal(product)" title="Editar producto">
                                                <img src="/img/lapiz.png" alt="Editar" class="admin-action-icon" />
                                            </button>
                                            <button class="btn-admin-action btn-admin-keys"
                                                @click="openCodesModal(product)" title="Ver claves">
                                                <img src="/img/llave.png" alt="Claves" class="admin-action-icon" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="filteredAdminProducts.length === 0" class="no-products-admin">
                            No se encontraron productos
                        </div>
                    </div>
                </section>

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

                <section v-if="activeSection === 'import'" class="content-section">
                    <h2 class="section-title">Importación de Productos</h2>
                    <p class="section-subtitle">Sube un archivo CSV para actualizar el catálogo. (Columnas: id, nombre,
                        descripcion,
                        precio, img, estoc, categoria)</p>

                    <div
                        style="max-width: 500px; background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #e0e0e0; margin-top: 20px;">
                        <label for="csvFile"
                            style="display:block; font-size:13px; font-weight:600; color:#0e273f; text-transform:uppercase; margin-bottom:10px;">Seleccionar
                            archivo CSV</label>
                        <input type="file" id="csvFile" accept=".csv" @change="handleFileUpload"
                            style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; background: white; margin-bottom: 20px;" />

                        <button @click="submitImportFile" :disabled="!importFile || importLoading"
                            style="width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; background: linear-gradient(135deg, #2e7d32, #388e3c); color: white;">
                            {{ importLoading ? 'Procesando archivo...' : 'Importar Catálogo' }}
                        </button>
                    </div>

                    <div v-if="importError"
                        style="margin-top: 20px; padding: 15px; border-radius: 8px; background: #ffebee; border: 1px solid #ffcdd2; color: #c62828;">
                        {{ importError }}
                    </div>

                    <div v-if="importResult" style="margin-top: 20px;">
                        <div v-if="importResult.messages && importResult.messages.length"
                            style="padding: 15px; border-radius: 8px; background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; margin-bottom: 10px;">
                            <ul style="margin:0; padding-left: 20px;">
                                <li v-for="(msg, index) in importResult.messages" :key="index">{{ msg }}</li>
                            </ul>
                        </div>
                        <div v-if="importResult.errors && importResult.errors.length"
                            style="padding: 15px; border-radius: 8px; background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; margin-bottom: 10px;">
                            <strong>Errores Críticos:</strong>
                            <ul style="margin:5px 0 0; padding-left: 20px;">
                                <li v-for="(err, index) in importResult.errors" :key="index">{{ err }}</li>
                            </ul>
                        </div>
                        <div v-if="importResult.row_warnings && importResult.row_warnings.length"
                            style="padding: 15px; border-radius: 8px; background: #fff3e0; border: 1px solid #ffe0b2; color: #e65100;">
                            <strong>Advertencias (Filas ignoradas):</strong>
                            <ul style="margin:5px 0 0; padding-left: 20px; max-height: 200px; overflow-y: auto;">
                                <li v-for="(warn, index) in importResult.row_warnings" :key="index">{{ warn }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <div v-if="editingProduct" class="modal-overlay" @click.self="closeEditModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <img src="/img/lapiz.png" alt="" style="width: 24px; height: 24px; object-fit: contain;" />
                Editar Producto
            </h3>

            <div class="modal-form-group">
                <label>Nombre</label>
                <input type="text" v-model="editProductForm.nombre" />
            </div>

            <div class="modal-form-group">
                <label>Descripción</label>
                <textarea v-model="editProductForm.descripcion"></textarea>
            </div>

            <div class="modal-form-row">
                <div class="modal-form-group">
                    <label>Precio (€)</label>
                    <input type="number" step="0.01" min="0" v-model="editProductForm.precio" />
                </div>
                <div class="modal-form-group">
                    <label>Descuento (%)</label>
                    <input type="number" min="0" max="100" v-model="editProductForm.porcentaje_descuento" />
                </div>
            </div>

            <div class="modal-form-row">
                <div class="modal-form-group">
                    <label>Plataforma</label>
                    <select v-model="editProductForm.plataforma">
                        <option value="">Sin plataforma</option>
                        <option value="PC">PC</option>
                        <option value="PS5">PS5</option>
                        <option value="Xbox">Xbox</option>
                        <option value="Switch">Switch</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Eco-Friendly</label>
                    <select v-model="editProductForm.is_eco">
                        <option :value="false">No</option>
                        <option :value="true">Sí</option>
                    </select>
                </div>
            </div>

            <div class="modal-form-group">
                <label>URL de Imagen</label>
                <input type="text" v-model="editProductForm.imagen_url" />
            </div>

            <div class="modal-actions">
                <button class="btn-modal-cancel" @click="closeEditModal">Cancelar</button>
                <button class="btn-modal-save" :disabled="editSaving" @click="saveProduct">
                    {{ editSaving ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
            </div>
        </div>
    </div>

    <div v-if="codesModal" class="modal-overlay" @click.self="closeCodesModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <img src="/img/llave.png" alt="" style="width: 24px; height: 24px; object-fit: contain;" />
                Claves — {{ codesModal.nombre }}
            </h3>

            <div v-if="codesLoading" style="text-align: center; padding: 30px; color:#888;">
                Cargando claves...
            </div>

            <template v-else-if="codesData">
                <div class="codes-summary">
                    <div class="codes-summary-item">
                        <span class="number">{{ codesData.total }}</span>
                        <span class="label">Total</span>
                    </div>
                    <div class="codes-summary-item">
                        <span class="number" style="color: #2e7d32;">{{ codesData.available }}</span>
                        <span class="label">Disponibles</span>
                    </div>
                    <div class="codes-summary-item">
                        <span class="number" style="color: #c62828;">{{ codesData.sold }}</span>
                        <span class="label">Vendidas</span>
                    </div>
                </div>

                <div class="codes-list" v-if="codesData.codes.length > 0">
                    <div v-for="code in codesData.codes" :key="code.id" class="code-item"
                        :class="code.is_sold ? 'code-sold' : 'code-available'">
                        <span @click="toggleCodeVisibility(code.id)"
                            :class="codesRevealed[code.id] ? 'code-visible' : 'code-hidden'" style="cursor: pointer;"
                            :title="codesRevealed[code.id] ? '' : 'Clic para ver la clave'">
                            {{ code.code }}
                        </span>
                        <span class="code-status" :style="{ color: code.is_sold ? '#c62828' : '#2e7d32' }">
                            {{ code.is_sold ? 'Vendida' : 'Disponible' }}
                        </span>
                    </div>
                </div>
                <div v-else style="text-align: center; color: #888; padding: 20px;">
                    No hay claves registradas
                </div>

                <div class="add-codes-area">
                    <h4
                        style="margin: 0 0 10px; color: #0e273f; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <img src="/img/plus-pequeno.png" alt=""
                            style="width: 16px; height: 16px; object-fit: contain;" />
                        Añadir Nuevas Claves
                    </h4>
                    <textarea v-model="newCodesText" placeholder="Introduce las claves, una por línea..."></textarea>
                    <p class="add-codes-hint">Escribe una clave por línea. Ejemplo: ABCD-1234-EFGH</p>
                    <button class="btn-add-codes" :disabled="addingCodes || !newCodesText.trim()" @click="addNewCodes">
                        {{ addingCodes ? 'Añadiendo...' : 'Añadir Claves al Stock' }}
                    </button>
                </div>
            </template>

            <div class="modal-actions">
                <button class="btn-modal-cancel" @click="closeCodesModal">Cerrar</button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/profile.css';
@import '../../../assets/css/admin-products.css';
</style>
