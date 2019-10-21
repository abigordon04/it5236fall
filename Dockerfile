FROM php:7.2-apache

RUN docker-ext-php-install pdo pdo_mysql

COPY webapp/ /var/www/html/
COPY api/ /var/www/html/api/
COPY credentails.php /var/www/html
