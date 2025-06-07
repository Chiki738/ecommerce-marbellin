FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd xml

RUN a2enmod rewrite

COPY . /var/www/html

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN composer install --no-dev --optimize-autoloader --verbose

# Asegura que bootstrap/cache exista antes de cambiar permisos
RUN mkdir -p /var/www/html/bootstrap/cache

# Permisos para que Laravel pueda escribir en storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Opcional: permisos para toda la app, si es necesario
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html/public

EXPOSE 80

CMD ["apache2-foreground"]
