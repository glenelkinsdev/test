FROM php:7.4-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && a2enmod rewrite headers

COPY ./rick-and-morty /var/www/html

WORKDIR /var/www/html

RUN composer install

# in case these are missing
RUN mkdir -p /var/www/html/var/cache

RUN chmod -R 777 /var/www/html/var/cache

RUN mkdir -p /var/www/html/var/log

RUN chmod -R 777 /var/www/html/var/log
