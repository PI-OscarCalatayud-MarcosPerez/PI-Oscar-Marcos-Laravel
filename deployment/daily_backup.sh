#!/bin/bash

# daily_backup.sh
# Script para realizar copias de seguridad de la aplicación web y base de datos
# Se debe configurar en cron para ejecución nocturna

BACKUP_ROOT="/home/backup/ftp/fitxers"
APP_ROOT="/home/app/ftp/www"
DATE=$(date +"%Y%m%d")
DAYS_TO_KEEP=7

# Crear directorio destino si no existe
mkdir -p $BACKUP_ROOT

# 1. Backup de Ficheros Web
echo "Iniciando backup de ficheros web..."
tar -czf "$BACKUP_ROOT/app_backup_$DATE.tar.gz" -C "$APP_ROOT" .

# 2. Backup de Bases de Datos (Ejemplo MySQL)
# Asegúrate de configurar .my.cnf para el usuario root o usar credenciales aquí
echo "Iniciando backup de base de datos..."
# mysqldump -u root --all-databases > "$BACKUP_ROOT/db_backup_$DATE.sql"
# O para una DB específica:
# mysqldump -u root nombre_db > "$BACKUP_ROOT/db_backup_$DATE.sql"

# 3. Rotación de Backups (Mantener últimos 7 días)
echo "Eliminando backups antiguos..."
find "$BACKUP_ROOT" -type f -name "app_backup_*.tar.gz" -mtime +$DAYS_TO_KEEP -delete
find "$BACKUP_ROOT" -type f -name "db_backup_*.sql" -mtime +$DAYS_TO_KEEP -delete

# Ajustar permisos para que el usuario backup_user pueda leerlos (lectura para grupo/otros o dueño)
chown backup_user:backup_user "$BACKUP_ROOT"/*
chmod 640 "$BACKUP_ROOT"/*

echo "Backup completado para $DATE."
