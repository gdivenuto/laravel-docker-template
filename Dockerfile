FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    zip \
    intl \
    mbstring \
    bcmath \
    xml

RUN a2enmod rewrite

COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["entrypoint.sh"]