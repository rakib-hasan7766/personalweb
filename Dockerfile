FROM php:8.2-apache
RUN docker-php-ext-install pdo
COPY . /var/www/html/
