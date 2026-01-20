#!/bin/sh
set -e

# Set PHP timezone from environment variable (default: Asia/Jakarta)
PHP_TIMEZONE=${PHP_TIMEZONE:-Asia/Jakarta}

# Write timezone to PHP configuration
echo "date.timezone=${PHP_TIMEZONE}" > /usr/local/etc/php/conf.d/timezone.ini

# Set system timezone (optional, for system-level timezone)
# Only set if tzdata is installed and timezone file exists
if [ -d /usr/share/zoneinfo ] && [ -f /usr/share/zoneinfo/${PHP_TIMEZONE} ]; then
    cp /usr/share/zoneinfo/${PHP_TIMEZONE} /etc/localtime 2>/dev/null || true
    echo ${PHP_TIMEZONE} > /etc/timezone 2>/dev/null || true
fi

# Execute the main command (php-fpm)
exec "$@"

