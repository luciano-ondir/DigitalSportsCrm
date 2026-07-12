# Kit Docker local — CBES / DigitalSportsCrm

Este kit deve ser copiado para a raiz do repositório `DigitalFederation/DigitalSportsCrm`.

## Serviços

- `app`: Apache + PHP 8.3 + Composer 2 + Node 22 + Chromium headless.
- `mysql`: MySQL 9.7, com `utf8mb4`.
- `redis`: opcional para testes de cache/fila.
- `mailpit`: captura e-mails locais.
- `queue` e `scheduler`: opcionais via profile `workers`.

## Instalação inicial

```bash
git clone https://github.com/DigitalFederation/DigitalSportsCrm.git cbes-crm
cd cbes-crm

# copie os arquivos deste kit para a raiz do projeto
cp -r /caminho/para/cbes-docker-dev-kit/. .

chmod +x scripts/dev-*.sh
bash scripts/dev-setup.sh
```

Acesse:

- Aplicação: http://localhost:8080
- Mailpit: http://localhost:8025
- MySQL local: 127.0.0.1:3307

Credencial inicial de desenvolvimento, se o seeder criar o admin:

- e-mail: `admin@cbes.test`
- senha: `ChangeMe#2026`

Troque esses valores em `.env` se desejar.

## Uso diário

```bash
bash scripts/dev-up.sh
docker compose exec app php artisan test
docker compose exec app npm run build
```

Para Vite com HMR:

```bash
docker compose exec app npm run dev -- --host 0.0.0.0
```

## Queue e scheduler

Por padrão, `.env.docker.example` usa `QUEUE_CONNECTION=sync`, bom para desenvolvimento simples.

Para testar fila e agendador:

1. altere no `.env`:

```ini
QUEUE_CONNECTION=database
```

2. suba os workers:

```bash
docker compose --profile workers up -d queue scheduler
```

## PDF/Chromium

O container define:

```ini
CHROME_BIN=/usr/bin/chromium
PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
PUPPETEER_SKIP_DOWNLOAD=true
```

Teste:

```bash
docker compose exec app chromium --headless --no-sandbox --disable-gpu --print-to-pdf=/tmp/teste.pdf https://example.com
docker compose exec app ls -lh /tmp/teste.pdf
```

No código Laravel/Browsershot, use `/usr/bin/chromium` quando for necessário informar o caminho do Chrome/Chromium.

## Banco local

O banco Docker é descartável. Para apagar e recriar:

```bash
bash scripts/dev-reset-db.sh
```

## Observações de segurança

Não use este `.env` em produção. Ele contém senha local simples e `APP_DEBUG=true`.
