FROM php:8.2-apache

# Install dependencies and extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql mysqli gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache rewrite + headers modules
RUN a2enmod rewrite headers

# Update Apache config to allow .htaccess overrides
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Create runtime directories that legacy app expects but are git-ignored.
# Without these, fopen("back/dat/access/YYYYMM.cgi") fails silently and the
# legacy front controller exits with empty body (404 in user perception).
RUN mkdir -p \
        /var/www/html/back/dat/access \
        /var/www/html/back/tpl/templates_c \
        /var/www/html/back/tpl/cache \
        /var/www/html/back/tpl/configs \
        /var/www/html/storage \
    && chown -R www-data:www-data \
        /var/www/html/back/dat/access \
        /var/www/html/back/tpl/templates_c \
        /var/www/html/back/tpl/cache \
        /var/www/html/back/tpl/configs \
        /var/www/html/storage \
    && chmod -R 0775 \
        /var/www/html/back/dat/access \
        /var/www/html/back/tpl/templates_c \
        /var/www/html/back/tpl/cache \
        /var/www/html/back/tpl/configs \
        /var/www/html/storage
