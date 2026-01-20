# Multi-stage build for better security and smaller image size
FROM php:8.3.4-fpm-alpine3.18 AS builder

# Install build dependencies
RUN apk add --no-cache --virtual .build-deps \
    autoconf build-base libmcrypt-dev curl

# Build Mcrypt extension (legacy support for Yii Framework 1.x)
RUN curl -fsSLOJ https://pecl.php.net/get/mcrypt/1.0.7 \
    && tar -xf mcrypt-1.0.7.tgz \
    && cd mcrypt-1.0.7 \
    && phpize \
    && ./configure \
    && make \
    && make test \
    && cp modules/*.so $(pecl config-get ext_dir) \
    && cd .. \
    && rm -rf mcrypt-1.0.7.tgz mcrypt-1.0.7

# Runtime stage - clean image with only necessary components
FROM php:8.3.4-fpm-alpine3.18 AS runtime

# Update system and install runtime dependencies in single layer
RUN apk update && apk upgrade && \
    apk add --no-cache \
        curl gd libpng libpng-dev freetype libjpeg-turbo \
        freetype-dev libjpeg-turbo-dev zlib-dev \
        postgresql-dev libzip-dev \
        libmcrypt \
        tzdata \
        autoconf build-base

# Install PHP extensions in optimized order
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo_pgsql pdo_mysql zip

# Install Redis extension via PECL
RUN pecl install redis && \
    docker-php-ext-enable redis && \
    apk del autoconf build-base

# Copy Mcrypt extension from builder stage
# First, copy to a temporary location, then move to the correct PHP extension directory
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-*/mcrypt.so /tmp/mcrypt.so
RUN EXT_DIR=$(php-config --extension-dir) && \
    cp /tmp/mcrypt.so ${EXT_DIR}/mcrypt.so && \
    rm /tmp/mcrypt.so && \
    echo "extension=mcrypt.so" > /usr/local/etc/php/conf.d/php-ext-mcrypt.ini

# Copy PHP configuration
COPY conf/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint script
COPY build/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set default timezone environment variable
ENV PHP_TIMEZONE=Asia/Jakarta

# Copy application files
COPY . /var/www/html

# Setup application structure and permissions
RUN mv /var/www/html/framework /var/www/framework && \
    mv /var/www/html/build/index.php /var/www/html/index.php && \
    rm -rf /var/www/html/.git /var/www/html/Dockerfile /var/www/html/docker-compose.yml /var/www/html/build && \
    mkdir -p /var/www/html/assets /var/www/html/protected/runtime && \
    chown -R www-data:www-data /var/www/html /var/www/framework

# Set entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Health check for container monitoring
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Expose PHP-FPM port
EXPOSE 9000

# Default command
CMD ["php-fpm"]