FROM php:7.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN apt-get update && apt-get install -y nano

COPY webapp/ /var/www/html/
COPY api/ /var/www/html/api/
COPY aws-webapp/ /var/www/html/aws/
COPY ddb/ /var/www/html/ddb/
COPY vendor/ /var/www/html/ddb/vendor/
COPY credentials.php /var/www/html/api
COPY phpMyAdmin /var/www/html/phpMyAdmin/
