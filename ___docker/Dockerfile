FROM php:8.3-fpm as api

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    nano \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libc6 \
    zip \
    unzip \
    supervisor \
    default-mysql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache sockets

RUN pecl install redis xdebug && docker-php-ext-enable xdebug

# Configure Xdebug with coverage mode
RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

COPY . /var/www

# Install dependencies based on the environment
RUN composer install

# Set file permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap

# allow run "php artisan tinker" inside the container without error
RUN mkdir -p /var/www/.config/psysh && \
        chown -R www-data:www-data /var/www/.config/psysh

EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
