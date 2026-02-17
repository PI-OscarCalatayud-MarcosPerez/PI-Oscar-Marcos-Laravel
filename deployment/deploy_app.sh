#!/bin/bash

# deploy_app.sh
# Script para desplegar la aplicación Laravel
# Ejecutar con sudo

APP_DIR="/home/app/ftp/www"
USER="app"
GROUP="www-pub"

echo "Iniciando despliegue de Laravel en $APP_DIR..."

# Guardar directorio actual (donde está el script y el tar.gz)
DEPLOY_ROOT=$(pwd)

# Copiar el archivo comprimido si está en el directorio actual
if [ -f "$DEPLOY_ROOT/project.tar.gz" ]; then
    echo "Copiando project.tar.gz a $APP_DIR..."
    cp "$DEPLOY_ROOT/project.tar.gz" "$APP_DIR/"
fi

cd $APP_DIR

# 1. Verificar si hay archivos
if [ -f "project.tar.gz" ]; then
    echo "Descomprimiendo código..."
    tar -xzf project.tar.gz
    rm project.tar.gz
    # Mover contenido si se descomprimió en una subcarpeta, o asumir que está en root
    # Ajustar según como se comprima
fi

# 2. Configurar permisos (ANTES de composer para poder escribir en vendor)
echo "Configurando permisos..."
chown -R $USER:$GROUP $APP_DIR
chmod -R 775 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

# 3. Instalar dependencias PHP
echo "Instalando dependencias de Composer..."
# Ejecutar como usuario 'app' para no tener problemas de permisos luego
sudo -u $USER composer install --no-dev --optimize-autoloader --no-interaction

# 4. Configurar .env
if [ ! -f .env ]; then
    echo "Creando .env desde .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

# Configurar DB en .env (Forzar valores correctos siempre)
echo "Configurando entorno..."
sed -i 's/# DB_/DB_/g' .env
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=laravel_db/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=laravel_user/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=password123/' .env

# Asegurar que APP_URL es correcta
sed -i 's|^APP_URL=.*|APP_URL=https://13.222.71.248|' .env

# Forzamos permiso de escritura en .env por si acaso
chmod 664 .env

# 5. Base de datos y Migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# 6. Cache
echo "Limpiando y cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Despliegue finalizado."
