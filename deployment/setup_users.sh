#!/bin/bash

# setup_users.sh
# Script para configurar usuarios y seguridad SSH
# Ejecutar con sudo

# 1. Crear usuarios app y backup
echo "Creando usuarios y directorios..."

# Usuario app
if ! id "app" &>/dev/null; then
    useradd -m -d /home/app -s /bin/bash app
    echo "Usuario 'app' creado."
fi
mkdir -p /home/app/ftp/www
mkdir -p /home/app/logs
chown -R app:app /home/app

# Usuario backup
if ! id "backup_user" &>/dev/null; then
    useradd -m -d /home/backup -s /bin/bash backup_user
    echo "Usuario 'backup_user' creado."
fi
mkdir -p /home/backup/ftp/fitxers
chown -R backup_user:backup_user /home/backup

# 2. Configurar SSH
echo "Configurando SSH..."
SSHD_CONFIG="/etc/ssh/sshd_config"

# Backup config original
cp $SSHD_CONFIG "${SSHD_CONFIG}.bak"

# Deshabilitar root login
sed -i 's/^PermitRootLogin.*/PermitRootLogin no/' $SSHD_CONFIG
# Autenticación solo con clave pública (deshabilitar contraseña)
sed -i 's/^PasswordAuthentication.*/PasswordAuthentication no/' $SSHD_CONFIG
# Asegurar que PubkeyAuthentication está activado
sed -i 's/^#PubkeyAuthentication.*/PubkeyAuthentication yes/' $SSHD_CONFIG

# Mensaje de bienvenida (Banner)
echo "Benvingut a la instància de Servidor Web en AWS de NOM COGNOM DE CADA MEMBRE DEL GRUP" > /etc/ssh/banner_welcome
# Configurar Banner en sshd_config (si no existe, lo añade al final)
if grep -q "^Banner" $SSHD_CONFIG; then
    sed -i 's|^Banner.*|Banner /etc/ssh/banner_welcome|' $SSHD_CONFIG
else
    echo "Banner /etc/ssh/banner_welcome" >> $SSHD_CONFIG
fi

# 3. Usuarios Alumnos
for alumno in oscar marcos; do
    if ! id "$alumno" &>/dev/null; then
        useradd -m -s /bin/bash $alumno
        echo "Usuario '$alumno' creado."
        # Añadir permisos para que puedan subir ficheros a /home/app/ftp/www
    fi
done

# Grupo para publicación web
groupadd www-pub
usermod -aG www-pub app
usermod -aG www-pub oscar
usermod -aG www-pub marcos

# Permisos de grupo en directorio web
chown -R app:www-pub /home/app/ftp/www
chmod -R 775 /home/app/ftp/www
# Bit setgid para mantener grupo
chmod g+s /home/app/ftp/www

echo "Configuración de usuarios completada. Recuerda reiniciar sshd: systemctl restart sshd"
