#!/bin/bash
set -e

# Inicializar estructura de storage si el volumen está vacío
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/testing
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Fijar permisos (funciona correctamente en named volumes Linux)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instalar dependencias de Composer si no están
if [ ! -f /var/www/vendor/autoload.php ]; then
    echo "Instalando dependencias de Composer..."
    cd /var/www && composer install --no-interaction --prefer-dist --optimize-autoloader
fi

exec "$@"
