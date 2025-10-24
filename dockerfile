# Use official PHP 8.4 + Apache image
FROM php:8.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring bcmath zip xml pdo pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory to project root
WORKDIR /var/www/html

# Copy project files into container
COPY . /var/www/html/

# Set Apache DocumentRoot to public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Install mPDF 8.2.5 (or other dependencies defined in composer.json)
RUN composer require mpdf/mpdf:8.2.5 --no-interaction --prefer-dist --optimize-autoloader

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Use default Apache entrypoint
CMD ["apache2-foreground"]