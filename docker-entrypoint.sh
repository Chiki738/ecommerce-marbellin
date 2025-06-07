#!/bin/bash
set -e

cd /var/www/html

echo "Esperando a que la base de datos esté disponible..."

# Esperar a que la base de datos esté lista
until php artisan migrate --force; do
  echo "Base de datos no disponible, reintentando en 5 segundos..."
  sleep 5
done

echo "Base de datos lista. Generando caches y clave de app..."

# Limpiar y generar caches
php artisan config:clear
php artisan config:cache
php artisan route:cache

# Asegurar que exista la carpeta de vistas compiladas
if [ ! -d "storage/framework/views" ]; then
  mkdir -p storage/framework/views
  echo "Carpeta storage/framework/views creada."
fi

# Intentar cachear vistas, continuar aunque falle
if [ -d "resources/views" ]; then
  php artisan view:cache || echo "No se pudo cachear vistas, pero se continúa..."
else
  echo "No se encontró la carpeta resources/views, omitiendo cache de vistas."
fi

# Generar clave si no existe
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Inicializando Apache..."

exec apache2-foreground
