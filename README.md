# 🎮 MOKeys - Plataforma E-commerce de Videojuegos

MOKeys es una aplicación web moderna para la venta de claves de videojuegos, desarrollada con arquitectura separada frontend/backend, containerizada con Docker y desplegada en AWS.

**Autores**: Marcos Pérez & Óscar Calatayud  
**Stack**: Laravel 11 (Backend API) + Vue 3 (Frontend SPA) + MySQL 8.4  
**Infraestructura**: AWS (EC2, RDS, ALB) con CI/CD automatizado

---

## 📚 Documentación

- 📐 [**Arquitectura AWS**](./ARCHITECTURE.md): Diagrama de infraestructura, VPC, Security Groups, DNS
- 🤝 [**Guía de Contribución**](./CONTRIBUTING.md): Git workflow, code style, responsabilidades del equipo
- 📋 **Documentación Frontend**: [frontend/README.md](./frontend/README.md)

---

## ✨ Características

### Backend (Laravel 11)
- 🔐 **Autenticación**: Laravel Sanctum (cookies + CSRF)
- 👥 **Roles**: Sistema de roles (admin, user) con middleware
- 📦 **CRUD Completo**: Productos, categorías, reseñas, pedidos
- 🏗️ **Arquitectura**: Controllers → Services → Repositories
- 🧪 **Testing**: PHPUnit con cobertura crítica
- 🔄 **API REST**: Endpoints documentados para frontend

### Frontend (Vue 3)
- ⚡ **SPA**: Vue Router con modo history
- 🗄️ **State Management**: Pinia stores (auth, products, cart)
- 🎨 **UI/UX**: Diseño responsivo, loading states, error handling
- 🔒 **Protección de Rutas**: Guards por rol (admin/user)
- 📡 **HTTP Client**: Axios con interceptors

### Infraestructura
- 🐳 **Docker**: Contenedores para dev y producción
- ☁️ **AWS**: VPC, EC2, RDS Multi-AZ, ALB con HTTPS
- 🚀 **CI/CD**: GitHub Actions (test → build → deploy)
- 🌐 **DNS**: Zona delegada `mokeys.com`

---

## 🚀 Instalación y Desarrollo Local

### Requisitos
- Docker & Docker Compose
- Make (opcional)
- Git

### Setup Inicial

```bash
# 1. Clonar repositorio
git clone https://github.com/[usuario]/mokeys.git
cd mokeys

# 2. Copiar .env y configurar
cp .env.example .env
# Editar .env con tus valores (normalmente solo para producción)

# 3. Levantar contenedores (desarrollo)
make up
# O sin make: docker-compose up -d --build

# 4. Instalación de dependencias (solo primera vez)
make install
# Esto ejecuta: composer install, npm install, php artisan key:generate, migrate

# 5. Acceder a la aplicación
# Backend: http://localhost (API)
# Frontend: http://localhost:5173 (Vite dev server)
# PhpMyAdmin: http://localhost:8080
```

### Comandos Útiles

| Comando        | Descripción                     |
| -------------- | ------------------------------- |
| `make up`      | Levantar contenedores           |
| `make down`    | Detener contenedores            |
| `make sh`      | Terminal del contenedor Laravel |
| `make test`    | Ejecutar tests PHPUnit          |
| `make migrate` | Ejecutar migraciones            |
| `make seed`    | Ejecutar seeders                |
| `make logs`    | Ver logs en tiempo real         |

### Entorno de Desarrollo Frontend

```bash
cd frontend
npm install
npm run dev  # Servidor Vite en http://localhost:5173
```

---

## 🐳 Docker - Entorno de Producción

### Build de Producción (Local Testing)

```bash
# Backend
docker build -f Dockerfile.prod -t mokeys-backend:latest .

# Frontend
cd frontend
docker build -f Dockerfile.prod -t mokeys-frontend:latest .

# Orquestación completa
docker-compose -f docker-compose.prod.yml up --build
```

### Variables de Entorno Requeridas (Producción)

Copiar `.env.example` a `.env` en el servidor y configurar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.mokeys.com

DB_HOST=<RDS_ENDPOINT>
DB_DATABASE=MOKeys
DB_USERNAME=<RDS_USER>
DB_PASSWORD=<RDS_PASSWORD>

FRONTEND_URL=https://mokeys.com
SESSION_DOMAIN=.mokeys.com
SANCTUM_STATEFUL_DOMAINS=mokeys.com
```

---

## 🔄 CI/CD - Despliegue Automático

### Workflows de GitHub Actions

El proyecto incluye dos pipelines automatizados:

#### Backend ([`.github/workflows/deploy-backend.yml`](./.github/workflows/deploy-backend.yml))

**Triggers**: Push a `main` que modifique archivos backend

**Pasos**:
1. ✅ **Tests**: Ejecuta `php artisan test`
2. 🐳 **Build**: Crea imagen Docker desde `Dockerfile.prod`
3. 📤 **Push**: Sube imagen a AWS ECR
4. 🚀 **Deploy**: SSH a EC2, pull de imagen, restart de contenedores
5. 📊 **Migraciones**: Ejecuta `php artisan migrate --force` automáticamente

#### Frontend ([`.github/workflows/deploy-frontend.yml`](./.github/workflows/deploy-frontend.yml))

**Triggers**: Push a `main` que modifique `frontend/`

**Pasos**:
1. 📦 **Install**: `npm ci`
2. 🏗️ **Build**: `npm run build` (genera `dist/`)
3. 📤 **Deploy**: Sube a S3 + CloudFront **o** copia via SCP a EC2

### GitHub Secrets Necesarios

Configurar en **GitHub → Settings → Secrets and variables → Actions**:

```
AWS_ACCESS_KEY_ID          # Credenciales AWS
AWS_SECRET_ACCESS_KEY
AWS_REGION                 # ej: us-east-1
ECR_REGISTRY              # ej: 123456789.dkr.ecr.us-east-1.amazonaws.com

