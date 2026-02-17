#!/bin/bash

# setup_database.sh
# Script para crear la base de datos y usuario de Laravel
# Ejecutar con sudo

DB_NAME="laravel_db"
DB_USER="laravel_user"
DB_PASS="password123" # CAMBIAR POR ALGO SEGURO O GENERAR

echo "Configurando MySQL..."

# Crear DB y Usuario
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "Base de datos '$DB_NAME' creada. Usuario '$DB_USER' configurado."
