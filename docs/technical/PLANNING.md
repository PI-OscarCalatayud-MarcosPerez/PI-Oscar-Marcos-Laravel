# 📅 Cronograma de Trabajo - Sprint 3

Este documento detalla la planificación y reparto de tareas entre **Marcos** y **Óscar** para la Iteración 3 del proyecto.

## 👥 Reparto de Roles
- **Marcos Pérez**
- **Óscar Calatayud**

## 🗓️ Planificación Semanal (Gantt Simplificado)

| Fase          | Tarea                                          | Responsable  | Estado |
| :------------ | :--------------------------------------------- | :----------- | :----- |
| **S1: Setup** | Creación proyecto Laravel y config `.env`      | Marcos       | ✅      |
| **S1: Setup** | Configuración Docker y stack MySQL             | Marcos       | ✅      |
| **S2: Data**  | Migraciones y Modelos (Product, User)          | Marcos       | ✅      |
| **S2: Data**  | Seeders de productos y usuarios                | Marcos       | ✅      |
| **S2: Auth**  | Instalación y configuración Laravel Breeze     | Marcos       | ✅      |
| **S3: Feat**  | Lógica de importación de productos (CSV/Excel) | Marcos       | ✅      |
| **S3: Feat**  | API Base `/api/products` para Sprint 4         | Marcos       | ✅      |
| **S3: UI**    | Diseño de vistas responsivas (Blade/Vue)       | Óscar        | ✅      |
| **S4: Admin** | CRUD administrativo de productos               | Marcos       | ⚠️      |
| **S4: Test**  | Ejecución de tests automatizados               | Óscar/Marcos | ⚠️      |
| **S4: Docs**  | README, Comparación Breeze vs Manual           | Óscar        | ⚠️      |

## 📋 Detalle de Tareas

### Marcos Pérez (Backend/Infrastructure)
1. **Configuración Inicial**: Inicializar Laravel dentro del repo y conectar con la DB de producción.
2. **Eloquent**: Definir el esquema de la base de datos para sustituir los archivos JSON.
3. **Seguridad**: Implementar Breeze para sustituir el sistema de login manual anterior.
4. **Importación**: Crear el controlador `ProductImportController` para carga masiva.
5. **API**: Preparar los endpoints REST para que Óscar pueda trabajar en el Sprint 4 con Vue.

### Óscar Calatayud (Frontend/General)
1. **Integración UI**: Asegurar que el diseño de los sprints anteriores se mantiene en la nueva arquitectura.
2. **Vue Migration**: Liderar el salto a SPA (Single Page Application) coordinando con la API de Marcos.
3. **Control de Calidad**: Validar los flujos de usuario en el login y registro.
4. **Documentación**: Mantener actualizado el README y el manual de usuario con los nuevos endpoints y flujo de instalación.

---
> [!NOTE]
> Este cronograma se actualiza semanalmente según el progreso del equipo.
