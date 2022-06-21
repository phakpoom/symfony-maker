FROM php:8.1-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git build-essential autoconf ruby libicu-dev libzip-dev zip libonig-dev libxml2-dev libpng-dev libmagickwand-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml

ENV TZ=Asia/Bangkok

WORKDIR /var/www/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
RUN composer install
CMD sleep 100000000
