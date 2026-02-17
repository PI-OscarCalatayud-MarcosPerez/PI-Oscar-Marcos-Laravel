#!/bin/bash

# Configuration
KEY_PATH="/home/batoi/Descargas/proyecto_clave.pem"
SERVER_IP="13.222.71.248"
SERVER_USER="ubuntu"
REMOTE_DIR="~/deployment"

# 1. Empaquetar el proyecto (Excluyendo cosas pesadas e innecesarias)
echo "🗜️  Comprimiendo archivos..."
rm -rf deployment
mkdir -p deployment

# Preguntar si compilar assets
read -p "¿Quieres compilar frontend (npm run build)? [y/N] " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    npm run build
fi

tar -czf deployment/project.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.git' \
    --exclude='storage/*.key' \
    --exclude='.env' \
    --exclude='deployment/setup_*.sh' \
    --exclude='deployment/deploy_app.sh' \
    .

# 2. Subir archivos
echo "🚀 Subiendo archivos al servidor..."
scp -o StrictHostKeyChecking=no -i "$KEY_PATH" deployment/project.tar.gz "$SERVER_USER@$SERVER_IP:$REMOTE_DIR/"

# 3. Trigger deployment script remotely
echo "🔄 Ejecutando despliegue en remoto..."
ssh -o StrictHostKeyChecking=no -i "$KEY_PATH" "$SERVER_USER@$SERVER_IP" "sudo $REMOTE_DIR/deploy_app.sh"

echo "✅ ¡Actualización completada!"