EC2_HOST                   # IP pública EC2 backend
EC2_FRONTEND_HOST          # IP pública EC2 frontend (o S3_BUCKET)
EC2_USER                   # Usuario SSH (ej: ec2-user)
EC2_SSH_KEY                # Clave privada SSH completa

VITE_API_BASE_URL          # https://api.mokeys.com

# Opcionales (si usas S3)
S3_BUCKET                  # Nombre del bucket S3
CLOUDFRONT_DISTRIBUTION_ID # ID de CloudFront
```

---

## ☁️ Arquitectura AWS

### Resumen de Componentes

```
Internet
   ↓
Route 53 (mokeys.com)
   ↓
Application Load Balancer (HTTPS:443)
   ↓
┌─────────────────┬─────────────────┐
│   EC2 Frontend  │   EC2 Backend   │
│   Nginx + Vue   │   PHP-FPM       │
└─────────────────┴─────────────────┘
                      ↓
            RDS MySQL Multi-AZ
```

**Ver documentación completa**: [ARCHITECTURE.md](./ARCHITECTURE.md)

### Configuración Inicial AWS

1. **VPC**: Crear VPC con subredes públicas/privadas
2. **RDS**: MySQL 8.4 Multi-AZ en subredes privadas
3. **EC2**: 2 instancias (frontend/backend) con Docker instalado
4. **ALB**: Load Balancer con certificado SSL/TLS (Let's Encrypt vía ACM)
5. **Route 53**: Configurar zona `mokeys.com` apuntando a ALB
6. **ECR**: Repositorios para imágenes Docker

**Todos los detalles**: [ARCHITECTURE.md - Sección Setup](./ARCHITECTURE.md#configuración-inicial-aws)

---

## 🧪 Testing

### Backend

```bash
# Todos los tests
make test

# Test específico
docker exec -it mokeys_app php artisan test --filter ProductTest

# Con coverage
docker exec -it mokeys_app php artisan test --coverage
```

### Frontend

```bash
cd frontend
npm run test         # Tests unitarios (si existen)
npm run test:e2e     # Tests E2E (si existen)
```

---

## 👤 Usuarios de Prueba

### Usuario Administrador
- **Email**: `admin@mokeys.com`
- **Contraseña**: `admin123`
- **Permisos**: CRUD completo, eliminar reseñas, gestión usuarios

### Usuario Estándar
- **Email**: `user@mokeys.com`
- **Contraseña**: `user123`
- **Permisos**: Comprar productos, dejar reseñas

**Nota**: Ejecutar `php artisan db:seed` para crear estos usuarios.

---

## 📂 Estructura del Proyecto

```
mokeys/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controladores API y Web
│   │   └── Middleware/        # Auth, CORS, Roles
│   ├── Models/                # Eloquent Models
│   ├── Services/              # Lógica de negocio
│   └── Repositories/          # Abstracción de DB
├── frontend/
│   ├── src/
│   │   ├── modules/           # Feature modules (auth, products)
│   │   ├── stores/            # Pinia stores
│   │   ├── router/            # Vue Router
│   │   └── services/          # Axios API clients
│   ├── Dockerfile.prod        # Build de producción
│   └── nginx.conf             # Config Nginx para SPA
├── database/
│   ├── migrations/            # Esquema de DB
│   └── seeders/               # Datos de prueba
├── .github/workflows/         # Pipelines CI/CD
├── docker-compose.yml         # Desarrollo local
├── docker-compose.prod.yml    # Testing de producción local
├── Dockerfile                 # Desarrollo
├── Dockerfile.prod            # Producción optimizado
├── ARCHITECTURE.md            # Docs AWS
└── CONTRIBUTING.md            # Guía de contribución
```

---

## 🔧 Tecnologías Utilizadas

| Layer          | Tecnología          | Versión |
| -------------- | ------------------- | ------- |
| **Backend**    | Laravel             | 11.x    |
|                | PHP                 | 8.3     |
|                | MySQL               | 8.4     |
| **Frontend**   | Vue.js              | 3.5     |
|                | Pinia               | 3.0     |
|                | Vue Router          | 5.0     |
|                | Axios               | 1.13    |
| **DevOps**     | Docker              | 27.x    |
|                | GitHub Actions      | -       |
|                | AWS (EC2, RDS, ALB) | -       |
| **Web Server** | Nginx               | 1.27    |

---

## 📞 Soporte y Contribución

- 📖 Lee la [Guía de Contribución](./CONTRIBUTING.md) antes de hacer PRs
- 🐛 Reporta bugs abriendo un **Issue** en GitHub
- 💡 Propón features en **Discussions**

**Equipo de Desarrollo**:
- Marcos Pérez: Backend, Infraestructura AWS, CI/CD Backend
- Óscar Calatayud: Frontend, DNS, CI/CD Frontend

---

## 📄 Licencia

Este proyecto es un trabajo académico desarrollado para el módulo de **Despliegue de Aplicaciones Web** y **Nuevas Tecnologías de Virtualización**.

---

**⭐ Si te gusta el proyecto, dale una estrella en GitHub!**
