FROM php:8.3.4-fpm-alpine
## install php-cli
RUN apk update && apk upgrade
RUN apk add --no-cache bash curl gd libpng libpng-dev libwebp libwebp-dev freetype libjpeg-turbo freetype-dev libjpeg-turbo-dev zlib-dev \
&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
&& docker-php-ext-install gd 
RUN apk add --no-cache postgresql-dev && docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pdo_mysql
RUN apk add --no-cache libzip-dev  && docker-php-ext-install zip
RUN apk add autoconf build-base libmcrypt-dev && curl -fsSLOJ https://pecl.php.net/get/mcrypt/1.0.7 \
 && tar -xf mcrypt-1.0.7.tgz
RUN cd mcrypt-1.0.7 \
 && phpize \
 && ./configure \
 && make \
 && make test \
 && cp modules/*.so $(pecl config-get ext_dir) \
 && cd .. \
 && rm -rf mcrypt-1.0.7.tgz mcrypt-1.0.7
RUN echo extension="mcrypt.so" > /usr/local/etc/php/conf.d/php-ext-mcrypt.ini
RUN pecl install redis && docker-php-ext-enable redis
RUN apk del autoconf build-base
RUN echo "date.timezone=Asia/Jakarta" > /usr/local/etc/php/conf.d/timezone.ini \
&& echo "mbstring.func_overload = 0" > /usr/local/etc/php/conf.d/mbstring.ini \
&& echo "post_max_size = 20M" > /usr/local/etc/php/conf.d/post.ini \
&& echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/upload.ini \
&& echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/upload.ini \
&& echo "memory_limit=1024M" > /usr/local/etc/php/conf.d/memory.ini
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Install PHPUnit via Composer (preferred) or fallback to PHAR
# Composer will install PHPUnit to vendor/bin/phpunit
# We also create a symlink for convenience
RUN if [ -f composer.json ]; then \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts --dev && \
    ln -sf /var/www/html/vendor/bin/phpunit /usr/local/bin/phpunit || \
    (wget https://phar.phpunit.de/phpunit-9.6.phar -O /usr/local/bin/phpunit && chmod +x /usr/local/bin/phpunit); \
    else \
    wget https://phar.phpunit.de/phpunit-9.6.phar -O /usr/local/bin/phpunit && chmod +x /usr/local/bin/phpunit; \
    fi