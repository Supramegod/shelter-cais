FROM php:8.2-apache

# Install dependency jika perlu
RUN apt-get update && apt-get install -y unzip zip git

# Aktifkan mod_rewrite (jika perlu)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy semua isi project ke dalam container
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install

# Set permission (jika dibutuhkan)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
