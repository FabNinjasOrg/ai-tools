# Use PHP 8.2 FPM image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    curl \
    git \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    cron \
    supervisor \
    busybox \
    wget \
    gnupg \
    libmagickwand-dev \
    imagemagick \
    ghostscript \
    nodejs \
    npm \
    poppler-utils \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        bcmath \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

# Match www-data to host user
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy everything
COPY . .

# Set Laravel writable directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup cron job
RUN echo "* * * * * cd /var/www && busybox sh -c '/usr/local/bin/php artisan schedule:run >> /var/www/storage/logs/cron.log 2>&1'" | crontab -

# Expose PHP-FPM port (matches Nginx fastcgi_pass)
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-n"]
