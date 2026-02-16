# 🚀 Guía Rápida de Inicio - Despliegue AWS

## ✅ Lo que YA está listo

Todo el código y configuración están implementados:
- ✅ Dockerfiles de producción (backend y frontend)
- ✅ Workflows de CI/CD (GitHub Actions)
- ✅ CORS configurado para producción
- ✅ Documentación completa (arquitectura, contribución, etc.)

---

## 📝 Pasos que DEBES hacer ahora

### 1. Preparar tu Información

Antes de empezar, necesitas confirmar:
- **Número de grupo**: XX (para `mokeys.com`)
- **Dominio de producción**: `https://mokeys.com` y `https://api.mokeys.com`

### 2. Actualizar Placeholders

Buscar y reemplazar en TODOS los archivos creados:

```bash
# En tu editor, buscar "projecteXX" y reemplazar por "projecte05" (ejemplo)
# Archivos afectados:
- .env.example
- frontend/.env.production
- config/cors.php
- ARCHITECTURE.md
- README.md
- GITHUB_SECRETS.md
```

O usar comando:
```bash
cd /home/batoi/Documentos/PI-Marcos-Oscar-Final
find . -type f \( -name "*.md" -o -name "*.php" -o -name ".env*" \) -exec sed -i 's/projecteXX/projecte05/g' {} +
```

### 3. Crear Infraestructura AWS

Sigue **[ARCHITECTURE.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/ARCHITECTURE.md)** paso a paso:

1. **VPC y Subredes** (30 min)
2. **RDS MySQL Multi-AZ** (20 min)
3. **EC2 Instances** (15 min)
4. **Application Load Balancer** (15 min)
5. **ECR Repositories** (5 min)
6. **Route 53 DNS** (10 min)
7. **ACM Certificate** (10 min)

**Tiempo estimado total**: ~2 horas

### 4. Configurar GitHub Secrets

Sigue **[GITHUB_SECRETS.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/GITHUB_SECRETS.md)**:

1. Ir a GitHub → Repositorio → Settings → Secrets → Actions
2. Añadir TODOS los secretos listados
3. Verificar con un push de prueba

### 5. Configurar EC2 Backend (Primera vez)

```bash
# SSH a EC2
ssh -i tu-aws-key.pem ec2-user@<IP_EC2_BACKEND>

# Instalar Docker
sudo yum update -y
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -a -G docker ec2-user

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Crear directorio de app
mkdir -p ~/mokeys-backend
cd ~/mokeys-backend

# Crear .env de producción
cat > .env << 'EOF'
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TU_KEY_GENERADA
APP_URL=https://api.mokeys.com

DB_CONNECTION=mysql
DB_HOST=<TU_RDS_ENDPOINT>
DB_PORT=3306
DB_DATABASE=MOKeys
DB_USERNAME=admin
DB_PASSWORD=<TU_PASSWORD_RDS>

FRONTEND_URL=https://mokeys.com
SESSION_DOMAIN=.mokeys.com
SANCTUM_STATEFUL_DOMAINS=mokeys.com
EOF

# Crear docker-compose.yml básico
cat > docker-compose.yml << 'EOF'
version: "3.9"
services:
  app:
    image: <TU_ECR_REGISTRY>/mokeys-backend:latest
    container_name: mokeys_app
    environment:
      - APP_ENV=production
    env_file:
      - .env
    ports:
      - "9000:9000"
    restart: unless-stopped
EOF

# Reiniciar sesión para aplicar grupo docker
exit
```

### 6. Primer Deploy Manual (Verificación)

```bash
# En tu local
cd /home/batoi/Documentos/PI-Marcos-Oscar-Final

# Login a ECR desde local
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin <TU_ECR_REGISTRY>

# Build y push backend
docker build -f Dockerfile.prod -t <TU_ECR_REGISTRY>/mokeys-backend:latest .
docker push <TU_ECR_REGISTRY>/mokeys-backend:latest

# Build y push frontend
cd frontend
docker build -f Dockerfile.prod -t <TU_ECR_REGISTRY>/mokeys-frontend:latest .
docker push <TU_ECR_REGISTRY>/mokeys-frontend:latest

# SSH a EC2 backend y pull
ssh -i tu-aws-key.pem ec2-user@<IP_EC2_BACKEND>
cd ~/mokeys-backend
docker pull <TU_ECR_REGISTRY>/mokeys-backend:latest
docker-compose up -d

# Ejecutar migraciones PRIMERA VEZ
docker exec mokeys_app php artisan migrate --force
docker exec mokeys_app php artisan db:seed --force
```

### 7. Verificar Funcionamiento

1. **DNS**: `https://mokeys.com` debe resolver a ALB
2. **HTTPS**: Certificado válido (candado verde)
3. **Frontend**: Página principal carga correctamente
4. **Backend API**: `https://api.mokeys.com/api/products` devuelve JSON
5. **Login**: Puedes iniciar sesión
6. **Tests**: Usuario admin puede acceder a panel admin

### 8. Activar CI/CD Automático

**Una vez todo funcione manualmente**:

```bash
# En tu local
git add .
git commit -m "feat: añadir configuración de despliegue AWS completa"
git push origin main
```

GitHub Actions se activará automáticamente y desplegará en cada push futuro.

---

## 🎯 Checklist Final

**Código** (✅ Ya hecho):
- [x] Dockerfile.prod backend
- [x] Dockerfile.prod frontend
- [x] Workflows GitHub Actions
- [x] CORS actualizado
- [x] Documentación completa

**AWS** (📋 Pendiente):
- [ ] VPC con subredes
- [ ] RDS MySQL Multi-AZ
- [ ] EC2 Backend + Frontend
- [ ] ALB con HTTPS
- [ ] Route 53 DNS
- [ ] ECR repositories

**GitHub** (📋 Pendiente):
- [ ] Secrets configurados
- [ ] Push de prueba exitoso

**Verificación** (📋 Pendiente):
- [ ] Acceso HTTPS funcional
- [ ] Login funciona
- [ ] CRUD productos funciona
- [ ] Panel admin accesible

---

## 📞 Si algo falla

1. **Revisar los logs**:
   ```bash
   # EC2
   docker logs mokeys_app
   
   # GitHub Actions
   GitHub → Actions → Click en workflow fallido
   ```



---

## 📚 Documentación de Referencia

| Documento                                                                                  | Propósito                 |
| ------------------------------------------------------------------------------------------ | ------------------------- |
| [ARCHITECTURE.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/ARCHITECTURE.md)     | Guía completa de AWS      |
| [GITHUB_SECRETS.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/GITHUB_SECRETS.md) | Configurar CI/CD          |
| [CONTRIBUTING.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/CONTRIBUTING.md)     | Workflow del equipo       |
| [README.md](file:///home/batoi/Documentos/PI-Marcos-Oscar-Final/README.md)                 | Guía general del proyecto |


---

**¡Éxito con el despliegue!** 🚀

Si tienes dudas durante el proceso, consulta la documentación o revisa los logs de errores específicos.
