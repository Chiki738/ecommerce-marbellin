#!/bin/bash
set -e

echo "Esperando a que la base de datos esté disponible..."

# Intentar conexión a la DB con php artisan migrate, si falla espera 5s y reintenta
until php artisan migrate --force; do
  echo "Base de datos no disponible, reintentando en 5 segundos..."
  sleep 5
done

echo "Base de datos lista. Generando caches y clave de app..."

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Solo genera la APP_KEY si no existe (por si ya tienes una)
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Inicializando Apache..."
exec apache2-foreground
