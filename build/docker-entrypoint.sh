#!/bin/sh
set -e

# Set PHP timezone from environment variable (default: Asia/Jakarta)
PHP_TIMEZONE=${PHP_TIMEZONE:-Asia/Jakarta}

# Write timezone to PHP configuration
# The timezone.ini file is pre-created in Dockerfile with www-data ownership
# so it can be modified at runtime when running as non-root user
if [ -w /usr/local/etc/php/conf.d/timezone.ini ]; then
    echo "date.timezone=${PHP_TIMEZONE}" > /usr/local/etc/php/conf.d/timezone.ini
else
    # Fallback: if we can't write, log a warning but continue
    echo "Warning: Cannot write to timezone.ini (running as non-root). Using default or existing value." >&2
fi

# Set system timezone (optional, for system-level timezone)
# Only set if tzdata is installed and timezone file exists
# Note: These operations require root, so we skip them when running as non-root
if [ "$(id -u)" = "0" ] && [ -d /usr/share/zoneinfo ] && [ -f /usr/share/zoneinfo/${PHP_TIMEZONE} ]; then
    cp /usr/share/zoneinfo/${PHP_TIMEZONE} /etc/localtime 2>/dev/null || true
    echo ${PHP_TIMEZONE} > /etc/timezone 2>/dev/null || true
fi

# Execute the main command (php-fpm)
exec "$@"

