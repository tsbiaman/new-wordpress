#!/usr/bin/env bash
set -euo pipefail

HOST=${DB_WAIT_HOST:-${DB_HOST:-db}}
PORT=${DB_WAIT_PORT:-${DB_PORT:-3306}}
TIMEOUT=${DB_WAIT_TIMEOUT:-120}
SLEEP=2

echo "[wait-for-db] Waiting for ${HOST}:${PORT} (timeout ${TIMEOUT}s)"
start=$(date +%s)
while true; do
  if nc -z "$HOST" "$PORT" >/dev/null 2>&1; then
    echo "[wait-for-db] Database is reachable"
    break
  fi
  now=$(date +%s)
  if (( now - start >= TIMEOUT )); then
    echo "[wait-for-db] ERROR: Timed out waiting for DB after ${TIMEOUT}s"
    exit 1
  fi
  sleep "$SLEEP"
done
