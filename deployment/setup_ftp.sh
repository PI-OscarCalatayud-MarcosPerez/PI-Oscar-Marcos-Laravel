#!/bin/bash

# setup_ftp.sh
# Instala y configura vsftpd

# 1. Instalar vsftpd
if ! command -v vsftpd &> /dev/null; then
    apt update
    apt install -y vsftpd
fi

# 2. Configurar vsftpd
mv /etc/vsftpd.conf /etc/vsftpd.conf.bak
cp vsftpd/vsftpd.conf /etc/vsftpd.conf

# 3. Configurar lista de usuarios permitidos
# userlist_deny=NO implica que solo los usuarios en la lista pueden conectar
echo "app" > /etc/vsftpd.user_list
echo "backup_user" >> /etc/vsftpd.user_list
echo "oscar" >> /etc/vsftpd.user_list
echo "marcos" >> /etc/vsftpd.user_list
# NO añadimos 'ubuntu' ni 'root'

# 4. Crear directorio secure_chroot_dir si no existe
mkdir -p /var/run/vsftpd/empty

# 5. Reiniciar servicio
systemctl restart vsftpd
echo "FTP configurado. Usuarios permitidos: app, backup_user, student1, student2"
