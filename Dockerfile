FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd xml

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el proyecto completo
COPY . /var/www/html

# Establecer el DocumentRoot a /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Instalar dependencias de Laravel sin entorno de desarrollo
RUN composer install --no-dev --optimize-autoloader --verbose

# Crear carpetas necesarias para Laravel
RUN mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Permisos adecuados
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copiar el script de arranque
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

# Iniciar el script personalizado
CMD ["docker-entrypoint.sh"]
