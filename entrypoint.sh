#!/usr/bin/env bash
set -euo pipefail

# Optionally wait for DB if WAIT_FOR_DB=true
if [[ "${WAIT_FOR_DB:-true}" == "true" ]]; then
  /usr/local/bin/wait-for-db
fi

# Ensure uploads directory is writable
chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null || true

exec docker-entrypoint.sh "$@"
