#!/bin/sh

# Asegurar que archivos creados por PHP sean legibles por nginx
umask 0022

echo "[Sistema de Salud] Iniciando setup..."

# 1. Instalar dependencias PHP si no están
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "[Sistema de Salud] Instalando dependencias Composer..."
    composer install --no-interaction --no-dev --optimize-autoloader --no-scripts --quiet
    php artisan package:discover --ansi 2>/dev/null || true
fi

# Los pasos 2-5 (assets, cache warming, storage:link, permisos) solo
# aplican al contenedor que sirve HTTP (php-fpm). El contenedor
# historias_scheduler (Etapa 6.3.2) comparte este mismo volumen e imagen
# pero solo necesita vendor/ para poder correr `php artisan schedule:run`
# — repetir cache:cache/route:cache/view:cache en cada restart del
# scheduler no aporta nada y compite de forma innecesaria con historias_app
# por los mismos archivos de bootstrap/cache.
if [ "$1" = "php-fpm" ]; then
    # 2. Compilar assets si no están
    if [ ! -d "/var/www/html/public/build" ]; then
        echo "[Sistema de Salud] Compilando assets..."
        npm install --silent 2>/dev/null
        npm run build --silent 2>/dev/null
    fi

    # 3. Optimizar
    echo "[Sistema de Salud] Optimizando..."
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache  2>/dev/null || true
    php artisan view:cache   2>/dev/null || true

    # 4. Storage link
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "[Sistema de Salud] Creando storage:link..."
        php artisan storage:link 2>/dev/null || true
    fi

    # 5. Permisos para archivos ya existentes en storage
    echo "[Sistema de Salud] Ajustando permisos de storage..."
    find /var/www/html/storage/app/public -type f -exec chmod 644 {} \; 2>/dev/null || true
    find /var/www/html/storage/app/public -type d -exec chmod 755 {} \; 2>/dev/null || true
fi

echo "[Sistema de Salud] Listo. Iniciando: $@"
exec "$@"