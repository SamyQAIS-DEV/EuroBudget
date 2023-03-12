FROM php:8.2-apache AS app_php
RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libcurl4-openssl-dev \
        libpng-dev \
        libzip-dev \
        zip \
        zlib1g-dev \
        libicu-dev \
        g++ \
        git \
        libssh2-1-dev libssh2-1 \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) iconv pdo pdo_mysql intl zip

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install calendar \
    && docker-php-ext-configure calendar

#RUN pecl install redis \
#    && docker-php-ext-enable redis

#RUN pecl install ssh2-1.3.1 \
#    && docker-php-ext-enable ssh2

#RUN #pecl install xdebug \
#    && docker-php-ext-enable xdebug

ENV PHP_IDE_CONFIG "serverName=dockerphp"

# Install composer
COPY --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer
RUN mkdir /var/composer
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN pecl install igbinary \
    && docker-php-ext-enable igbinary
