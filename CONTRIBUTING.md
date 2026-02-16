# Guía de Contribución - MOKeys

## 📋 Tabla de Contenidos

- [Estrategia de Branching](#estrategia-de-branching)
- [Flujo de Trabajo](#flujo-de-trabajo)
- [Code Review](#code-review)
- [Commits y Mensajes](#commits-y-mensajes)
- [Code Style](#code-style)
- [Testing](#testing)
- [Responsabilidades](#responsabilidades)

---

## 🌿 Estrategia de Branching

Utilizamos **Git Flow** simplificado:

### Ramas Principales

- **`main`**: Producción. **NUNCA** se hace commit directo aquí.
- **`develop`**: Desarrollo. Integración de features.

### Ramas de Trabajo

- **`feature/nombre-funcionalidad`**: Nuevas funcionalidades
- **`fix/nombre-bug`**: Corrección de bugs
- **`hotfix/nombre-urgente`**: Fixes críticos en producción

### Ejemplo de Nombres

```
✅ feature/login-sanctum
✅ feature/product-crud
✅ fix/cors-error
✅ hotfix/database-connection
❌ marcos-cambios
❌ nueva-rama
❌ test
```

---

## 🔄 Flujo de Trabajo

### 1. Crear una Rama

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nombre-funcionalidad
```

### 2. Desarrollar

- Haz commits frecuentes y atómicos
- Escribe mensajes de commit descriptivos (ver sección abajo)
- Asegúrate de que los tests pasen antes de hacer push

### 3. Push y Pull Request

```bash
git push origin feature/nombre-funcionalidad
```

Luego en GitHub:
1. Crear Pull Request hacia `develop`
2. Asignar reviewer (compañero del equipo)
3. Describir los cambios realizados
4. Esperar aprobación

### 4. Merge

- Solo después de aprobación del reviewer
- Solo si los tests de CI/CD pasan (✅ en GitHub Actions)
- Usar **Squash and Merge** para mantener historial limpio

---

## 👀 Code Review

### Responsabilidades del Autor

- [ ] Código funcional y testeado localmente
- [ ] Tests automáticos escritos (si aplica)
- [ ] Documentación actualizada
- [ ] Sin código comentado innecesario
- [ ] Variables de entorno documentadas en `.env.example`

### Responsabilidades del Reviewer

- [ ] Revisar lógica de negocio
- [ ] Verificar seguridad (SQL injection, XSS, etc.)
- [ ] Comprobar que sigue el code style
- [ ] Validar que los tests cubren casos edge
- [ ] Aprobar o pedir cambios con comentarios constructivos

### Checklist de Review

**Backend (Laravel)**:
- ✅ ¿Usa Form Requests para validación?
- ✅ ¿Está protegido con middlewares (auth, roles)?
- ✅ ¿Evita N+1 queries (usa `with()` en Eloquent)?
- ✅ ¿Usa repositorios/servicios en vez de lógica en controladores?

**Frontend (Vue)**:
- ✅ ¿Usa Pinia stores en vez de state local innecesario?
- ✅ ¿Componentes reutilizables y pequeños (<200 líneas)?
- ✅ ¿Maneja correctamente errores de API?
- ✅ ¿Feedback al usuario (loading, success, errors)?

---

## 💬 Commits y Mensajes

### Formato

```
<tipo>(<scope>): <mensaje corto>

[Cuerpo opcional con detalles]

[Footer opcional con referencias a issues]
```

### Tipos

- `feat`: Nueva funcionalidad
- `fix`: Corrección de bug
- `docs`: Solo documentación
- `style`: Formato, espacios (no cambia lógica)
- `refactor`: Refactorización sin cambiar comportamiento
- `test`: Añadir/modificar tests
- `chore`: Tareas de mantenimiento (deps, config)

### Ejemplos

```bash
✅ feat(auth): implementar login con Sanctum
✅ fix(products): corregir error en filtrado por categoría
✅ docs(readme): añadir instrucciones de Docker
✅ refactor(store): migrar de store pattern a Pinia
❌ cambios varios
❌ arreglado
❌ asdf
```

---

## 🎨 Code Style

### PHP (Laravel)

Seguimos **PSR-12** y las convenciones de Laravel:

```php
// ✅ BIEN
class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')
            ->where('active', true)
            ->get();

        return response()->json($products);
    }
}

// ❌ MAL
class ProductController extends Controller {
    public function index(Request $request) {
        $products=Product::where('active',true)->get();
        return response()->json($products);
    }
}
```

**Reglas clave**:
- 4 espacios de indentación (no tabs)
- Llaves `{` en nueva línea para clases/métodos
- Nombres de clases en `PascalCase`
- Nombres de métodos en `camelCase`
- Nombres de variables en `snake_case` (solo en DB)

### JavaScript (Vue)

Seguimos **Vue Style Guide Recommended**:

```javascript
// ✅ BIEN
export default {
    name: 'ProductList',
    props: {
        products: {
            type: Array,
            required: true
        }
    },
    setup() {
        const handleDelete = async (id) => {
            await productService.delete(id);
        };

        return { handleDelete };
    }
};

// ❌ MAL
export default {
  props: ['products'],
  setup(){
      const handleDelete=async(id)=>{
        await productService.delete(id)
      }
      return{handleDelete}
  }
}
```

**Reglas clave**:
- 4 espacios de indentación
- Componentes en `PascalCase.vue`
- Props con validación explícita
- Composition API con `setup()`
- `const` por defecto, `let` solo si muta

### Formateo Automático

```bash
# Backend
./vendor/bin/pint

# Frontend
cd frontend && npm run lint
```

---

## 🧪 Testing

### Backend

```bash
# Ejecutar todos los tests
make test

# Test específico
php artisan test --filter ProductTest
```

**Cobertura mínima**: 70% de código crítico (auth, productos, pedidos)

### Frontend

```bash
cd frontend
npm run test
```

### CI/CD

- Todos los PRs a `develop` o `main` **DEBEN** pasar tests automáticos
- GitHub Actions bloqueará merge si fallan

---

## 👥 Responsabilidades

### Marcos Pérez
- Backend Laravel (APIs, lógica de negocio)
- Base de datos (migraciones, seeders)
- Infraestructura AWS (EC2, RDS)
- CI/CD Backend

### Óscar Calatayud
- Frontend Vue (componentes, Pinia stores)
- Integración con API (servicios Axios)
- Infraestructura DNS y HTTPS
- CI/CD Frontend

### Compartido
- Code reviews mutuos
- Documentación
- Testing
- Resolución de merge conflicts

---

## 📦 Versionado

Usamos **Semantic Versioning** (SemVer):

```
MAJOR.MINOR.PATCH

Ejemplo: 1.2.3
```

- **MAJOR**: Cambios incompatibles (breaking changes)
- **MINOR**: Nueva funcionalidad compatible
- **PATCH**: Bugfixes compatibles

### Releases

- Crear tags en `main` después de merge desde `develop`
- Documentar changelog en GitHub Releases

```bash
git tag -a v1.0.0 -m "Release 1.0.0: Primera versión producción"
git push origin v1.0.0
```

---

## ❓ Dudas

Para cualquier duda sobre el flujo de trabajo o contribución, abrir una **Discussion** en GitHub o contactar directamente al equipo.

**Email**: [tu-email]@alu.edu.gva.es
