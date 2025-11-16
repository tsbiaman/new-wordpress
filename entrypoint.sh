#!/usr/bin/env bash
set -euo pipefail

# Default: wait for DB unless explicitly disabled
if [[ "${WAIT_FOR_DB:-true}" == "true" ]]; then
  /usr/local/bin/wait-for-db
fi

exec docker-entrypoint.sh "$@"
