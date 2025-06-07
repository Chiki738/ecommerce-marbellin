FROM php:8.2-apache

# Instala dependencias y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd xml

# Habilita mod_rewrite para URLs amigables en Apache
RUN a2enmod rewrite

# Copia todo el proyecto al contenedor
COPY . /var/www/html

# Cambia DocumentRoot para que Apache sirva desde /public (carpeta de Laravel)
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Da permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Define el directorio de trabajo en /public
WORKDIR /var/www/html/public

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Instala dependencias PHP sin paquetes de desarrollo, optimizando autoload
RUN composer install --no-dev --optimize-autoloader || (cat /root/.composer/composer.log && false)

# Expone puerto 80 para la app web
EXPOSE 80

# Ejecuta Apache en primer plano (necesario para Docker)
CMD ["apache2-foreground"]
