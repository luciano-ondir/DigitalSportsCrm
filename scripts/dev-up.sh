#!/usr/bin/env bash
set -euo pipefail
docker compose up -d mysql redis mailpit app
echo "Aplicação: http://localhost:8080"
echo "Mailpit:    http://localhost:8025"
