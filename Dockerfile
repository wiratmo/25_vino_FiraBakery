FROM php:8.0-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    curl \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        mysqli \
        mbstring \
        gd \
        zip

# Set working directory
WORKDIR /app

# Copy project
COPY . .

# Set permissions (important for CI3)
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose port
EXPOSE 80

# Start nginx + php-fpm
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
