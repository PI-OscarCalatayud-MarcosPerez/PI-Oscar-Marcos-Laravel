#!/bin/bash

# setup_apache.sh
# Instala y configura Apache con VirtualHosts y SSL

# 1. Instalar Apache, PHP, MySQL y utilidades
if ! command -v apache2 &> /dev/null; then
    apt update
    apt install -y apache2 apache2-utils
    
    # Instalar PHP 8.3 (Ubuntu 24.04) y extensiones para Laravel
    apt install -y php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath libapache2-mod-php8.3
    
    # Instalar MySQL Server
    apt install -y mysql-server
    
    # Instalar Composer y Unzip
    apt install -y unzip git composer
fi

# 2. Habilitar módulos
a2enmod ssl
a2enmod rewrite
a2enmod auth_basic
a2enmod php8.3

# 3. Generar Certificados SSL (Self-signed)
# App
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/app-selfsigned.key \
    -out /etc/ssl/certs/app-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Projecte/OU=IT/CN=app.projecteGrupX.es"

# Backup
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/backup-selfsigned.key \
    -out /etc/ssl/certs/backup-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Projecte/OU=IT/CN=backup.projecteGrupX.es"

# Test
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/test-selfsigned.key \
    -out /etc/ssl/certs/test-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Projecte/OU=IT/CN=test.projecteGrupX.es"

# 4. Copiar configuraciones
cp apache/projecte-app.conf /etc/apache2/sites-available/
cp apache/projecte-backup.conf /etc/apache2/sites-available/
cp apache/projecte-test.conf /etc/apache2/sites-available/

# 5. Crear usuario para acceso web seguro a backups
# Usuario 'admin_backup', password 'password123' (CAMBIAR EN PRODUCCIÓN)
htpasswd -cb /etc/apache2/.htpasswd admin_backup password123

# 6. Habilitar sitios y recargar apache
a2dissite 000-default.conf
a2ensite projecte-app.conf
a2ensite projecte-backup.conf
a2ensite projecte-test.conf

systemctl restart apache2
echo "Apache configurado correctamente."
