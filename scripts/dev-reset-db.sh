#!/usr/bin/env bash
set -euo pipefail
echo "ATENÇÃO: isto apagará o banco local Docker."
read -r -p "Digite RESET para continuar: " confirm
if [ "$confirm" != "RESET" ]; then
  echo "Cancelado."
  exit 0
fi
docker compose down -v
docker compose up -d mysql redis mailpit app
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan storage:link || true
