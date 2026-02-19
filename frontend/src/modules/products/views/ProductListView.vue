```vue
<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useProductStore } from '../store/productStore' // Import Store
import CategoryService from '../services/CategoryService'
import { useCartStore } from '@/modules/cart/store/cartStore'
import { useUiStore } from '@/stores/uiStore'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const ui = useUiStore()
const productStore = useProductStore() // Use Store

const addToCart = (product) => {
    let finalPrice = parseFloat(product.precio);
    if (product.porcentaje_descuento > 0) {
        finalPrice = finalPrice * (1 - product.porcentaje_descuento / 100);
    }
    cartStore.addItem({
        id: product.id,
        title: product.nombre,
        price: finalPrice,
        image: getImage(product),
        quantity: 1
    })
    ui.showToast('success', 'Producto añadido al carrito')
}

const buyNow = (product) => {
    addToCart(product)
    router.push('/cart')
}

// Filters State (Local UI state for inputs, syncing with store)
const maxPrice = ref(100)
const sortOrder = ref('default')
const selectedPlatforms = ref([])
const categories = ref([])
const platforms = ['PC', 'PS5', 'Xbox', 'Switch']

const fetchCategories = async () => {
    try {
        const response = await CategoryService.getCategories();
        categories.value = response.data;
    } catch (err) {
        console.error("Error fetching categories:", err);
    }
}

// Helper to clean strings
const normalizar = (texto) => {
    if (!texto) return "";
    return texto.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
}

// Helper for images
const getImage = (product) => {
    let img = product.imagen_url || product.img || "img/placeholder.jpg";
    if (img && !img.startsWith('http') && !img.startsWith('data:')) {
        if (img.startsWith('/')) img = img.substring(1);
        if (!img.startsWith('img/')) img = 'img/' + img;
        img = '/' + img;
    }
    return img;
}

const filteredProducts = computed(() => {
    let filtered = productStore.products;

    // 1. Client-side Search (if not backend)
    const queryQ = route.query.q ? normalizar(route.query.q) : "";
    if (queryQ) {
        filtered = filtered.filter(p => {
            const nom = p.nombre || p.nom || "";
            return normalizar(nom).includes(queryQ);
        });
    }

    // 2. Platforms
    if (selectedPlatforms.value.length > 0) {
        const normalizedPlats = selectedPlatforms.value.map(c => normalizar(c));
        filtered = filtered.filter(p => {
            const plat = p.plataforma ? normalizar(p.plataforma) : (p.descripcion ? normalizar(p.descripcion) : "");
            return normalizedPlats.some(np => plat.includes(np));
        });
    }

    // 3. Price
    filtered = filtered.filter(p => {
        const precio = parseFloat(p.precio || p.preu || 0);
        return precio <= parseFloat(maxPrice.value);
    });

    // 4. Sorting
    if (sortOrder.value === 'price-asc') {
        filtered.sort((a, b) => parseFloat(a.precio) - parseFloat(b.precio));
    } else if (sortOrder.value === 'price-desc') {
        filtered.sort((a, b) => parseFloat(b.precio) - parseFloat(a.precio));
    } else if (sortOrder.value === 'az') {
        filtered.sort((a, b) => (a.nombre || '').localeCompare(b.nombre || ''));
    }

    return filtered;
});

const clearFilters = () => {
    selectedPlatforms.value = [];
    maxPrice.value = 100;
    sortOrder.value = 'default';
    productStore.clearFilters(); // Clear store filters
    router.replace({ query: {} });
}

const goToProduct = (id) => {
    router.push(`/products/${id}`);
}

// Sync URL query to Store Filters
watch(() => route.query, (newQuery) => {
    const params = {};
    if (newQuery.category) params.category = newQuery.category;
    if (newQuery.offers) params.offers = newQuery.offers === 'true';

    // Trigger store fetch
    productStore.fetchProducts(params);
}, { immediate: true, deep: true });

onMounted(() => {
    fetchCategories();
    // Initial fetch handled by immediate watch
});
</script>

<template>

    <div class="shop-container container-fluid px-md-5">
        <!-- Sidebar -->
        <aside class="sidebar-filtros">
            <div class="filter-header">
                <h3>Filtrar por</h3>
                <button @click="clearFilters" class="btn-clean">Limpiar</button>
            </div>

            <div class="filter-group">
                <h4>Género</h4>
                <div v-for="cat in categories" :key="cat.id">
                    <!-- Using link or click to navigate/filter via URL -->
                    <a href="#" @click.prevent="router.push({ query: { ...route.query, category: cat.name } })"
                        :class="{ 'fw-bold': route.query.category === cat.name }"
                        style="text-decoration: none; color: inherit; display: block; margin-bottom: 5px;">
                        {{ cat.name }}
                    </a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Plataforma</h4>
                <label v-for="plat in platforms" :key="plat">
                    <input type="checkbox" :value="plat" v-model="selectedPlatforms" /> {{ plat }}
                </label>
            </div>

            <div class="filter-group">
                <h4>Precio Máximo</h4>
                <input type="range" min="0" max="100" v-model="maxPrice" style="width: 100%" />
                <p>Hasta: <span>{{ maxPrice }}</span>€</p>
            </div>
        </aside>

        <!-- Product Grid -->
        <section class="products-section">
            <div class="products-header">
                <span>{{ filteredProducts.length }} productos encontrados</span>
                <select v-model="sortOrder" id="sortOrder">
                    <option value="default">Orden por defecto</option>
                    <option value="price-asc">Precio: Menor a Mayor</option>
                    <option value="price-desc">Precio: Mayor a Menor</option>
                    <option value="az">Nombre: A-Z</option>
                </select>
            </div>

            <div class="products-grid">
                <div v-if="productStore.loading">Cargando...</div>
                <div v-if="productStore.error">{{ productStore.error }}</div>

                <div v-for="product in filteredProducts" :key="product.id" class="shop-item"
                    :title="'Ver detalles de ' + product.nombre">
                    <div class="shop-item-img" @click="goToProduct(product.id)"
                        :style="{ backgroundImage: 'url(' + getImage(product) + ')', backgroundSize: 'cover', backgroundPosition: 'center', height: '200px', cursor: 'pointer', position: 'relative' }">
                        <span v-if="product.porcentaje_descuento > 0"
                            class="badge badge-discount position-absolute top-0 end-0 m-2">
                            -{{ product.porcentaje_descuento }}%
                        </span>
                    </div>
                    <div class="shop-item-info">
                        <p class="shop-item-title" @click="goToProduct(product.id)" style="cursor:pointer">{{
                            product.nombre }}</p>
                        <p style="font-size:0.9rem; color:#666;">{{ product.category ? product.category.name :
                            (product.categoria || '') }}</p>
                        <p class="shop-item-price">
                            <span v-if="product.porcentaje_descuento > 0"
                                class="text-muted text-decoration-line-through me-2">
                                {{ parseFloat(product.precio).toFixed(2) }}€
                            </span>
                            <span :class="{ 'text-danger': product.porcentaje_descuento > 0 }">
                                {{ (parseFloat(product.precio) * (1 - product.porcentaje_descuento / 100)).toFixed(2)
                                }}€
                            </span>
                        </p>
                    </div>
                </div>

                <p v-if="!productStore.loading && filteredProducts.length === 0"
                    style="grid-column: 1/-1; text-align: center; color: #666;">
                    No se encontraron productos con esos filtros.
                </p>
            </div>
        </section>
    </div>
</template>

<style>
@import '../../../assets/css/product-list.css';
</style>
