# Configuración de GitHub Secrets - MOKeys

Este documento detalla todos los secretos que debes configurar en GitHub para que los pipelines de CI/CD funcionen correctamente.

## 📍 Dónde Configurar

GitHub → Tu Repositorio → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

---

## 🔐 Secretos Requeridos

### 1. AWS Credentials

Necesarios para autenticarte en AWS (ECR, EC2, S3, CloudFront):

| Nombre                  | Descripción                       | Ejemplo                                    |
| ----------------------- | --------------------------------- | ------------------------------------------ |
| `AWS_ACCESS_KEY_ID`     | Access Key de IAM con permisos    | `AKIAIOSFODNN7EXAMPLE`                     |
| `AWS_SECRET_ACCESS_KEY` | Secret Access Key correspondiente | `wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY` |
| `AWS_REGION`            | Región donde están tus recursos   | `us-east-1`                                |

**Cómo obtenerlos**:
1. AWS Console → IAM → Users → Tu usuario → Security credentials
2. Create access key → Use case: CLI
3. **Guarda ambas claves** (el secret solo se muestra una vez)

**Permisos mínimos necesarios**:
- `AmazonEC2ContainerRegistryFullAccess` (para ECR)
- `AmazonS3FullAccess` (si usas S3 para frontend)
- `CloudFrontFullAccess` (si usas CloudFront)

---

### 2. ECR Registry

| Nombre         | Descripción                   | Ejemplo                                        |
| -------------- | ----------------------------- | ---------------------------------------------- |
| `ECR_REGISTRY` | URL completa del registro ECR | `123456789012.dkr.ecr.us-east-1.amazonaws.com` |

**Cómo obtenerlo**:
1. AWS Console → ECR → Repositories
2. Copiar el URI del repositorio (sin el nombre del repo al final)

---

### 3. EC2 Backend

Para desplegar el backend Laravel:

| Nombre        | Descripción                            | Ejemplo                                       |
| ------------- | -------------------------------------- | --------------------------------------------- |
| `EC2_HOST`    | IP pública de la instancia EC2 backend | `3.85.123.45`                                 |
| `EC2_USER`    | Usuario SSH                            | `ec2-user` (Amazon Linux) / `ubuntu` (Ubuntu) |
| `EC2_SSH_KEY` | Clave privada SSH completa             | (Ver formato abajo)                           |

**Formato de `EC2_SSH_KEY`**:
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlw...
[múltiples líneas]
...AAAAEC2F5c2hhMjU2AAAAGQAAAA13d3cuZXhhbXBsZS5jb20AAAAEbm9uZQAAAAEa
-----END OPENSSH PRIVATE KEY-----
```

**Importante**:
- Incluye las líneas `-----BEGIN` y `-----END`
- Mantén todos los saltos de línea
- Es la misma clave `.pem` que usas para conectarte por SSH

---

### 4. EC2 Frontend (Opción 2: Si NO usas S3)

Solo necesario si despliegas el frontend en EC2 en vez de S3:

| Nombre              | Descripción                             |
| ------------------- | --------------------------------------- |
| `EC2_FRONTEND_HOST` | IP pública de la instancia EC2 frontend |

**Nota**: Si usas la misma EC2 para ambos, puedes usar el mismo valor que `EC2_HOST`.

---

### 5. S3 + CloudFront (Opción 1: Si SÍ usas S3)

Para desplegar el frontend en S3 con CloudFront:

| Nombre                       | Descripción                      | Ejemplo           |
| ---------------------------- | -------------------------------- | ----------------- |
| `S3_BUCKET`                  | Nombre del bucket S3             | `mokeys-frontend` |
| `CLOUDFRONT_DISTRIBUTION_ID` | ID de la distribución CloudFront | `E1234567890ABC`  |

**Cómo obtener CLOUDFRONT_DISTRIBUTION_ID**:
1. AWS Console → CloudFront → Distributions
2. Copiar el ID de tu distribución

**Nota**: Deja `S3_BUCKET` vacío si usas EC2 para frontend.

---

### 6. Variables de Entorno de la Aplicación

| Nombre              | Descripción                 | Ejemplo                          |
| ------------------- | --------------------------- | -------------------------------- |
| `VITE_API_BASE_URL` | URL de la API en producción | `https://api.mokeys.com` |

---

## ✅ Checklist de Configuración

Marca lo que ya has configurado:

**AWS**:
- [ ] `AWS_ACCESS_KEY_ID`
- [ ] `AWS_SECRET_ACCESS_KEY`
- [ ] `AWS_REGION`
- [ ] `ECR_REGISTRY`

**Backend**:
- [ ] `EC2_HOST`
- [ ] `EC2_USER`
- [ ] `EC2_SSH_KEY`

**Frontend (elige UNA opción)**:

Opción A - S3:
- [ ] `S3_BUCKET`
- [ ] `CLOUDFRONT_DISTRIBUTION_ID`

Opción B - EC2:
- [ ] `EC2_FRONTEND_HOST`

**Variables de App**:
- [ ] `VITE_API_BASE_URL`

---

## 🧪 Verificar Configuración

Después de configurar todos los secretos, haz un test:

1. **Crear una rama de prueba**:
   ```bash
   git checkout -b test/ci-cd-setup
   echo "test" >> test.txt
   git add test.txt
   git commit -m "test: verificar CI/CD"
   git push origin test/ci-cd-setup
   ```

2. **Cambiar el workflow temporalmente** (en `.github/workflows/deploy-backend.yml`):
   ```yaml
   on:
     push:
       branches: [main, test/ci-cd-setup]  # Añadir tu rama de test
   ```

3. **Observar el workflow**:
   - GitHub → Actions → Debería aparecer el workflow ejecutándose
   - Si falla, revisa los logs para ver qué secreto falta

4. **Limpiar** después del test:
   ```bash
   git checkout main
   git branch -D test/ci-cd-setup
   git push origin --delete test/ci-cd-setup
   ```

---

## 🔒 Seguridad

**Nunca compartas**:
- ❌ Claves SSH privadas
- ❌ AWS Access Keys
- ❌ Cualquier secret de GitHub

**Buenas prácticas**:
- ✅ Crea usuarios IAM específicos para CI/CD (no uses root)
- ✅ Usa el principio de mínimo privilegio en permisos IAM
- ✅ Rota las claves AWS cada 90 días
- ✅ Revisa logs de CloudTrail para detectar uso sospechoso

---

## 📞 Ayuda

Si algún workflow falla:

1. **Revisar logs**: GitHub → Actions → Click en el workflow fallido → Click en el job → Expandir el step que falló
2. **Errores comunes**:
   - `Error: Could not find ECR repository`: El repositorio ECR no existe o el nombre es incorrecto
   - `Permission denied (publickey)`: La clave SSH es incorrecta o falta
   - `AccessDenied` (AWS): El usuario IAM no tiene los permisos necesarios

**Documentación oficial**:
- [GitHub Actions Secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [AWS IAM Best Practices](https://docs.aws.amazon.com/IAM/latest/UserGuide/best-practices.html)

---

**Autores**: Marcos Pérez & Óscar Calatayud
