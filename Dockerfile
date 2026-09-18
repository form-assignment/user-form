FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
