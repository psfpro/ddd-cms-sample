FROM php:7.4-fpm-alpine

WORKDIR /var/www

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk --update add gcc make g++ zlib-dev

RUN apk --no-cache add \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    icu-dev \
    zip \
    libzip-dev \
    autoconf \
    curl \
    supervisor \
    openssl \
    openrc

RUN docker-php-ext-install \
    pdo_mysql \
    gd \
    zip \
    exif \
    intl

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/php.ini \
#    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/php.ini

RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer
