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

# Create session directory early (before copying files)
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod -R 700 /var/lib/php/sessions

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

# Create custom PHP configuration directly
RUN echo "display_errors = Off" > /usr/local/etc/php/conf.d/custom.ini && \
    echo "display_startup_errors = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_log = /var/log/apache2/php_errors.log" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "allow_url_fopen = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "allow_url_include = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.save_path = /var/lib/php/sessions" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.cookie_httponly = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.cookie_secure = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.use_strict_mode = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.gc_maxlifetime = 1440" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.cookie_lifetime = 0" >> /usr/local/etc/php/conf.d/custom.ini

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

# Create a writable directory for any temporary files
RUN mkdir -p /var/www/html/storage /var/www/html/tmp \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/tmp \
    && chmod -R 775 /var/www/html/storage /var/www/html/tmp

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]