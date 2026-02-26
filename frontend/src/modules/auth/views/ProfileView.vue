<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/modules/auth/store/authStore'
import { useRouter } from 'vue-router'
import { useRole } from '@/modules/roles/composables/useRole'
import { usePrefsStore } from '@/stores/prefsStore'
import RoleBadge from '@/modules/roles/components/RoleBadge.vue'
import UiToast from '@/components/UiToast.vue'
import ProductService from '@/modules/products/services/ProductService'
import http from '@/services/http'

const auth = useAuthStore()
const router = useRouter()
const { hasRole } = useRole()
const prefs = usePrefsStore()
const t = computed(() => prefs.t)

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

const adminReviews = ref([])
const reviewsLoading = ref(false)
const reviewSearch = ref('')

const purchases = ref([])
const purchasesLoading = ref(false)
const purchaseCodesRevealed = ref({})

const salesData = ref(null)
const salesLoading = ref(false)

const filteredAdminProducts = computed(() => {
    if (!adminSearch.value) return adminProducts.value
    const q = adminSearch.value.toLowerCase()
    return adminProducts.value.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        p.sku?.toLowerCase().includes(q) ||
        p.plataforma?.toLowerCase().includes(q)
    )
})

const filteredReviews = computed(() => {
    if (!reviewSearch.value) return adminReviews.value
    const q = reviewSearch.value.toLowerCase()
    return adminReviews.value.filter(r =>
        r.user?.name?.toLowerCase().includes(q) ||
        r.user?.email?.toLowerCase().includes(q) ||
        r.product?.nombre?.toLowerCase().includes(q) ||
        r.comentario?.toLowerCase().includes(q)
    )
})

const fetchAdminProducts = async () => {
    adminLoading.value = true
    try {
        const response = await ProductService.getAdminProducts()
        adminProducts.value = response.data
    } catch (err) {
        console.error(err)
        adminMessage.value = { type: 'error', text: t.value.admin.errorLoad }
    } finally {
        adminLoading.value = false
    }
}

const fetchAdminReviews = async () => {
    reviewsLoading.value = true
    try {
        const response = await http.get('/api/admin/reviews')
        adminReviews.value = response.data
    } catch (err) {
        console.error(err)
    } finally {
        reviewsLoading.value = false
    }
}

const deleteReview = async (id) => {
    if (!confirm(t.value.reviews.confirmDelete)) return
    try {
        await http.delete(`/api/admin/reviews/${id}`)
        adminReviews.value = adminReviews.value.filter(r => r.id !== id)
    } catch (err) {
        console.error(err)
    }
}

const fetchPurchases = async () => {
    purchasesLoading.value = true
    try {
        const response = await http.get('/api/mis-compras')
        purchases.value = response.data
    } catch (err) {
        console.error(err)
    } finally {
        purchasesLoading.value = false
    }
}

const fetchAdminSales = async () => {
    salesLoading.value = true
    try {
        const response = await http.get('/api/admin/sales')
        salesData.value = response.data
    } catch (err) {
        console.error(err)
    } finally {
        salesLoading.value = false
    }
}

const togglePurchaseCode = (id) => {
    purchaseCodesRevealed.value[id] = !purchaseCodesRevealed.value[id]
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
        adminMessage.value = { type: 'success', text: t.value.admin.updated }
        closeEditModal()
        await fetchAdminProducts()
        setTimeout(() => adminMessage.value = { type: '', text: '' }, 3000)
    } catch (err) {
        console.error(err)
        adminMessage.value = { type: 'error', text: t.value.admin.errorUpdate }
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
        adminMessage.value = { type: 'success', text: `${codes.length} ${t.value.admin.codesAdded}` }
        newCodesText.value = ''
        const response = await ProductService.getProductCodes(codesModal.value.id)
        codesData.value = response.data
        await fetchAdminProducts()
        setTimeout(() => adminMessage.value = { type: '', text: '' }, 3000)
    } catch (err) {
        console.error(err)
        const msg = err.response?.data?.message || t.value.admin.errorAddCodes
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
            importError.value = err.response.data.message || 'Error.'
            if (err.response.data.errors) {
                importResult.value = { errors: Object.values(err.response.data.errors).flat() }
            }
        } else {
            importError.value = 'Error inesperado.'
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
        saveMessage.value = t.value.profile.savedOk
        editForm.value.name = auth.user?.name || ''
        editForm.value.last_name = auth.user?.last_name || ''
        editForm.value.username = auth.user?.username || ''
    } catch (err) {
        saveMessage.value = t.value.profile.savedError
        console.error(err)
    } finally {
        saving.value = false
        setTimeout(() => saveMessage.value = '', 3000)
    }
}

