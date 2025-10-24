# Use official PHP+Apache image for PHP 8.4
FROM php:8.4-apache

# Enable Apache mod_rewrite (if needed)  
RUN a2enmod rewrite

# Install required PHP extensions for SQLite and mPDF  
RUN docker-php-ext-install pdo pdo_sqlite \
    && docker-php-ext-install mbstring xml gd bcmath zip \
    # clean up apt cache (for Debian-based image)  
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Optional: set working directory to Apache web root  
WORKDIR /var/www/html

# Copy your application into the container  
COPY . /var/www/html/

# (If you use Composer) install mPDF version 8.2.5  
# If you already have vendor/ in your project you could skip INSTALLNING here  
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer require mpdf/mpdf:8.2.5 --no-interaction --prefer-dist --optimize-autoloader

# Ensure correct permissions (Apache user)  
RUN chown -R www-data:www-data /var/www/html

# Expose port 80  
EXPOSE 80

# Use default Apache entrypoint (shared with base image)
