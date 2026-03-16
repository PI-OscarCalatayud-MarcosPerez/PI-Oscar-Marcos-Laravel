# Estado del Proyecto vs Requisitos

## ✅ HECHO (Lo que ya tienes)

*   **Aplicación funcionando (Parte 1 parcial):** Tienes Vue (frontend) y Laravel (backend) desplegados y comunicándose.
*   **Despliegue en AWS (Parte 4 parcial):** Tienes una instancia EC2 con todo instalado (Apache, MySQL, PHP, etc.).
*   **SSL (Parte 3 parcial):** Tienes HTTPS configurado con certificados autofirmados (falta Let's Encrypt real).
*   **Usuarios y Permisos:** Tienes usuarios configurados en el sistema operativo y FTP.
*   **Backup (Parte 4 parcial):** Tienes un script de backup diario.

## ❌ FALTA (Lo que pide el documento)

### 🧭 PART 1: DNS
*   [ ] **Dominio real:** Ahora mismo usas la IP (`13.222.71.248`). Necesitas configurar una zona DNS tipo `projecteXX.ddaw.es`.
*   [ ] **Registros DNS:** Configurar los registros `A`, `CNAME`, etc. en un servidor DNS.

### 🐳 PART 2: Docker en Desarrollo (Local)
*   [ ] **Dockerizar:** No usas Docker para desarrollar en local. Necesitas crear `Dockerfile` para Vue y Laravel y un `docker-compose.yml` para levantar todo junto con un comando.

### 🚀 PART 3: CI/CD (Automatización)
*   [ ] **Pipeline Automático:** Ahora despliegas ejecutando un script manual (`upload_changes.sh`). Necesitas configurar GitHub Actions o GitLab CI para que al hacer `git push`, se despliegue solo.
*   [ ] **HTTPS Real:** Necesitas certificados válidos (Let's Encrypt), no los autofirmados que dan error en el navegador.

### ☁️ PART 4: Arquitectura AWS Avanzada
*   [ ] **VPC y Subredes:** Ahora mismo está todo en una máquina (EC2). Piden separar:
    *   **VPC Propia:** Red privada virtual.
    *   **Subredes:** Públicas para el balanceador, privadas para la App y Datos.
*   [ ] **Balanceador:** Usar un Load Balancer (ALB) delante de la EC2.
*   [ ] **Base de Datos Separada (RDS):** Ahora MySQL está instalado DENTRO de la misma máquina que la web. Piden usar **AWS RDS** (servicio de base de datos gestionado).

### 📂 PART 5: Documentación
*   [ ] **Documentar todo:** Tienes un `README.md` básico, pero piden documentación detallada de arquitectura, despliegue, CI/CD, etc.

## Resumen del Plan de Acción
1.  **Prioridad 1 (Docker Local):** Crear `Dockerfile` y `docker-compose.yml` para cumplir la Parte 2.
2.  **Prioridad 2 (DNS y HTTPS):** Configurar el dominio real y Let's Encrypt.
3.  **Prioridad 3 (CI/CD):** Configurar GitHub Actions para despliegue automático.
4.  **Prioridad 4 (AWS Avanzado):** Esto es lo más complejo. Separar la BD a RDS y configurar la VPC. (Esto implicará migraciones y cambios de IP).
