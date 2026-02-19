#!/bin/bash

# Configuration
KEY="/home/batoi/Descargas/proyecto_clave.pem"
EC2_HOST="34.203.202.61"
EC2_USER="ubuntu"
RDS_HOST="projecte-db.ch1eaqti0rre.us-east-1.rds.amazonaws.com"
RDS_USER="admin"
RDS_PASS=""
DB_NAME="MOKeys"
DUMP_FILE="backup_migracion.sql"

chmod 400 "$KEY"

echo "---------------------------------------------------"
echo "  SUBIDA DE DATOS A AWS RDS (vía EC2 Jumpbox)"
echo "---------------------------------------------------"

echo "Por favor, introduce la contraseña del USUARIO 'admin' del RDS:"
read -s -p "Contraseña RDS: " RDS_PASS
echo ""

# 1. Upload SQL file to EC2
echo "[1/4] Subiendo $DUMP_FILE a la EC2 ($EC2_HOST)..."
scp -i "$KEY" -o StrictHostKeyChecking=no "$DUMP_FILE" "$EC2_USER@$EC2_HOST:~/$DUMP_FILE"
if [ $? -ne 0 ]; then
    echo "ERROR: Falló la subida del archivo SCP."
    exit 1
fi

# 2. Install MySQL Client on EC2 if needed
echo "[2/4] Instalando cliente MySQL en la EC2..."
ssh -i "$KEY" -o StrictHostKeyChecking=no "$EC2_USER@$EC2_HOST" \
    "sudo apt-get update -qq && sudo apt-get install -y mysql-client -qq"

# 3. Create Database in RDS
echo "[3/4] Creando base de datos '$DB_NAME' en RDS..."
ssh -i "$KEY" -o StrictHostKeyChecking=no "$EC2_USER@$EC2_HOST" \
    "mysql -h '$RDS_HOST' -u '$RDS_USER' -p'$RDS_PASS' -e 'CREATE DATABASE IF NOT EXISTS $DB_NAME;'"

# 4. Import Data to RDS
echo "[4/4] Inyectando datos en RDS..."
ssh -i "$KEY" -o StrictHostKeyChecking=no "$EC2_USER@$EC2_HOST" \
    "mysql -h '$RDS_HOST' -u '$RDS_USER' -p'$RDS_PASS' '$DB_NAME' < ~/$DUMP_FILE"

if [ $? -eq 0 ]; then
    echo "---------------------------------------------------"
    echo "  MIGRACIÓN A RDS COMPLETADA EXITOSAMENTE"
    echo "---------------------------------------------------"
else
    echo "ERROR: Falló la importación de datos en RDS."
    exit 1
fi
