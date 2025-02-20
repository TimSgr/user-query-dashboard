FROM php:7.4-fpm

WORKDIR /var/www/html

COPY . /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql

CMD ["php-fpm"]