FROM php:8.2-apache

# Installeer de vereiste extensies
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Kopieer alle projectbestanden
COPY . /var/www/html/

EXPOSE 80
