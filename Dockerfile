# === PHP-FPM 8.3 (Alpine) con extensiones comunes ===
FROM php:8.3-fpm-alpine

ARG WWWUSER=1000
ARG WWWGROUP=1000

# Paquetes de runtime + headers para extensiones
RUN set -eux; \
    apk add --no-cache \
      git curl zip unzip bash shadow openssl \
      icu icu-dev libxml2-dev oniguruma-dev \
      libpng-dev freetype-dev libjpeg-turbo-dev \
      libzip-dev linux-headers \
      nodejs npm; \
    # Dependencias de compilación (phpize, autoconf, etc.)
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
    # Configurar e instalar extensiones nativas
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" \
      pdo pdo_mysql mbstring exif pcntl bcmath intl gd zip; \
    # PECL redis (requiere phpize/autoconf)
    pecl install redis; \
    docker-php-ext-enable redis; \
    # Limpiar deps de build para reducir tamaño
    apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Usuario no root (evitar problemas de permisos con el host)
RUN usermod -u ${WWWUSER} www-data && groupmod -g ${WWWGROUP} www-data

USER www-data
WORKDIR /var/www/html

# Instalar dependencias del proyecto si ya hay composer.json (no falla si no existe)
RUN if [ -f composer.json ]; then composer install --no-interaction; fi

