#!/usr/bin/env bash
set -euo pipefail

if [ ! -f artisan ]; then
  echo "Execute este script na raiz do repositório DigitalSportsCrm."
  exit 1
fi

if [ ! -f .env ]; then
  cp .env.docker.example .env
  echo "Arquivo .env criado a partir de .env.docker.example"
fi

docker compose build
docker compose up -d mysql redis mailpit app

docker compose exec app composer install
docker compose exec app npm ci

if ! grep -q '^APP_KEY=base64:' .env; then
  docker compose exec app php artisan key:generate
fi

docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link || true
docker compose exec app npm run build
docker compose exec app php artisan about

echo
echo "Ambiente local pronto."
echo "Aplicação: http://localhost:8080"
echo "Mailpit:    http://localhost:8025"
echo "MySQL:      127.0.0.1:3307 / database=digital_sports_crm / user=cbes / pass=cbes"
echo "Admin dev:  admin@cbes.test / ChangeMe#2026"
