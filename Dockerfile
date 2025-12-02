FROM php:8.4-apache

WORKDIR /var/www/html

# Enable Apache modules
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libzip-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Copy Apache config
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Copy application FIRST
COPY . .

#THEN set ownership to www-data (Apache user)
RUN chown -R www-data:www-data /var/www/html

#Create directories that might be missing (like in .gitignore)
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

#Install dependencies as www-data
USER www-data
RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 80

CMD ["apache2-foreground"]