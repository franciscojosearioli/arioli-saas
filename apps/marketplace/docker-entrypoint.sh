#!/bin/sh

# Asegurar que archivos creados por PHP sean legibles por nginx
umask 0022

echo "[Marketplace] Iniciando setup..."

# 1. Instalar dependencias PHP si no están
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "[Marketplace] Instalando dependencias Composer..."
    composer install --no-interaction --no-dev --optimize-autoloader --no-scripts --quiet
    php artisan package:discover --ansi 2>/dev/null || true
fi

# 2. Compilar assets si no están
if [ ! -d "/var/www/html/public/build" ]; then
    echo "[Marketplace] Compilando assets..."
    npm install --silent 2>/dev/null
    npm run build --silent 2>/dev/null
fi

# 3. Crear la base si todavía no existe — a diferencia de las apps
# multi-tenant, acá no hay un provisioner que la cree por nosotros.
if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
    echo "[Marketplace] Verificando base de datos '$DB_DATABASE'..."
    mysql -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" \
        -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
        2>/dev/null || echo "[Marketplace] No se pudo verificar/crear la base (¿mysql no está listo todavía?)"
fi

# 4. Migraciones (base propia, no multi-tenant — ver §01/§08 del Artifact)
echo "[Marketplace] Migrando..."
php artisan migrate --force 2>/dev/null || true

# 5. Optimizar
echo "[Marketplace] Optimizando..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache  2>/dev/null || true
php artisan view:cache   2>/dev/null || true

# 6. Storage link
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "[Marketplace] Creando storage:link..."
    php artisan storage:link 2>/dev/null || true
fi

echo "[Marketplace] Listo. Iniciando PHP-FPM..."
exec php-fpm
