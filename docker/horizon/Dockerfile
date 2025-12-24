# ===========================================
# Laravel Horizon Queue Worker
# ===========================================
ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-cli-alpine

LABEL maintainer="Panel Empresarial"
LABEL description="Laravel Horizon Queue Worker"

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpq-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        intl \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        xml

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Set working directory
WORKDIR /var/www/html

# Health check
HEALTHCHECK --interval=60s --timeout=10s --start-period=30s --retries=3 \
    CMD php artisan horizon:status || exit 1

# Run Horizon
CMD ["php", "artisan", "horizon"]
