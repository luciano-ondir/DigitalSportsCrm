FROM composer:2 AS composer_bin
FROM node:22-bookworm AS node_bin

FROM php:8.3-apache-bookworm

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        ca-certificates \
        chromium \
        curl \
        default-mysql-client \
        fontconfig \
        fonts-dejavu-core \
        fonts-liberation \
        fonts-noto \
        fonts-noto-color-emoji \
        git \
        gnupg \
        gosu \
        imagemagick \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmagickwand-dev \
        libonig-dev \
        libpng-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && a2enmod rewrite headers expires ssl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer_bin /usr/bin/composer /usr/local/bin/composer
COPY --from=node_bin /usr/local/bin/node /usr/local/bin/node
COPY --from=node_bin /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version \
    && npm --version \
    && php --version \
    && composer --version \
    && chromium --version

COPY .docker/app/apache.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/app/php.ini /usr/local/etc/php/conf.d/99-cbes-dev.ini
COPY .docker/app/entrypoint.sh /usr/local/bin/cbes-entrypoint

RUN chmod +x /usr/local/bin/cbes-entrypoint \
    && mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html \
    && mkdir -p /home/www-data/.npm /tmp/composer-cache \
    && chown -R www-data:www-data /home/www-data /tmp/composer-cache

WORKDIR /var/www/html

ENTRYPOINT ["cbes-entrypoint"]
CMD ["apache2-foreground"]
