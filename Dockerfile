FROM php:8.2-apache

# Instala dependencias
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Habilita mod_rewrite para Laravel
RUN a2enmod rewrite

# Copia los archivos al contenedor
COPY . /var/www/html

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Directorio de trabajo
WORKDIR /var/www/html

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Instala dependencias del proyecto
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80
EXPOSE 80
