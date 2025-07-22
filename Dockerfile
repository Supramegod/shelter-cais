# Menggunakan image PHP 8.1 sebagai base image
#FROM php:8.1-apache
FROM php:8.2-apache

# Install dependensi dan ekstensi PHP yang dibutuhkan untuk CodeIgniter
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libxml2-dev \
    libzip-dev

# Install ekstensi GD dan mysqli
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ docker-php-ext-install gd mysqli
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip xml

RUN docker-php-ext-enable intl mbstring

# Aktifkan mod_rewrite untuk Apache
RUN a2enmod rewrite

# Copy kode CodeIgniter ke dalam container
COPY . /var/www/html/

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
#RUN composer install

# Jalankan composer update di sini
#RUN composer update

RUN composer install --optimize-autoloader --no-dev

RUN mkdir /var/www/html/public

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

#RUN git config --global --add safe.directory /home/data/project/cais

# Set environment variables if needed
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Set direktori kerja
WORKDIR /var/www/html/

RUN composer install --optimize-autoloader --no-dev

# Expose port 80
EXPOSE 80

# Jalankan Apache server
CMD ["apache2-foreground"]
