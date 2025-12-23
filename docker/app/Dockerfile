# ===========================================
# Laravel Application Container
# PHP 8.4 + Required Extensions
# ===========================================
ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm-alpine

LABEL maintainer="Panel Empresarial"
LABEL description="Laravel Application with PHP-FPM"

# Arguments
ARG APP_ENV=local
ARG WWWGROUP=1000
ARG WWWUSER=1000

# Environment
ENV APP_ENV=${APP_ENV}
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp/composer

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpq-dev \
    libxml2-dev \
    supervisor \
    shadow

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

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create user
RUN addgroup -g ${WWWGROUP} www \
    && adduser -D -u ${WWWUSER} -G www www

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --chown=www:www . .

# Install dependencies based on environment
RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader; \
    else \
        composer install --no-interaction --prefer-dist; \
    fi

# Set permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy supervisor configuration
COPY docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP-FPM configuration
RUN sed -i 's/user = www-data/user = www/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = www/g' /usr/local/etc/php-fpm.d/www.conf

# Expose PHP-FPM port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

# Start PHP-FPM
CMD ["php-fpm"]
