# Use official PHP 8.4 + Apache image
FROM php:8.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update package lists
RUN apt-get update

# Install system dependencies
RUN apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libsqlite3-dev \
    unzip \
    git

# Configure and install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Install other PHP extensions
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_sqlite

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copy the rest of the project files
COPY . .

# Run post-install scripts if any
RUN composer run-script --no-dev post-install-cmd 2>/dev/null || true

# Set Apache DocumentRoot to public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/default-ssl.conf 2>/dev/null || true

# Configure Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/sites-available/000-default.conf

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create SQLite database directory if it doesn't exist
RUN mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]