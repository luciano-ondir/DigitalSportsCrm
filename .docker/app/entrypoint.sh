#!/usr/bin/env bash
set -e

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if [ "${CBES_AUTO_INSTALL:-0}" = "1" ]; then
  if [ ! -d vendor ]; then
    gosu www-data composer install
  fi

  if [ ! -d node_modules ]; then
    gosu www-data npm ci
  fi
fi

exec "$@"
