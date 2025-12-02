FROM php:8.3-apache

WORKDIR /var/www/html

# Enable Apache modules
RUN a2enmod rewrite

# Install system dependencies including extension dependencies
RUN apt-get update && apt-get install -y \
    git curl libzip-dev zip unzip

# Install PHP extensions with proper flags
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Redis extension (phpredis)
RUN pecl install redis && docker-php-ext-enable redis

# Copy Apache config from docker folder
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Set proper permissions BEFORE composer install
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Switch to www-data user for composer install
USER www-data

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Switch back to root for Apache
USER root

# Ensure Apache can write to storage
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