const handleDeleteAccount = async () => {
    if (deleteConfirm.value !== t.value.profile.deleteWord) return
    deleting.value = true
    try {
        await auth.deleteAccount()
        router.push('/login')
    } catch (err) {
        console.error(err)
        deleting.value = false
    }
}

const canDelete = computed(() => deleteConfirm.value === t.value.profile.deleteWord)

const formatDate = (dateStr) => {
    const d = new Date(dateStr)
    return d.toLocaleDateString(prefs.lang === 'es' ? 'es-ES' : 'en-US', {
        year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    })
}

const renderStars = (n) => '★'.repeat(n) + '☆'.repeat(5 - n)

onMounted(() => {
    if (hasRole('admin')) {
        fetchAdminProducts()
    }
    fetchPurchases()
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
                        <span>{{ t.profile.myProfile }}</span>
                    </button>

                    <button class="menu-item" :class="{ active: activeSection === 'purchases' }"
                        @click="activeSection = 'purchases'; fetchPurchases()">
                        <img src="/img/carrito1.png" alt="" class="menu-icon" />
                        <span>{{ t.purchases.title }}</span>
                    </button>

                    <button class="menu-item" :class="{ active: activeSection === 'edit' }"
                        @click="activeSection = 'edit'">
                        <img src="/img/lapiz.png" alt="" class="menu-icon" />
                        <span>{{ t.profile.editData }}</span>
                    </button>

                    <button v-if="hasRole('admin')" class="menu-item"
                        :class="{ active: activeSection === 'admin-products' }"
                        @click="activeSection = 'admin-products'; fetchAdminProducts()">
                        <img src="/img/carrito1.png" alt="" class="menu-icon" style="filter: hue-rotate(180deg);" />
                        <span>{{ t.admin.products }}</span>
                    </button>

                    <button v-if="hasRole('admin')" class="menu-item"
                        :class="{ active: activeSection === 'admin-reviews' }"
                        @click="activeSection = 'admin-reviews'; fetchAdminReviews()">
                        <img src="/img/estrella_roja.png" alt="" class="menu-icon" />
                        <span>{{ t.admin.reviews }}</span>
                    </button>

                    <button v-if="hasRole('admin')" class="menu-item"
                        :class="{ active: activeSection === 'admin-sales' }"
                        @click="activeSection = 'admin-sales'; fetchAdminSales()">
                        <img src="/img/carrito1.png" alt="" class="menu-icon" style="filter: hue-rotate(90deg);" />
                        <span>{{ t.sales.title }}</span>
                    </button>

                    <button v-if="hasRole('venedor', 'admin', 'gerent')" class="menu-item"
                        :class="{ active: activeSection === 'import' }" @click="activeSection = 'import'">
                        <img src="/img/boton-circular-plus2.png" alt="" class="menu-icon" />
                        <span>{{ t.admin.upload }}</span>
                    </button>

                    <button class="menu-item" :class="{ active: activeSection === 'delete' }"
                        @click="activeSection = 'delete'">
                        <img src="/img/borrar2.png" alt="" class="menu-icon" />
                        <span>{{ t.profile.deleteAccount }}</span>
                    </button>

                    <div class="menu-divider"></div>

                    <div class="sidebar-prefs">
                        <button class="pref-btn" @click="prefs.toggleDark()"
                            :title="prefs.darkMode ? t.theme.light : t.theme.dark">
                            {{ prefs.darkMode ? '☀️' : '🌙' }}
                        </button>
                        <button class="pref-btn pref-lang" @click="prefs.toggleLang()">
                            {{ prefs.lang === 'es' ? '🇬🇧 EN' : '🇪🇸 ES' }}
                        </button>
                    </div>

                    <div class="menu-divider"></div>

                    <button class="menu-item menu-item-logout" @click="handleLogout">
                        <img src="/img/puerta-abierta2.png" alt="" class="menu-icon" />
                        <span>{{ t.profile.logout }}</span>
                    </button>
                </nav>
            </aside>

            <main class="profile-content">

                <section v-if="activeSection === 'info'" class="content-section">
                    <h2 class="section-title">{{ t.profile.myProfile }}</h2>
                    <p class="section-subtitle">{{ t.profile.accountInfo }}</p>

                    <div class="info-grid">
                        <div class="info-card">
                            <img src="/img/usuario1.png" alt="" class="info-card-icon" />
                            <div>
                                <label>{{ t.profile.name }}</label>
                                <p>{{ auth.user?.name || t.profile.notAvailable }}</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <img src="/img/usuario1.png" alt="" class="info-card-icon" />
                            <div>
                                <label>{{ t.profile.lastName }}</label>
                                <p>{{ auth.user?.last_name || t.profile.notAvailable }}</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <img src="/img/sobre_rojo.png" alt="" class="info-card-icon" />
                            <div>
                                <label>{{ t.profile.email }}</label>
                                <p>{{ auth.user?.email || t.profile.notAvailable }}</p>
                            </div>
                        </div>
                        <div class="info-card" v-if="auth.user?.username">
                            <img src="/img/estrella_roja.png" alt="" class="info-card-icon" />
                            <div>
                                <label>{{ t.profile.username }}</label>
                                <p>{{ auth.user?.username }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="activeSection === 'purchases'" class="content-section">
                    <h2 class="section-title">{{ t.purchases.title }}</h2>
                    <p class="section-subtitle">{{ t.purchases.subtitle }}</p>

                    <div v-if="purchasesLoading" style="text-align: center; padding: 40px; color: #888;">
                        {{ t.purchases.loading }}
                    </div>

                    <div v-else-if="purchases.length === 0" class="no-products-admin">
                        {{ t.purchases.noResults }}
                    </div>

                    <div v-else class="table-responsive-admin">
                        <table class="admin-products-table purchases-table">
                            <thead>
                                <tr>
                                    <th>{{ t.purchases.date }}</th>
                                    <th>{{ t.purchases.product }}</th>
                                    <th>{{ t.purchases.platform }}</th>
                                    <th>{{ t.purchases.price }}</th>
                                    <th>{{ t.purchases.code }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in purchases" :key="p.id">
                                    <td>
                                        <small>{{ formatDate(p.created_at) }}</small>
                                    </td>
                                    <td><strong>{{ p.product_name }}</strong></td>
                                    <td>{{ p.platform || '—' }}</td>
                                    <td><span class="price-final-admin">{{ parseFloat(p.price_paid).toFixed(2) }}€</span></td>
                                    <td>
                                        <span class="purchase-code"
                                            :class="purchaseCodesRevealed[p.id] ? 'code-visible' : 'code-hidden'"
                                            @click="togglePurchaseCode(p.id)"
                                            style="cursor:pointer;"
                                            :title="purchaseCodesRevealed[p.id] ? '' : t.purchases.clickToReveal">
                                            {{ p.code }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section v-if="activeSection === 'edit'" class="content-section">
                    <h2 class="section-title">{{ t.profile.editData }}</h2>
                    <p class="section-subtitle">{{ t.profile.editSubtitle }}</p>

                    <form @submit.prevent="handleSaveProfile" class="edit-form">
                        <div class="form-group">
                            <label for="edit-name">{{ t.profile.name }}</label>
                            <input type="text" id="edit-name" v-model="editForm.name" />
                        </div>
                        <div class="form-group">
                            <label for="edit-last-name">{{ t.profile.lastName }}</label>
                            <input type="text" id="edit-last-name" v-model="editForm.last_name" />
                        </div>
                        <div class="form-group">
                            <label for="edit-username">{{ t.profile.username }}</label>
                            <input type="text" id="edit-username" v-model="editForm.username" />
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save" :disabled="saving">
                                {{ saving ? t.profile.saving : t.profile.save }}
                            </button>
                            <span v-if="saveMessage" class="save-feedback"
                                :class="{ success: saveMessage.includes('✓'), error: saveMessage.includes('✗') }">
                                {{ saveMessage }}
                            </span>
                        </div>
                    </form>
                </section>

                <section v-if="activeSection === 'admin-products'" class="content-section admin-products-section">
                    <h2 class="section-title">{{ t.admin.products }}</h2>
                    <p class="section-subtitle">{{ t.admin.productsSubtitle }}</p>

                    <div v-if="adminMessage.text"
                        :class="adminMessage.type === 'success' ? 'admin-msg-success' : 'admin-msg-error'">
                        {{ adminMessage.text }}
                    </div>

                    <div class="admin-products-header">
                        <input type="text" v-model="adminSearch" :placeholder="t.admin.searchProduct"
                            class="admin-search-input" />
                        <span style="color: #888; font-size: 13px;">{{ filteredAdminProducts.length }}
                            {{ t.admin.products.toLowerCase?.() || '' }}</span>
                    </div>

                    <div v-if="adminLoading" style="text-align: center; padding: 40px; color: #888;">
                        {{ t.admin.loading }}
                    </div>

                    <div v-else class="table-responsive-admin">
                        <table class="admin-products-table">
                            <thead>
                                <tr>
                                    <th>{{ t.admin.img }}</th>
                                    <th>{{ t.admin.productName }}</th>
                                    <th>{{ t.admin.productPrice }}</th>
                                    <th>{{ t.admin.discount }}</th>
                                    <th>{{ t.admin.platform }}</th>
                                    <th>{{ t.admin.stock }}</th>
                                    <th>{{ t.admin.actions }}</th>
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
                                                @click="openEditModal(product)" :title="t.admin.editProduct">
                                                <img src="/img/lapiz.png" alt="" class="admin-action-icon" />
                                            </button>
                                            <button class="btn-admin-action btn-admin-keys"
                                                @click="openCodesModal(product)" :title="t.admin.keys">
                                                <img src="/img/llave.png" alt="" class="admin-action-icon" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="filteredAdminProducts.length === 0" class="no-products-admin">
                            {{ t.admin.noProducts }}
                        </div>
                    </div>
                </section>

                <section v-if="activeSection === 'admin-reviews'" class="content-section admin-products-section">
                    <h2 class="section-title">{{ t.admin.reviews }}</h2>
                    <p class="section-subtitle">{{ t.admin.reviewsSubtitle }}</p>

                    <div class="admin-products-header">
                        <input type="text" v-model="reviewSearch" :placeholder="t.reviews.search"
                            class="admin-search-input" />
                        <span style="color: #888; font-size: 13px;">{{ filteredReviews.length }} reviews</span>
                    </div>

                    <div v-if="reviewsLoading" style="text-align: center; padding: 40px; color: #888;">
                        {{ t.reviews.loading }}
                    </div>

                    <div v-else class="table-responsive-admin">
                        <table class="admin-products-table">
                            <thead>
                                <tr>
                                    <th>{{ t.reviews.user }}</th>
                                    <th>{{ t.reviews.product }}</th>
                                    <th>{{ t.reviews.rating }}</th>
                                    <th>{{ t.reviews.comment }}</th>
                                    <th>{{ t.reviews.date }}</th>
                                    <th>{{ t.reviews.actions }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="review in filteredReviews" :key="review.id">
                                    <td>
                                        <strong>{{ review.user?.name || '—' }}</strong>
                                        <br />
                                        <small style="color:#888;">{{ review.user?.email }}</small>
                                    </td>
                                    <td>{{ review.product?.nombre || '—' }}</td>
                                    <td>
                                        <span class="review-stars-admin">{{ renderStars(review.estrellas) }}</span>
                                    </td>
                                    <td>
                                        <span class="review-comment-cell">{{ review.comentario }}</span>
                                    </td>
                                    <td><small>{{ formatDate(review.created_at) }}</small></td>
                                    <td>
                                        <button class="btn-admin-action btn-review-delete"
                                            @click="deleteReview(review.id)" :title="t.reviews.delete">
                                            <img src="/img/borrar2.png" alt="" class="admin-action-icon" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="filteredReviews.length === 0" class="no-products-admin">
                            {{ t.reviews.noReviews }}
                        </div>
                    </div>
                </section>

                <section v-if="activeSection === 'admin-sales'" class="content-section admin-products-section">
                    <h2 class="section-title">{{ t.sales.title }}</h2>
                    <p class="section-subtitle">{{ t.sales.subtitle }}</p>

                    <div v-if="salesLoading" style="text-align: center; padding: 40px; color: #888;">
                        {{ t.sales.loading }}
                    </div>

                    <template v-else-if="salesData">
                        <div class="sales-stats-grid">
                            <div class="sales-stat-card sales-stat-revenue">
                                <span class="sales-stat-value">{{ salesData.stats.total_revenue.toFixed(2) }}€</span>
                                <span class="sales-stat-label">{{ t.sales.totalRevenue }}</span>
                            </div>
                            <div class="sales-stat-card sales-stat-count">
                                <span class="sales-stat-value">{{ salesData.stats.total_sales }}</span>
                                <span class="sales-stat-label">{{ t.sales.totalSales }}</span>
                            </div>
                            <div class="sales-stat-card sales-stat-avg">
                                <span class="sales-stat-value">{{ salesData.stats.avg_ticket.toFixed(2) }}€</span>
                                <span class="sales-stat-label">{{ t.sales.avgTicket }}</span>
                            </div>
                        </div>

                        <h3 class="sales-sub-title">{{ t.sales.topProducts }}</h3>
                        <div class="table-responsive-admin">
                            <table class="admin-products-table">
                                <thead>
                                    <tr>
                                        <th>{{ t.sales.product }}</th>
                                        <th>{{ t.admin.platform }}</th>
                                        <th>{{ t.sales.totalSales }}</th>
                                        <th>{{ t.sales.totalRevenue }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in salesData.by_product" :key="idx">
                                        <td><strong>{{ item.product }}</strong></td>
                                        <td>{{ item.platform || '—' }}</td>
                                        <td>{{ item.count }} {{ t.sales.units }}</td>
                                        <td><span class="price-final-admin" style="color:#2e7d32;">{{ item.revenue.toFixed(2) }}€</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="sales-sub-title" style="margin-top: 30px;">{{ t.sales.recentSales }}</h3>
                        <div class="table-responsive-admin">
                            <table class="admin-products-table">
                                <thead>
                                    <tr>
                                        <th>{{ t.sales.buyer }}</th>
                                        <th>{{ t.sales.product }}</th>
                                        <th>{{ t.sales.date }}</th>
                                        <th>{{ t.sales.price }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="p in salesData.purchases.slice(0, 20)" :key="p.id">
                                        <td>
                                            <strong>{{ p.user?.name || '—' }}</strong>
                                            <br /><small style="color:#888;">{{ p.user?.email }}</small>
                                        </td>
                                        <td>{{ p.product_name }}</td>
                                        <td><small>{{ formatDate(p.created_at) }}</small></td>
                                        <td><span class="price-final-admin">{{ parseFloat(p.price_paid).toFixed(2) }}€</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="salesData.purchases.length === 0" class="no-products-admin">
                            {{ t.sales.noSales }}
                        </div>
                    </template>
                </section>

                <section v-if="activeSection === 'delete'" class="content-section">
                    <h2 class="section-title section-title-danger">{{ t.profile.deleteAccount }}</h2>
                    <p class="section-subtitle">{{ t.profile.deleteWarning }}</p>

                    <div class="danger-zone">
                        <div class="danger-warning">
                            <img src="/img/borrar2.png" alt="" class="danger-icon" />
                            <div>
                                <h4>{{ t.profile.deleteConfirm }}</h4>
                                <p>{{ t.profile.deleteDesc }}
                                    <strong>{{ t.profile.deleteCantUndo }}</strong>.
                                </p>
                            </div>
                        </div>

                        <div class="confirm-delete">
                            <label>{{ t.profile.typeDelete }} <strong>{{ t.profile.deleteWord }}</strong> {{ t.profile.toConfirm }}</label>
                            <input type="text" v-model="deleteConfirm" :placeholder="t.profile.deleteWord"
                                class="delete-input" />
                            <button class="btn-delete" :disabled="!canDelete || deleting" @click="handleDeleteAccount">
                                {{ deleting ? t.profile.deleting : t.profile.deletePermanent }}
                            </button>
                        </div>
                    </div>
                </section>

                <section v-if="activeSection === 'import'" class="content-section">
                    <h2 class="section-title">{{ t.import.title }}</h2>
                    <p class="section-subtitle">{{ t.import.subtitle }}</p>

                    <div class="import-box">
                        <label for="csvFile" class="import-label">{{ t.import.selectFile }}</label>
                        <input type="file" id="csvFile" accept=".csv" @change="handleFileUpload"
                            class="import-file-input" />

                        <button @click="submitImportFile" :disabled="!importFile || importLoading"
                            class="btn-import">
                            {{ importLoading ? t.import.processing : t.import.importBtn }}
                        </button>
                    </div>

                    <div v-if="importError" class="import-error">{{ importError }}</div>

                    <div v-if="importResult" style="margin-top: 20px;">
                        <div v-if="importResult.messages && importResult.messages.length" class="import-success-box">
                            <ul>
                                <li v-for="(msg, index) in importResult.messages" :key="index">{{ msg }}</li>
                            </ul>
                        </div>
                        <div v-if="importResult.errors && importResult.errors.length" class="import-error-box">
                            <strong>{{ t.import.criticalErrors }}</strong>
                            <ul>
                                <li v-for="(err, index) in importResult.errors" :key="index">{{ err }}</li>
                            </ul>
                        </div>
                        <div v-if="importResult.row_warnings && importResult.row_warnings.length" class="import-warn-box">
                            <strong>{{ t.import.warnings }}</strong>
                            <ul style="max-height: 200px; overflow-y: auto;">
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
                {{ t.admin.editProduct }}
            </h3>

            <div class="modal-form-group">
                <label>{{ t.admin.productName }}</label>
                <input type="text" v-model="editProductForm.nombre" />
            </div>
            <div class="modal-form-group">
                <label>{{ t.admin.productDesc }}</label>
                <textarea v-model="editProductForm.descripcion"></textarea>
            </div>
            <div class="modal-form-row">
                <div class="modal-form-group">
                    <label>{{ t.admin.priceLabel }}</label>
                    <input type="number" step="0.01" min="0" v-model="editProductForm.precio" />
                </div>
                <div class="modal-form-group">
                    <label>{{ t.admin.discountLabel }}</label>
                    <input type="number" min="0" max="100" v-model="editProductForm.porcentaje_descuento" />
                </div>
            </div>
            <div class="modal-form-row">
                <div class="modal-form-group">
                    <label>{{ t.admin.platform }}</label>
                    <select v-model="editProductForm.plataforma">
                        <option value="">{{ t.admin.noPlatform }}</option>
                        <option value="PC">PC</option>
                        <option value="PS5">PS5</option>
                        <option value="Xbox">Xbox</option>
                        <option value="Switch">Switch</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>{{ t.admin.ecoFriendly }}</label>
                    <select v-model="editProductForm.is_eco">
                        <option :value="false">{{ t.admin.no }}</option>
                        <option :value="true">{{ t.admin.yes }}</option>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <label>{{ t.admin.imageUrl }}</label>
                <input type="text" v-model="editProductForm.imagen_url" />
            </div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" @click="closeEditModal">{{ t.admin.cancel }}</button>
                <button class="btn-modal-save" :disabled="editSaving" @click="saveProduct">
                    {{ editSaving ? t.profile.saving : t.admin.saveChanges }}
                </button>
            </div>
        </div>
    </div>

    <div v-if="codesModal" class="modal-overlay" @click.self="closeCodesModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <img src="/img/llave.png" alt="" style="width: 24px; height: 24px; object-fit: contain;" />
                {{ t.admin.keys }} — {{ codesModal.nombre }}
            </h3>

            <div v-if="codesLoading" style="text-align: center; padding: 30px; color:#888;">
                {{ t.admin.loadingKeys }}
            </div>

            <template v-else-if="codesData">
                <div class="codes-summary">
                    <div class="codes-summary-item">
                        <span class="number">{{ codesData.total }}</span>
                        <span class="label">{{ t.admin.total }}</span>
                    </div>
                    <div class="codes-summary-item">
                        <span class="number" style="color: #2e7d32;">{{ codesData.available }}</span>
                        <span class="label">{{ t.admin.available }}</span>
                    </div>
                    <div class="codes-summary-item">
                        <span class="number" style="color: #c62828;">{{ codesData.sold }}</span>
                        <span class="label">{{ t.admin.sold }}</span>
                    </div>
                </div>

                <div class="codes-list" v-if="codesData.codes.length > 0">
                    <div v-for="code in codesData.codes" :key="code.id" class="code-item"
                        :class="code.is_sold ? 'code-sold' : 'code-available'">
                        <span @click="toggleCodeVisibility(code.id)"
                            :class="codesRevealed[code.id] ? 'code-visible' : 'code-hidden'" style="cursor: pointer;"
                            :title="codesRevealed[code.id] ? '' : t.purchases.clickToReveal">
                            {{ code.code }}
                        </span>
                        <span class="code-status" :style="{ color: code.is_sold ? '#c62828' : '#2e7d32' }">
                            {{ code.is_sold ? t.admin.soldLabel : t.admin.availableLabel }}
                        </span>
                    </div>
                </div>
                <div v-else style="text-align: center; color: #888; padding: 20px;">
                    {{ t.admin.noKeys }}
                </div>

                <div class="add-codes-area">
                    <h4 style="margin: 0 0 10px; color: #0e273f; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <img src="/img/plus-pequeno.png" alt="" style="width: 16px; height: 16px; object-fit: contain;" />
                        {{ t.admin.addKeys }}
                    </h4>
                    <textarea v-model="newCodesText" :placeholder="t.admin.keysPlaceholder"></textarea>
                    <p class="add-codes-hint">{{ t.admin.keysHint }}</p>
                    <button class="btn-add-codes" :disabled="addingCodes || !newCodesText.trim()" @click="addNewCodes">
                        {{ addingCodes ? t.admin.adding : t.admin.addToStock }}
                    </button>
                </div>
            </template>

            <div class="modal-actions">
                <button class="btn-modal-cancel" @click="closeCodesModal">{{ t.admin.close }}</button>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../assets/css/profile.css';
@import '../../../assets/css/admin-products.css';
</style>
