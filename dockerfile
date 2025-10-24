# Use official PHP 8.2 + Apache image
FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update package lists and install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libsqlite3-dev \
    unzip \
    git && \
    rm -rf /var/lib/apt/lists/*

# Configure and install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install other PHP extensions
RUN docker-php-ext-install mbstring zip pdo pdo_sqlite

# Create session directory early
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod -R 700 /var/lib/php/sessions

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# ------------------------------
# 📦 Application setup
# ------------------------------

# Set working directory for the backend (non-public) files
WORKDIR /var/www/

# Copy composer files for dependency caching
COPY composer.json composer.lock /var/www/

# Install dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

RUN composer dump-autoload --optimize

# Copy the rest of the project EXCEPT public directory
COPY . /var/www/
RUN rm -rf /var/www/public

# Copy only the public directory into Apache’s web root
COPY public/ /var/www/html/

# ------------------------------
# ⚙️ PHP configuration
# ------------------------------
RUN echo "display_errors = On" > /usr/local/etc/php/conf.d/custom.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
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

# Run post-install composer scripts if defined
RUN composer run-script --no-dev post-install-cmd 2>/dev/null || true

# ------------------------------
# 🌐 Apache configuration
# ------------------------------
# Ensure Apache DocumentRoot points to /var/www/html
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides in the public directory
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/sites-available/000-default.conf

# ------------------------------
# 🔒 Permissions & storage
# ------------------------------
RUN mkdir -p /var/www/database /var/www/storage /var/www/tmp && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
