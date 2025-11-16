FROM php:8.2-apache

# Install PostgreSQL drivers
RUN apt-get update && apt-get install -y \
    libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

WORKDIR /var/www/html/
