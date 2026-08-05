#!/bin/sh

# Asegurar que archivos creados por PHP sean legibles por nginx
umask 0022

echo "[Inmobiliarias] Iniciando setup..."

# 1. Instalar dependencias PHP si no están
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "[Inmobiliarias] Instalando dependencias Composer..."
    composer install --no-interaction --no-dev --optimize-autoloader --no-scripts --quiet
    php artisan package:discover --ansi 2>/dev/null || true
fi

# Los pasos 2-5 (assets, cache warming, storage:link, permisos) solo
# aplican al contenedor que sirve HTTP (php-fpm). Los contenedores
# inmobiliarias_horizon e inmobiliarias_scheduler comparten este mismo
# volumen e imagen pero solo necesitan vendor/ para correr `php artisan
# horizon` / `schedule:run` — repetir cache:cache/route:cache/view:cache
# en cada restart compite de forma innecesaria con inmobiliarias_app por
# los mismos archivos de bootstrap/cache.
if [ "$1" = "php-fpm" ]; then
    # 2. Compilar assets si no están
    if [ ! -d "/var/www/html/public/build" ]; then
        echo "[Inmobiliarias] Compilando assets..."
        npm install --silent 2>/dev/null
        npm run build --silent 2>/dev/null
    fi

    # 3. Optimizar
    echo "[Inmobiliarias] Optimizando..."
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache  2>/dev/null || true
    php artisan view:cache   2>/dev/null || true

    # 4. Storage link
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "[Inmobiliarias] Creando storage:link..."
        php artisan storage:link 2>/dev/null || true
    fi

    # 5. Permisos para archivos ya existentes en storage
    echo "[Inmobiliarias] Ajustando permisos de storage..."
    find /var/www/html/storage/app/public -type f -exec chmod 644 {} \; 2>/dev/null || true
    find /var/www/html/storage/app/public -type d -exec chmod 755 {} \; 2>/dev/null || true
fi

echo "[Inmobiliarias] Listo. Iniciando: $@"
exec "$@"
