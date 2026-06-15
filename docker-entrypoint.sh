#!/bin/bash
set -e

if [ -f /var/www/html/migrations/migrate.php ]; then
    php /var/www/html/migrations/migrate.php 2>/dev/null || true
fi

if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

exec "$@"
