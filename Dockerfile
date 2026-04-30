# Step 1: Composer dependencies

FROM composer:2.6 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --prefer-dist \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

# Step 2: Node build (Vite)

FROM node:20-slim AS node_builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# Step 3: PHP-FPM

FROM php:8.2-fpm

WORKDIR /var/www/html

# Install required system packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libzip-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libmagickwand-dev \
    libheif-dev \
    imagemagick \
    ghostscript \
    cron \
    busybox \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql zip intl mbstring bcmath gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    # && curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "/awscliv2.zip" \
    # && unzip /awscliv2.zip \
    # && ./aws/install \
    # && rm -rf /aws /awscliv2.zip \
    && rm -rf /var/lib/apt/lists/*

# Copy application code
COPY . .

# Copy vendor from step 1
COPY --from=vendor /app/vendor ./vendor

# Copy built frontend assets
COPY --from=node_builder /app/public ./public

# Add custom php.ini settings
COPY docker/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Create necessary Laravel directories permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Entrypoint script execution
# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php-fpm"]
