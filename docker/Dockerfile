########## STAGE 1: PHP Dependencies ##########
FROM composer AS vendor
WORKDIR /app

# copy from the root's code perspective
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-scripts

########### STAGE 2: Final Production Image ###########
# STAGE 2: Final Production Image
FROM php:8.4-fpm-alpine
WORKDIR /var/www/html

# 1. Define arguments for local user (defaults to empty for prod)
ARG USER_ID
ARG GROUP_ID

# 2. Install system tools (allow us to modify existing users, fcgi for healthcheck)
RUN apk add --no-cache shadow fcgi

# 3. If USER_ID is passed, change www-data's ID to match yours
# This ensures permissions align with your local host machine
RUN if [ ! -z "$USER_ID" ]; then \
        usermod -u $USER_ID www-data && \
        groupmod -g $GROUP_ID www-data; \
    fi

# 4. Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql bcmath gd zip intl mbstring redis

# 5. Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY docker/php/fpm.conf /usr/local/etc/php-fpm.d/www.conf

# 6. Copy Code with Permissions (The "One-Shot" method)
# This handles the owner for EVERYTHING copied in
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor

# 6. Setup Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

#EXPOSE 9000
USER www-data
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

############################################################
# FROM php:8.4-apache

# WORKDIR /var/www/html

# # Enable Apache modules
# RUN a2enmod rewrite

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git curl libzip-dev zip unzip

# # Install PHP extensions
# RUN docker-php-ext-install pdo pdo_mysql zip

# # Install Redis extension
# RUN pecl install redis && docker-php-ext-enable redis

# # Copy Apache config
# COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# #Copy application FIRST
# COPY . .

# #THEN set ownership to www-data (Apache user)
# RUN chown -R www-data:www-data /var/www/html

# #Create directories that might be missing (like in .gitignore)
# RUN mkdir -p storage/framework/{sessions,views,cache} \
#     && mkdir -p bootstrap/cache \
#     && chmod -R 775 storage bootstrap/cache

# #Install dependencies as www-data
# USER www-data
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# EXPOSE 80

# CMD ["apache2-foreground"]