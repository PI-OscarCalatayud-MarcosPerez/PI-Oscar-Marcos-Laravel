# SA.3 Iteración: Migración a Laravel v2 (mínimo viable)

En este sprint damos el salto de la versión PHP + JSON Server (v1, que se mantiene intacta en `legacy-php/`) a una versión más profesional con Laravel (v2) dentro de la carpeta `laravel/`. El objetivo es construir el backend mínimo viable sobre MySQL, sin perder el trabajo anterior, para empezar a consolidar un ecosistema MVC real con migraciones, modelos y autenticación moderna.

Es el primer contacto “serio” con un framework grande. Partiremos del catálogo y usuarios del sprint anterior, los migraremos a MySQL vía Eloquent y dejaremos preparado el terreno para el futuro cliente SPA (Vue) y para el microservicio Node.js que compartirá la misma base de datos.

## 🎯 Objetivos de aprendizaje (DWES · DIW · DAW)
- [x] Entender y configurar un proyecto Laravel con .env, migraciones y Eloquent (DWES).
- [x] Aplicar buenas prácticas MVC y rutas en un framework PHP moderno (DAW).
- [x] Crear vistas Blade reutilizando el diseño del front antiguo (DIW) y servir datos desde MySQL, adaptándolas para diseño responsivo (CSS Grid, media queries).
- [x] Integrar autenticación Laravel Breeze y compararla con la autenticación manual de PHP (DWES/Seguridad).
- [x] Automatizar la importación de Excel a base de datos desde Laravel, validando formatos y campos (DWES).
- [x] Realizar pruebas básicas (artisan test o pruebas manuales guiadas) para validar productos, autenticación e imports (DWES RA8.g).
- [x] Dejar la puerta abierta a la futura API REST para SPA Vue y microservicio Node (DWEC/DWES).

## 🌐 Relación con el proyecto integrador
Este sprint corresponde a la Iteración 3 (backend Laravel + DIW responsivo + pruebas) del enunciado global (`docs/projecte.md`).
- [x] Se mantiene toda la v1 dentro de `legacy-php/` (PHP + JSON Server + front antiguo). No se toca, pero se puede reutilizar estilo y JS.
- [x] Se crea `laravel/` con el backend v2 profesional. `docker-compose.yml` se puede ampliar para incluir servicio PHP-FPM + Nginx/Apache compartiendo MySQL con el resto de servicios.
- [x] MySQL será la BBDD común para Laravel y, en sprints futuros, para el microservicio Node.js (estadísticas/recomendaciones con Swagger).
- [x] La parte cliente de los sprints 1 y 2 se versiona como legacy; en este sprint se exportan componentes y CSS a Blade, y se refuerzan las validaciones y los comentarios con JS mientras no llega la SPA Vue.

## 🧩 Tareas / Historias de usuario

### C1 – Creación del proyecto Laravel y configuración del entorno
**Contexto:** Hace falta un esqueleto Laravel operativo dentro del repositorio único.
- [x] Crear la carpeta `laravel/` e inicializar el proyecto con Laravel (instalación habitual vía Composer).
- [x] Configurar `.env` para MySQL (misma instancia que legacy) y generar la clave de aplicación.
- [x] Revisar el stack de contenedores existente del landing: si ya tienes `docker-compose.yml` para el front, mantenlo intacto y decide si Laravel compartirá la BBDD vía ese compose.

### C2 – Modelo de datos y migraciones (products, users)
**Contexto:** Trasladamos el esquema de `products.json` y `users.json` a MySQL con migraciones.
- [x] Crear migración y modelo Product (vía generator de Laravel). Campos inspirados en `products.json`: sku, name, description, price, stock, image, category, timestamps. Añadir índice único por sku.
- [x] Reutilizar la migración de usuarios por defecto de Laravel (users), adaptando solo si hacen falta campos extra básicos.
- [x] Ejecutar migraciones contra la BBDD MySQL.
- [x] Añadir `database/seeders/ProductSeeder` con unos cuantos productos de prueba para validar la vista.

