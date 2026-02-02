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

# Enable Apache rewrite module for .htaccess support
RUN a2enmod rewrite

# Update Apache config to allow .htaccess overrides
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
