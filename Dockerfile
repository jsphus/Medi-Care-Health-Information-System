FROM php:8.2-apache

# Install PostgreSQL drivers
RUN apt-get update && apt-get install -y \
    libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Configure Apache to allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy project files to Apache root
COPY . /var/www/html/

WORKDIR /var/www/html/
