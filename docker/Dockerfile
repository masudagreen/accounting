# syntax=docker/dockerfile:1.7
# ==================== BUILDER STAGE ====================
# Build PHP extensions in an isolated layer so the final image stays lean.
FROM php:8.3-apache AS builder

WORKDIR /build

# Build-time libraries required to compile the PHP extensions below.
# Debian Bookworm package names (verified against packages.debian.org).
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        libzip-dev \
        libcurl4-openssl-dev \
        zlib1g-dev \
        zip \
        unzip \
        git \
        ca-certificates \
    ; \
    rm -rf /var/lib/apt/lists/*

# Install the PHP extensions we rely on across the application and tests.
RUN docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        gd \
        intl \
        zip \
        bcmath \
        opcache

# Composer 2.x from the official image. Pinned to ":2" for determinism.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies. We copy only the files composer needs to resolve
# the graph and generate the optimized autoloader — the rest of the source is
# bind-mounted at runtime. Running as root inside the builder is fine.
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_HOME=/tmp/composer

WORKDIR /var/www/html
COPY composer.json composer.lock ./
# PSR-4 / classmap targets referenced by composer.json — needed only so the
# autoloader dump sees the right directory layout.
COPY src/ ./src/
COPY back/class/else/ ./back/class/else/

RUN --mount=type=cache,target=/tmp/composer/cache \
    composer install \
        --no-progress \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader

# ==================== FINAL STAGE ====================
FROM php:8.3-apache

WORKDIR /var/www/html

# Runtime shared libraries matching what the builder linked against.
# Debian Trixie ベース: libicu76 / libzip5 （旧 Bookworm の libicu72/libzip4 は不在）
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpng16-16 \
        libonig5 \
        libxml2 \
        libicu76 \
        libzip5 \
        curl \
        ca-certificates \
        tzdata \
    ; \
    rm -rf /var/lib/apt/lists/*

# Copy compiled extensions and the matching config stubs from the builder.
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Pull the installed vendor/ tree from the builder. The application source is
# provided by the bind mount at runtime; docker-compose keeps this directory
# from being shadowed via a dedicated named volume (app_vendor).
COPY --from=builder --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_HOME=/tmp/composer

# Apply the project-wide php.ini overrides and Apache vhost.
COPY docker/php/zz-rucaro.ini /usr/local/etc/php/conf.d/zz-rucaro.ini
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Runtime directories required by the new layout (ADR-001) and the legacy
# Smarty compile/cache paths that back/** still expects.
RUN set -eux; \
    mkdir -p \
        /var/www/html/public \
        /var/www/html/storage/logs \
        /var/www/html/storage/cache \
        /var/www/html/storage/receipts \
        /var/www/html/storage/uploads \
        /var/www/html/back/tpl/templates_c \
        /var/www/html/back/tpl/cache \
    ; \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/back/tpl

RUN a2enmod rewrite headers expires

ENV TZ=Asia/Tokyo

HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD curl -fsS http://localhost/ >/dev/null || exit 1

CMD ["apache2-foreground"]
