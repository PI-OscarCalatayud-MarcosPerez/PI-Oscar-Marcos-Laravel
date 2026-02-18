<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import ProductService from '../services/ProductService'
import { useCartStore } from '@/modules/cart/store/cartStore'
import { useUiStore } from '@/stores/uiStore'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const ui = useUiStore()

const addToCart = (product) => {
    cartStore.addItem({
        id: product.id,
        title: product.nombre,
        price: parseFloat(product.precio),
        image: getImage(product),
        quantity: 1
    })
    ui.showToast('success', 'Producto añadido al carrito')
}

const buyNow = (product) => {
    addToCart(product)
    router.push('/cart') // Or /checkout if user prefers
}

// State
const products = ref([])
const loading = ref(true)
const error = ref(null)

// Filters State
const searchTerm = ref('') // Connected to navbar search ideally, or local input
const selectedCategories = ref([])
const selectedPlatforms = ref([])
const maxPrice = ref(100)
const sortOrder = ref('default')

// Filter Options
const categories = ['Acción', 'RPG', 'Deportes', 'Estrategia', 'Aventura', 'Terror', 'Software']
const platforms = ['PC', 'PS5', 'Xbox', 'Switch']

const fetchProducts = async () => {
    loading.value = true;
    try {
        const response = await ProductService.getProducts();
        const data = response.data;
        products.value = Array.isArray(data) ? data : (data.products || data.data || []);
    } catch (err) {
        error.value = "Error listando productos";
        console.error(err);
    } finally {
        loading.value = false;
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

// Computed Filtered Products
const filteredProducts = computed(() => {
    let filtered = products.value;

    const queryQ = route.query.q ? normalizar(route.query.q) : "";
    if (queryQ) {
        filtered = filtered.filter(p => {
            const nom = p.nombre || p.nom || "";
            return normalizar(nom).includes(queryQ);
        });
    }

    // 2. Categories
    if (selectedCategories.value.length > 0) {
        const normalizedCats = selectedCategories.value.map(c => normalizar(c));
        filtered = filtered.filter(p => {
            const cat = p.categoria ? normalizar(p.categoria) : "";
            return normalizedCats.includes(cat);
        });
    }

    // 3. Platforms (Note: Assuming platform info exists or generic)
    if (selectedPlatforms.value.length > 0) {
        const normalizedPlats = selectedPlatforms.value.map(c => normalizar(c));
        filtered = filtered.filter(p => {
            const plat = p.plataforma ? normalizar(p.plataforma) : (p.descripcion ? normalizar(p.descripcion) : "");
            // Simple fallback check in description if no field
            return normalizedPlats.some(np => plat.includes(np));
        });
    }

    // 4. Price
    filtered = filtered.filter(p => {
        const precio = parseFloat(p.precio || p.preu || 0);
        return precio <= parseFloat(maxPrice.value);
    });

    // 5. Sorting
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
    selectedCategories.value = [];
    selectedPlatforms.value = [];
    maxPrice.value = 100;
    sortOrder.value = 'default';
    router.replace({ query: {} }); // Clear search
}

const goToProduct = (id) => {
    router.push(`/products/${id}`);
}

onMounted(() => {
    fetchProducts();
    // Sync query params if needed
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
                <label v-for="cat in categories" :key="cat">
                    <input type="checkbox" :value="cat" v-model="selectedCategories" /> {{ cat }}
                </label>
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
                <div v-if="loading">Cargando...</div>
                <div v-if="error">{{ error }}</div>

                <div v-for="product in filteredProducts" :key="product.id" class="shop-item"
                    :title="'Ver detalles de ' + product.nombre">
                    <div class="shop-item-img" @click="goToProduct(product.id)"
                        :style="{ backgroundImage: 'url(' + getImage(product) + ')', backgroundSize: 'cover', backgroundPosition: 'center', height: '200px', cursor: 'pointer' }">
                    </div>
                    <div class="shop-item-info">
                        <p class="shop-item-title" @click="goToProduct(product.id)" style="cursor:pointer">{{
                            product.nombre }}</p>
                        <p style="font-size:0.9rem; color:#666;">{{ product.categoria }}</p>
                        <p class="shop-item-price">{{ parseFloat(product.precio).toFixed(2) }}€</p>
                    </div>
                </div>

                <p v-if="!loading && filteredProducts.length === 0"
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