### C3 – Autenticación con Laravel Breeze
**Contexto:** Sustituimos la autenticación manual en PHP por una solución integrada.
- [x] Instalar Breeze y escoger versión Blade (no SPA aún).
- [x] Compilar assets con la herramienta que toque (npm, Vite).
- [x] Verificar rutas `/register` y `/login` funcionales con usuarios guardados en MySQL.
- [x] Comparar en una nota breve (README) el flujo Breeze vs. autenticación manual del Sprint 2.

### C4 – Importación de Excel a la BBDD (command o controlador)
**Contexto:** Reaprovechamos el flujo de Excel del Sprint 2 pero ahora todo va directamente a MySQL vía Laravel.
- [x] Añadir dependencia para gestionar Excel (Laravel-Excel o PhpSpreadsheet).
- [x] Crear un command o un controlador con formulario de upload que lea el Excel e inserte/actualice productos.
- [x] Validar campos obligatorios (sku, name, price, stock) y formatos numéricos. Gestionar errores amigables.
- [x] Guardar imagen o ruta de imagen según datos disponibles.
- [x] Registrar logs/resúmenes de importación (número de líneas, errores) y mostrar feedback en terminal o vista.

### C5 – Vista Blade de listado de productos y primera API /api/products
**Contexto:** Necesitamos una salida visual y un endpoint inicial para el futuro cliente SPA.
- [x] Crear ruta pública `/productos` en `web.php` que consulte `Product::all()` y pase datos a una vista Blade.
- [x] Maquetar una vista `resources/views/productos/index.blade.php` con tarjetas/grids reutilizando el estilo del front antiguo (DIW).
- [x] Exponer una ruta GET `/api/products` sencilla en `routes/api.php` que retorne JSON de productos (sin auth).
- [x] API comentarios/valoraciones (base backend): crear la migración comments o reviews, el modelo Eloquent y un controlador borrador con index y store + rutas base.
- [x] Añadir un pequeño texto al README indicando que en sprints futuros el cliente Vue consumirá esta API.

### C6 – Validaciones y comentarios/valoraciones en el cliente (JS provisional)
**Contexto:** Continuamos utilizando JS en cliente para cubrir comentarios y validaciones mientras no llega la SPA Vue.
- [x] Validación auth: Breeze ya aplica validaciones servidor.
- [x] Validación contacto: reutilizar la validación del formulario de contacto del front antiguo.
- [x] API comentarios/valoraciones (ejecución completa): acabar la migración, implementar lógica real en store e index con validaciones.
- [x] Bloque UI: añadir al Blade de productos un formulario de comentarios/valoraciones y la lista de comentarios consumiendo la API con fetch/AJAX.
- [x] Provisionalidad: documentar que esta solución es temporal hasta la SPA Vue.

### C7 – Pruebas básicas con Laravel
**Contexto:** Validar la API construida (productos y comentarios/valoraciones) con tests automatizados de Laravel.
- [x] Tests de API productos: GET `/api/products` (200 y estructura básica).
- [x] Tests de comentarios/valoraciones: store e index con producto existente; validar campos obligatorios.
- [x] Prueba de importación: test de command/controlador de Excel.
- [x] Documentar resultados: listar qué se ha probado, datos utilizados y estado.

## 📦 Entregables del sprint
- [x] Código Laravel dentro de `laravel/` con migraciones, modelos, rutas, vistas y autenticación Breeze funcional.
- [x] Infraestructura docker clarificada.
- [x] Documentación mínima en el README.md.
- [x] Captura o GIF breve de la vista `/productos` mostrando tarjetas.
- [x] Evidencia de pruebas: tests Laravel sobre API de productos y de comentarios/valoraciones.
- [x] Evidencia de planificación y ejecución.

## ✅ Criterios de evaluación
- **Laravel core:** Migraciones correctas, modelos Eloquent, rutas y controladores limpios.
- **Autenticació:** Breeze operativo (registro/login/logout).
- **Importación Excel:** Carga a products con validaciones y gestión de errores.
- **DIW:** Vista Blade coherente con la estética del Sprint 2 (responsivo, tarjetas claras).
- **Calidad de código:** Nomenclatura clara, archivos en la carpeta adecuada, comentarios mínimos y útiles, README actualizado.
- **Integración:** `legacy-php/` preservado; nueva API `/api/products` disponible.
- **Pruebas:** Tests Laravel sobre API de productos y comentarios/valoraciones.
- **Gestión de proyecto:** Planificación y ejecución evidenciadas.
