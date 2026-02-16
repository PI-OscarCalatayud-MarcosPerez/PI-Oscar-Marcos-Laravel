# 🎨 MOKeys Frontend - Vue 3 SPA

Aplicación de cliente (Single Page Application) para la plataforma MOKeys, construida con Vue 3, Pinia y Vue Router.

---

## 🚀 Desarrollo Local

```bash
# Instalar dependencias
npm install

# Servidor de desarrollo (Vite)
npm run dev

# Build de producción
npm run build

# Preview del build
npm run preview
```

**URL Local**: http://localhost:5173

---

## 📦 Tecnologías

- **Vue 3**: Framework JavaScript reactivo
- **Pinia**: State management oficial de Vue
- **Vue Router**: Enrutamiento SPA
- **Axios**: Cliente HTTP para API
- **Vite**: Build tool ultra-rápido

---

## 📁 Estructura

```
frontend/
├── src/
│   ├── modules/              # Feature modules
│   │   ├── auth/             # Login, register, guards
│   │   ├── products/         # Listado, detalle, CRUD
│   │   └── admin/            # Panel admin
│   ├── stores/               # Pinia stores
│   │   ├── auth.js           # Estado de autenticación
│   │   ├── products.js       # Catálogo de productos
│   │   └── cart.js           # Carrito de compras
│   ├── router/               # Configuración de rutas
│   ├── services/             # API clients (Axios)
│   ├── components/           # Componentes reutilizables
│   ├── views/                # Páginas principales
│   └── main.js               # Entry point
├── public/                   # Assets estáticos
├── Dockerfile.prod           # Build de producción
├── nginx.conf                # Config Nginx para SPA
└── vite.config.js            # Configuración Vite
```

---

## 🔐 Autenticación

La aplicación usa **Laravel Sanctum** con cookies HTTPONLY para autenticación:

1. Frontend solicita token CSRF: `GET /sanctum/csrf-cookie`
2. Usuario hace login: `POST /login` (email, password)
3. Backend devuelve cookie de sesión
4. Todas las requests posteriores incluyen automáticamente la cookie

**Store de Auth**: `src/stores/auth.js`

---

## 🛣️ Rutas

| Ruta            | Componente        | Acceso         |
| --------------- | ----------------- | -------------- |
| `/`             | HomeView          | Público        |
| `/products`     | ProductsView      | Público        |
| `/products/:id` | ProductDetailView | Público        |
| `/login`        | LoginView         | No autenticado |
| `/register`     | RegisterView      | No autenticado |
| `/cart`         | CartView          | Autenticado    |
| `/admin`        | AdminDashboard    | Rol: admin     |

**Guards**: Ver `src/router/index.js` para lógica de protección de rutas.

---

## 📡 API Services

Todos los servicios HTTP están en `src/modules/*/services/`:

```javascript
// Ejemplo: src/modules/products/services/productService.js
import axios from '@/lib/axios';

export default {
  async getAll() {
    const { data } = await axios.get('/api/products');
    return data;
  },
  
  async getById(id) {
    const { data } = await axios.get(`/api/products/${id}`);
    return data;
  }
};
```

**Base URL**: Configurada automáticamente por Vite proxy (dev) o variable de entorno (prod).

---

## 🌐 Variables de Entorno

### Desarrollo (`.env.development`)

No es necesario crear este archivo. Vite usa el proxy configurado en `vite.config.js`:

```javascript
server: {
  proxy: {
    '/api': 'http://localhost',  // Backend Laravel
    '/sanctum': 'http://localhost'
  }
}
```

### Producción (`.env.production`)

**Ya creado en este proyecto**:

```env
VITE_API_BASE_URL=https://api.mokeys.com
```

Al hacer `npm run build`, Vite inyecta esta variable en el código.

---

## 🐳 Docker - Producción

### Dockerfile Multi-Stage

El `Dockerfile.prod` utiliza dos etapas:

1. **Builder**: Node.js para compilar (`npm run build`)
2. **Server**: Nginx Alpine para servir los estáticos

```bash
# Build de imagen
docker build -f Dockerfile.prod -t mokeys-frontend:latest .

# Ejecutar contenedor
docker run -p 8080:80 mokeys-frontend:latest
```

**Resultado**: SPA Vue servida en `http://localhost:8080`

---

## 🚀 Despliegue

### Opción 1: S3 + CloudFront (Recomendado)

```bash
# Build
npm run build

# Subir a S3
aws s3 sync dist/ s3://tu-bucket-frontend --delete

# Invalidar caché CloudFront
aws cloudfront create-invalidation --distribution-id XXXXX --paths "/*"
```

### Opción 2: EC2 con Nginx

1. Build local o vía CI/CD
2. SCP de archivos:
   ```bash
   scp -r dist/* ec2-user@IP:/var/www/html
   ```
3. Configurar Nginx para servir SPA (ver `nginx.conf`)

### Opción 3: GitHub Actions

Ver [`.github/workflows/deploy-frontend.yml`](../.github/workflows/deploy-frontend.yml) para pipeline automatizado.

---

## 🎨 Code Style

- **Componentes**: PascalCase (`ProductCard.vue`)
- **Stores**: camelCase (`useAuthStore`)
- **Servicios**: camelCase (`productService.js`)
- **Indentación**: 4 espacios
- **API**: Composition API con `<script setup>`

```vue
<script setup>
import { ref, onMounted } from 'vue';
import { useProductStore } from '@/stores/products';

const store = useProductStore();
const loading = ref(false);

onMounted(async () => {
  loading.value = true;
  await store.fetchProducts();
  loading.value = false;
});
</script>
```

---

## 🧪 Testing

```bash
# Tests unitarios (si están configurados)
npm run test

# E2E tests (si están configurados)
npm run test:e2e
```

---

## 📞 Troubleshooting

### Error de CORS
- **Causa**: Backend no permite el origen del frontend
- **Solución**: Verificar `config/cors.php` en Laravel incluye tu dominio

### 419 CSRF Token Mismatch
- **Causa**: Cookie CSRF no se envía correctamente
- **Solución**: Asegurar `withCredentials: true` en Axios y `supports_credentials: true` en CORS

### Rutas 404 en producción
- **Causa**: Nginx no redirige a `index.html`
- **Solución**: Usar el `nginx.conf` proporcionado con `try_files`

---

**Desarrollado por**: Marcos Pérez & Óscar Calatayud
