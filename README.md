# Sistem Jejak GPL — Agent Project Pack

This folder is the working application for the **Sistem Jejak GPL prototype**.

The project is based primarily on the FAMA MockFlow wireframe exported as `design/original/Wireframe-Skrin-Flow.pdf`.

## Source-of-truth order

When sources conflict, use this priority:

1. Approved stakeholder decision / ADR
2. `docs/FAMA_Jejak_GPL_Prototype_Requirements.md`
3. Supporting functional documents under `docs/`
4. `design/SCREEN_MAP.md`
5. Final-design MockFlow reference pages
6. Supporting / earlier MockFlow concepts
7. Developer assumptions

An assumption MUST NOT override a documented requirement.

## Run the prototype (UI-first / fixture data)

```bash
cp .env.example .env.local
npm install
npm run dev
```

Seed accounts (fixture mode):

```text
Exporter: ali@abcfruits.example / Exporter123!
FAMA:     aliabu@fama.gov.my / Fama123!
DagangNet demo id: H0B00001
iFAMA demo IC:     770101145533
```

Public traces:

- Inactive: `/trace/GPL-QR-000123`
- Active: `/trace/GPL-QR-000109`

## Optional PostgreSQL

```bash
docker compose up -d
# set DATA_SOURCE=prisma and DATABASE_URL in .env.local
npx prisma db push
npm run db:seed
```

## Scripts

```bash
npm run typecheck
npm run lint
npm test
npm run test:e2e
```

## Core principle

The agent must implement the **approved prototype**, not redesign the product and not fill stakeholder gaps with invented logic.
