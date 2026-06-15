#!/bin/bash
set -e

# Run migration
if [ -f /var/www/html/migrations/xvilo.sql ]; then
    echo "Running migrations..."
    mysql -h"${DB_HOST:-62.84.180.151}" \
          -P"${DB_PORT:-3306}" \
          -u"${DB_USER:-pterodactyl}" \
          -p"${DB_PASS:-P@ssw0rd2024!}" \
          -D"${DB_NAME:-s82939_Lost100}" \
          < /var/www/html/migrations/xvilo.sql 2>/dev/null || true
    echo "Migration done."
fi

exec "$@"
