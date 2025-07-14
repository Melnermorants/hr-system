FROM php:8.2-apache
# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite
