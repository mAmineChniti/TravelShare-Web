# Multi-stage Dockerfile for Symfony

# Stage 1: Build
FROM php:8.1-fpm-alpine AS build

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 var/ public/

# Stage 2: Production
FROM php:8.1-fpm-alpine AS production

# Copy built application
COPY --from=build /app /app

# Set working directory
WORKDIR /app

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]