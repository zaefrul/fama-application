# Sistem Jejak GPL — Laravel port

Laravel + Blade port of the approved **Sistem Jejak GPL** prototype. Requirements and MockFlow references in `docs/` and `design/` are unchanged.

This is the `laravel` branch. `main` remains the Next.js prototype.

## Run

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Seed accounts:

```text
Exporter: ali@abcfruits.example / Exporter123!
FAMA:     aliabu@fama.gov.my / Fama123!
DagangNet demo id: H0B00001
iFAMA demo IC:     770101145533
```

Public traces:

- Inactive: `/trace/GPL-QR-000123`
- Active: `/trace/GPL-QR-000109`

## Scripts

```bash
php artisan test
vendor/bin/pint
```

## Optional MySQL

```bash
docker compose up -d
# set DB_CONNECTION=mysql and DB_* in .env
php artisan migrate --seed
```
