#!/bin/bash
set -e

if [ -f /var/www/html/migrations/xvilo.sql ]; then
    mysql -h"${DB_HOST:-62.84.180.151}" -P"${DB_PORT:-3306}" -u"${DB_USER:-pterodactyl}" -p"${DB_PASS:-P@ssw0rd2024!}" -D"${DB_NAME:-s82939_Lost100}" < /var/www/html/migrations/xvilo.sql 2>/dev/null || true
fi

if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

exec "$@"
