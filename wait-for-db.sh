#!/usr/bin/env bash
# Simple DB wait script for WordPress entrypoint
# Usage: WAIT_FOR_DB=true DB_WAIT_HOST=db-stack-production DB_WAIT_PORT=3306 ./wait-for-db.sh

set -euo pipefail

HOST=${DB_WAIT_HOST:-localhost}
PORT=${DB_WAIT_PORT:-3306}
TIMEOUT=${DB_WAIT_TIMEOUT:-120}
SLEEP_INTERVAL=2

start_ts=$(date +%s)

printf "[wait-for-db] Waiting for database at %s:%s (timeout %ss)\n" "$HOST" "$PORT" "$TIMEOUT"

while true; do
  # Try to open a TCP socket
  if nc -z "$HOST" "$PORT" >/dev/null 2>&1; then
    printf "[wait-for-db] DB reachable at %s:%s\n" "$HOST" "$PORT"
    break
  fi

  now_ts=$(date +%s)
  elapsed=$((now_ts - start_ts))
  if (( elapsed >= TIMEOUT )); then
    echo "[wait-for-db] ERROR: timed out waiting for DB after ${TIMEOUT}s"
    exit 1
  fi

  sleep $SLEEP_INTERVAL
done
