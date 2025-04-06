# Use an official PHP image as the base image
FROM php:8.1-fpm

# Install system dependencies and PHP extensions required for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Set the working directory to /var/www
WORKDIR /var/www

# Copy the composer.lock and composer.json files
COPY composer.json composer.lock ./

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application files
COPY . .

# Set the correct permissions for Laravel's storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Run Laravel's artisan commands (like key:generate, migrate, etc.)
RUN php artisan key:generate

# Expose port 80 to allow external access to the application
EXPOSE 80

# Start PHP-FPM server
CMD ["php-fpm"]
