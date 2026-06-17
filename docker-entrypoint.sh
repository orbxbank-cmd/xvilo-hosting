#!/bin/bash
set -e

mkdir -p /var/www/html/public/uploads/proofs /var/www/html/public/uploads/avatars
chmod -R 777 /var/www/html/public/uploads

if [ -f /var/www/html/migrations/migrate.php ]; then
    php /var/www/html/migrations/migrate.php 2>/dev/null || true
fi

if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

exec "$@"
