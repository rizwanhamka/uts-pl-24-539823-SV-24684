#!/bin/sh
set -e

echo "[entrypoint] Menjalankan migrasi database..."
php artisan migrate --force

if [ "${RUN_SEED:-false}" = "true" ]; then
  echo "[entrypoint] Menjalankan seeder..."
  php artisan db:seed --force
fi

echo "[entrypoint] Menjalankan FrankenPHP..."
exec frankenphp run --config /etc/frankenphp/Caddyfile --adapter caddyfile
