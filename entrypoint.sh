#!/usr/bin/env bash
set -euo pipefail

# Optionally wait for DB if WAIT_FOR_DB=true
if [[ "${WAIT_FOR_DB:-true}" == "true" ]]; then
  /usr/local/bin/wait-for-db
fi

exec docker-entrypoint.sh "$@"
