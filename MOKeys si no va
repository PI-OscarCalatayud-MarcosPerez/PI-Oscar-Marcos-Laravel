de normal 

sudo make up

_______________________________________________________________________________________________________
BASE DE DATOS ERRORES

si no va la base de datos : sudo systemctl stop mysql o sudo systemctl stop mariadb


Si hay error en la base de datos

sudo docker compose down -v && sudo docker compose up -d --build

________________________________________________________________________________________________________

si no cargan los prodcutos 
sudo docker exec laravel_app php artisan migrate --seed 2>&1
