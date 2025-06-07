#!/bin/bash

# Esperar la BD
until php artisan migrate --force; do
  echo "Esperando a que la base de datos esté disponible..."
  sleep 5
done

# Preparar Laravel
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan key:generate --force

exec apache2-foreground
