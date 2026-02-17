# Instrucciones de Despliegue en AWS

Este directorio contiene los scripts y configuraciones necesarios para desplegar el servidor web seguro.

## Pasos Previos
1. Lanzar instancia Ubuntu en AWS.
2. Asegurar que los puertos 80, 443, 22 y 30000-30050 están abiertos en el Security Group.
3. Subir esta carpeta `deployment` al servidor (ej. con `scp` o `rsync`).

## Ejecución
Ejecutar los scripts como root (o con sudo) en el siguiente orden:

1. **Configuración de Usuarios y Sistema**:
   ```bash
   sudo ./setup_users.sh
   ```
   *Nota*: Este script crea usuarios y modifica la configuración de SSH. Asegúrese de tener su clave pública configurada correctamente para el usuario `ubuntu` antes de desconectar, o podría perder acceso si algo falla con la autenticación. El script deshabilita el login con contraseña.

2. **Configuración de Servidor Web (Apache)**:
   ```bash
   sudo ./setup_apache.sh
   ```
   Genera certificados SSL autofirmados y configura los VirtualHosts. Crea un usuario `admin_backup` (pass: `password123`) para el acceso web a backups.

3. **Configuración de FTP (vsftpd)**:
   ```bash
   sudo ./setup_ftp.sh
   ```
   Configura FTP seguro modo pasivo. Solo usuarios `app`, `backup_user` y estudiantes pueden acceder.

4. **Automatización de Backups**:
   Configurar el cron manualmente o añadir al script de usuarios:
   ```bash
   # Añadir al crontab de root
   0 3 * * * /path/to/deployment/daily_backup.sh
   ```

## Usuarios y Contraseñas
- **SSH**: Usuario `ubuntu` (Clave pública).
- **FTP**:
    - `app`: acceso a `/home/app/ftp/www`
    - `backup_user`: acceso a `/home/backup/ftp/fitxers`
    - Estudiantes (`student1`, etc.): acceso a sus homes o directorio test.
- **Web Backup**: Usuario `admin_backup`, Contraseña `password123` (Cambiado en `setup_apache.sh`).

## Notas Importantes
- Reemplazar `projecteGrupX.es` por el dominio real en los archivos `.conf` antes de ejecutar `setup_apache.sh` si es necesario, o editar después en `/etc/apache2/sites-available/`.
- El script de backups debe tener permisos de ejecución (`chmod +x daily_backup.sh`).
