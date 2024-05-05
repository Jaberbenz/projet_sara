FROM php:7.4-apache

# Copiez les fichiers du site web et le fichier ZIP dans le conteneur
COPY . /var/www/html/

# Installez les extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql
