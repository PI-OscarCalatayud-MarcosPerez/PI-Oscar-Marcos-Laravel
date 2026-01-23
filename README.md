# 🎮 MOKeys - Tienda de Videojuegos (Laravel 11)

MOKeys es una plataforma e-commerce para la venta de claves de videojuegos, desarrollada por Marcos Pérez y Óscar Calatayud. Este proyecto ha sido migrado y modernizado a **Laravel 11**, utilizando una arquitectura profesional basada en contenedores **Docker**.

---

## 📋 Características Principales

### 🖥️ Frontend (DIW)
- **Diseño Responsivo**: Interfaz adaptable a escritorio y móvil (menú hamburguesa, grids fluidos).
- **Estética Coherente**: Uso de plantillas Blade (`layouts/app.blade.php`) para mantener header, footer y estilos unificados.
- **Feedback Visual**:
  - Alertas de éxito/error en valoraciones.
  - Formulario de reseñas oculto si el usuario ya ha valorado.
  - Skeleton loading y mensajes claros ("Cargando...", "No hay productos") en catálogos dinámicos.

### ⚙️ Backend (Laravel)
- **Arquitectura de Capas**:
  - **Controladores** (`App\Http\Controllers`): Gestionan la entrada HTTP.
  - **Servicios** (`App\Services`): Lógica de negocio (ej. validación de reseñas duplicadas).
  - **Repositorios** (`App\Repositories`): Abstracción de base de datos (Eloquent).
- **API REST**: Endpoint `/api/products` para alimentar componentes dinámicos via AJAX.
- **Autenticación**: Sistema completo de Login/Registro con roles de usuario ('user', 'admin').
- **Moderación**: Los administradores pueden eliminar reseñas inapropiadas.

### 📂 Estructura del Proyecto
- `app/`: Lógica central (Controllers, Models, Services, Repositories).
- `resources/views/`: Plantillas Blade organizadas (`pages`, `products`, `partials`, `layouts`).
- `public/`: Assets estáticos (CSS, JS, imágenes).
- `legacy-php/`: Código del proyecto antiguo conservado como referencia.
- `docker/`: Configuración de servicios (Nginx, PHP, MySQL).

---

## 🚀 Instalación y Despliegue

### Requisitos
- Docker y Docker Compose instalados.
- Make (opcional, para usar los comandos rápidos).

### Paso a Paso

1. **Desplegar contenedores**:
   ```bash
   make up
   # O manualmente: docker compose up -d --build
   ```

2. **Instalación Inicial** (Solo la primera vez):
   Esto instalará dependencias, copiará el `.env`, generará la key y ejecutará migraciones.
   ```bash
   make install
   ```

3. **Migraciones de Base de Datos**:
   Si necesitas actualizar la estructura de la BD (ej. añadir roles):
   ```bash
   make migrate
   ```

---

## 🛠️ Comandos Útiles (Makefile)

| Comando                  | Descripción                                                 |
| ------------------------ | ----------------------------------------------------------- |
| `make up`                | Levanta los contenedores en segundo plano.                  |
| `make down`              | Detiene los contenedores.                                   |
| `make sh`                | Accede a la terminal del contenedor de Laravel (workspace). |
| `make artisan CMD="..."` | Ejecuta un comando de artisan dentro de Docker.             |
| `make logs`              | Ver logs de la aplicación en tiempo real.                   |

---

## 🔍 Detalles de Implementación

- **Validaciones**: Se utilizan `FormRequests` y validaciones en controlador para asegurar la integridad de datos (ej. estrellas entre 1 y 5).
- **Seguridad**: Protección CSRF en todos los formularios, sanitización de inputs y uso de sentencias preparadas (via Eloquent).
- **Nomenclatura**: Código en inglés (estándar Laravel) con comentarios explicativos cuando es necesario. Documentación en castellano.

---

**Autores**: Marcos Pérez y Óscar Calatayud.
