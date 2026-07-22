<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>


## Requerimientos

### PHP 8.2

Habilitar extensiones
gd
openssl
pdo_mysql
pdo_pgsql
pgsql
sodium
zip

Configurar php
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M

### Apache 2.4

## Configurar .env

Ejecutar:
cp .env.example .env

Completar variables de entorno de la aplicacion.

APP_NAME=

### Configuracion mysql

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

## Instalacion

### Composer
Ejecutar los comandos
composer install
composer update
composer autodump

### Node.js
Ejecutar los comandos 
npm install
npm update

### Laravel
Ejecutar los comandos
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
