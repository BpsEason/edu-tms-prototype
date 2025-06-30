FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    mysql-client \
    nodejs \
    npm

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy application code
COPY . .

# Install dependencies and setup
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build
RUN php artisan key:generate
RUN php artisan migrate --seed --force

# Expose port
EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
