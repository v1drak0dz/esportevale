FROM php:7.3-apache

# Ativa mod_rewrite do Apache
RUN a2enmod rewrite

# Permite que .htaccess funcione com RewriteRule
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    unzip \
    zip \
    curl \
    && docker-php-ext-install mysqli curl mbstring

# Define diretório de trabalho
WORKDIR /var/www/html
