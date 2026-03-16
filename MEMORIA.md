# Memoria Técnica: Proyecto MOKeys

## 1. Introducción y Descripción del Proyecto

MOKeys es una plataforma de comercio electrónico especializada en la distribución y venta de claves digitales para videojuegos. El propósito principal del proyecto es ofrecer un ecosistema seguro y eficiente para que los usuarios finales puedan adquirir licencias de activación de software lúdico de forma inmediata.

La solución implementada se basa en un modelo de negocio de mercado digital (marketplace) con gestión de inventario dinámico. A diferencia de otros sistemas de comercio electrónico convencionales, MOKeys integra una arquitectura que vincula cada venta a un código de activación único y real, garantizando la integridad del stock y la satisfacción del cliente mediante la entrega automatizada de los productos adquiridos.

## 2. Arquitectura y Stack Tecnológico

El desarrollo de la plataforma se ha segmentado en capas diferenciadas para asegurar el desacoplamiento, la escalabilidad y la facilidad de mantenimiento.

### 2.1. Frontend
Se ha desarrollado una Single Page Application (SPA) utilizando las siguientes tecnologías:
- **Vue 3 (Composition API):** Framework progresivo para la construcción de interfaces de usuario reactivas.
- **Vite:** Herramienta de compilación y servidor de desarrollo optimizado para un rendimiento superior.
- **Pinia:** Sistema de gestión de estado global, utilizado para centralizar la información de la sesión del usuario y el carrito de compra.
- **Axios:** Cliente HTTP para la comunicación asíncrona con la API del backend.

### 2.2. Backend
El núcleo lógico del sistema se sustenta en una arquitectura robusta:
- **Laravel (API REST):** Framework de PHP que proporciona una estructura sólida basada en los patrones de Controladores y Servicios, permitiendo una separación clara entre la lógica de negocio y la gestión de peticiones.
- **MySQL:** Sistema de gestión de bases de datos relacionales para el almacenamiento persistente de usuarios, productos, claves y transacciones.

### 2.3. Infraestructura Local
Para garantizar la paridad entre los entornos de desarrollo y evitar conflictos de dependencias, se ha utilizado:
- **Docker y Docker Compose:** La infraestructura se ha orquestado mediante contenedores independientes para el frontend, el backend y el motor de base de datos, facilitando un despliegue replicable y aislado.

## 3. Funcionalidades Principales Implementadas

El sistema dispone de un conjunto de funcionalidades críticas para el funcionamiento de un marketplace digital:

### 3.1. Autenticación y Gestión de Usuarios
Se ha implementado un sistema de autenticación seguro que permite la gestión de roles (administrador y cliente). Los usuarios disponen de capacidades para la edición de su perfil personal y la eliminación de su cuenta, cumpliendo con estándares de privacidad y gestión de datos.

### 3.2. Catálogo de Productos y Filtros Dinámicos
La plataforma ofrece un catálogo visual de videojuegos que permite la navegación mediante filtros dinámicos basados en categorías y precios. Se ha integrado una lógica de cálculo de porcentajes para la visualización de ofertas, permitiendo al sistema destacar productos con descuentos temporales de forma automática.

### 3.3. Gestión de Stock Real
Se ha diseñado un esquema de base de datos que establece una relación de 1 a N entre los videojuegos y las claves únicas de activación. Cada juego puede contener múltiples códigos de stock; cuando se realiza una transacción, el sistema asigna una clave específica al pedido, descontándola automáticamente del inventario disponible.

### 3.4. Automatización de Correos Transaccionales
Para optimizar el flujo de post-venta, se ha integrado la plataforma con **n8n**. Mediante el uso de webhooks, el backend notifica la finalización de una compra exitosa, desencadenando un flujo de trabajo automatizado que envía el código de activación directamente al correo electrónico del cliente sin intervención manual.

## 4. Integración, Desplegamiento Continuo (CI/CD) y Cloud

La madurez técnica de la solución se refleja en su estrategia de despliegue y automatización.

### 4.1. Infraestructura en la Nube (AWS)
Se ha desplegado una infraestructura escalable en Amazon Web Services (AWS) que incluye:
- **Virtual Private Cloud (VPC) y Subredes:** Segmentación de red para mejorar la seguridad.
- **EC2 y Application Load Balancer (ALB):** Instancias de computación balanceadas para soportar cargas de tráfico variables.
- **Amazon RDS Multi-AZ:** Base de datos gestionada con alta disponibilidad mediante replicación en múltiples zonas de disponibilidad.

### 4.2. Pipelines de Automatización
Se han configurado flujos de trabajo en **GitHub Actions** para automatizar el ciclo de vida del software:
- Compilación y optimización del frontend.
- Ejecución de pruebas unitarias y de integración en el backend.
- Despliegue automático hacia la infraestructura de producción tras la validación de los tests.

### 4.3. Seguridad y Certificación
La plataforma cuenta con una configuración de DNS delegado y certificados SSL/TLS emitidos mediante Let's Encrypt, asegurando que toda la comunicación entre el cliente y el servidor se realice bajo el protocolo HTTPS.

## 5. Conclusión

El proyecto MOKeys representa una solución técnica integral que aborda los desafíos actuales del comercio electrónico de productos digitales. La combinación de un frontend reactivo moderno, un backend robusto con lógica de servicios y una infraestructura en la nube automatizada mediante procesos de CI/CD, proporciona una herramienta de alta calidad, segura y lista para operar en un entorno de producción real. El valor técnico reside en la integración coherente de tecnologías de vanguardia para ofrecer una experiencia de usuario fluida y una gestión administrativa eficiente del stock digital.
