#!/bin/bash
KEY="/home/batoi/Descargas/proyecto_clave.pem"
HOST="13.222.71.248"
USER="ubuntu"
DUMP_FILE="backup_migracion.sql"

chmod 400 "$KEY"

echo "Por favor, introduce los datos de la base de datos del servidor ANTIGUO (ORIGEN 13.222.71.248):"
read -p "Nombre de la base de datos (en el servidor antiguo): " DB_NAME
read -s -p "Contraseña de root MySQL (en el servidor antiguo): " DB_PASS
echo ""

echo "Conectando a $HOST..."
ssh -i "$KEY" -o StrictHostKeyChecking=no "$USER@$HOST" exit
if [ $? -ne 0 ]; then
    echo "ERROR: No se pudo conectar por SSH. Verifica tu conexión a internet y que la instancia siga encendida."
    exit 1
fi

echo "Realizando volcado de datos (intentando con sudo)..."
ssh -i "$KEY" -o StrictHostKeyChecking=no "$USER@$HOST" "sudo mysqldump -u root -p'$DB_PASS' '$DB_NAME' > ~/$DUMP_FILE"

if [ $? -ne 0 ]; then
    echo "ERROR: Falló el comando mysqldump remoto."
    echo "--------------------------------------------------------"
    echo "Parece que el nombre de la base de datos '$DB_NAME' NO EXISTE."
    echo "Aquí tienes la lista de bases de datos en el servidor:"
    echo "--------------------------------------------------------"
    ssh -i "$KEY" -o StrictHostKeyChecking=no "$USER@$HOST" "sudo mysql -u root -p'$DB_PASS' -e 'SHOW DATABASES;'"
    echo "--------------------------------------------------------"
    echo "Por favor, vuelve a ejecutar el script usando uno de los nombres de arriba."
    exit 1
fi

echo "Descargando archivo sql..."
scp -i "$KEY" -o StrictHostKeyChecking=no "$USER@$HOST":~/$DUMP_FILE ./$DUMP_FILE

if [ $? -eq 0 ]; then
    echo "MIGRACIÓN COMPLETADA EXITOSAMENTE"
    echo "Archivo guardado en: $(pwd)/$DUMP_FILE"
else
    echo "ERROR: Falló la descarga SCP."
    exit 1
fi
