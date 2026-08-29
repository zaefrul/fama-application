# 10 — Architecture Decisions

## ADR-001 — Modular Monolith

**Status:** Accepted for Prototype V1

Use one Next.js application with clear internal modules.

Reason: prototype speed, simple deployment, sufficient separation, and avoidance of premature distributed architecture.

## ADR-002 — PostgreSQL + Prisma

**Status:** Baseline

Use PostgreSQL for prototype persistence and Prisma ORM for schema/migrations unless a concrete project constraint changes this.

## ADR-003 — Mock External Integrations

**Status:** Accepted

DagangNet and iFAMA are simulated behind provider interfaces.

## ADR-004 — Separate Application and QR States

**Status:** Accepted

Application workflow and QR lifecycle are modeled independently.

## ADR-005 — QR Activation after Approval

**Status:** Prototype assumption

For the prototype:

```text
Application APPROVED
→ QR ACTIVE
```

This must be reconfirmed before production.

## ADR-006 — Seed-Based Demo Data

**Status:** Accepted

Demo data comes from seed scripts, not hardcoded UI.

## ADR-007 — Stable Public Traceability Route

**Status:** Accepted concept

QR resolves to a stable public route:

```text
/trace/:qrCode
```

The QR identifier should not change when status changes.

## ADR-008 — Farm and Importer

**Status:** Temporary prototype decision

Do not create standalone master modules until stakeholder confirmation. Store relevant details on the export application for V1.

## ADR-009 — One QR per application

**Status:** Temporary prototype decision  
**Date:** 2026-08-19

Prototype V1 uses a 1:1 relationship between `ExportApplication` and `QRCode`. QR stock, bulk generation, and official FAMA label layouts remain open questions and are not implemented.

## ADR-010 — UI-first fixture repository

**Status:** Accepted for Prototype V1  
**Date:** 2026-08-19

Screens depend on repository interfaces. Default `DATA_SOURCE=fixture` uses an in-memory store so the UI runs without PostgreSQL. `DATA_SOURCE=prisma` swaps in the Prisma implementation after Docker/migrate/seed.

## ADR-011 — Canonical MockFlow set

**Status:** Accepted  
**Date:** 2026-08-19

Primary screens are PDF pages 25–28, 18–21, 16 and 15. Pages 13 and 29 are excluded. See `design/SCREEN_MAP.md`.

## ADR-012 — Rejected applications are view-only

**Status:** Temporary prototype decision  
**Date:** 2026-08-19

Rejected applications cannot be edited or resubmitted until stakeholders confirm. Rejection remarks are required.

## ADR-013 — Public page language and fields

**Status:** Temporary prototype decision  
**Date:** 2026-08-19

Authenticated UI is BM. Public `/trace/:qrCode` supports BM/EN and a 中文 stub. The public page shows the fields visible on MockFlow page 21, including addresses and certificate thumbnails, as a display assumption pending privacy confirmation. Nutrition is shown only when data exists.

## ADR-014 — FAMA-managed vendor and QR

**Status:** Temporary prototype decision  
**Date:** 2026-08-21

Pegawai FAMA may register a vendor as a company-only record (`externalSource = FAMA`, no exporter login) and create an active QR in one flow.

Reason: MAHA/ops needs FAMA to prepare a small vendor cohort without booth-side exporter self-service.

Rules:

- Create + activate walks existing transitions: `DRAFT` → `SUBMITTED` → `UNDER_REVIEW` → `APPROVED` + QR `ACTIVE`.
- Approval remarks for this path: `Dicipta dan diaktifkan oleh FAMA`.
- FAMA may edit FAMA-sourced company fields (including name and registration no.) and APPROVED public application fields after activation.
- DagangNet-seeded companies keep name and registration no. read-only.
- QR identity (`qrCode`, `publicSlug`) does not change on edit.
- Exporter self-service and DRAFT-only exporter edits stay unchanged.
- Binding a FAMA-created company to DagangNet or an exporter login remains an open question.

## ADR-015 — Laravel port branch

**Status:** Accepted for the `laravel` branch only  
**Date:** 2026-08-21

This branch ports the approved Next.js prototype to Laravel + Blade + Eloquent + Tailwind.

It does **not** change the accepted Prototype V1 stack on `main` (ADR-001 / ADR-002). Business rules, routes, seed accounts, and MockFlow screens stay the same. SQLite is the default local store; MySQL remains supported via `.env`.

## ADR-016 — Public QR access is a page view

**Status:** Temporary prototype decision  
**Date:** 2026-08-21

A known public HTML `/trace/{qrCode}` view is stored as one `QrAccess` row. Invalid codes and the public JSON API are not counted.

The FAMA dashboard compares this calendar week with last week (Monday start, Asia/Kuala_Lumpur) and shows a 7-day imbasan bar chart plus the three most-scanned QRs.

**Reason:** Stakeholders asked for “how many people access the QR”. Public visitors have no identity. Unique-visitor and IP storage remain open questions, so the prototype counts page views and labels them *imbasan*, not *orang*.

**Consequences:** Language switches and refreshes increment the count. No IP or user-agent is stored.

## ADR-017 — Official actor label is Usahawan

**Status:** Accepted for Prototype V1  
**Date:** 2026-08-27

User-visible Malay copy uses **Usahawan** instead of Pengeksport. Public EN uses Entrepreneur; ZH uses 业者.

Internal identifiers stay `Role::EXPORTER`, `/exporter/*` routes, and `Exporter*` classes.

**Reason:** SA terminology (2026-08-27). A route/enum rename is out of scope and would not change behaviour.

**Consequences:** Docs, login, dashboards, application views, public `/trace`, and tests use Usahawan. API path `/api/exporter` is unchanged.

## ADR-018 — Optional export date, lot, farm location, and QR display image

**Status:** Temporary prototype decision  
**Date:** 2026-08-27

`ExportApplication` stores:

- `export_date` (nullable);
- `lot_no` (nullable);
- `farm_location` (nullable text);
- `farm_lat` / `farm_lng` (nullable);
- `display_image_path` (nullable; QR hero).

Farm details stay on the application (ADR-008). Geolocation is optional, not required. Public `/trace` embeds OpenStreetMap when both coordinates exist.

FAMA review of uploaded photos before they go public is not specified and is not implemented.

**Reason:** SA asked for usahawan-uploaded premium images, lot number, interactive farm location, and a non-mandatory export date.

**Consequences:** Empty export date is hidden on the public page, not shown as a blank. Missing display image falls back to company gallery, then a marked placeholder.

## ADR-019 — Users may add a missing produce type from the form

**Status:** Temporary prototype decision  
**Date:** 2026-08-29

Authenticated usahawan and FAMA officers search **Jenis Keluaran Pertanian** with an autocomplete (type to filter a scrollable list) and can press **+** to add a name that is not in the list. The name is stored on shared `produce_types` (case-insensitive reuse) and linked to the company.

If two users add the same new name at once, a transaction looks up the name first and reuses that id; a unique-name clash still resolves to the existing row instead of failing.

Official ownership of produce master data remains an open question (`docs/09-open-questions.md`). This does not add a FAMA-only catalogue admin screen.

**Reason:** Requested so a keluaran can be recorded when the seeded list is incomplete.

**Consequences:** New names become selectable for every company. Duplicate company rows for the same type are not created. `company_produce` is unique per company and produce type.

## Adding a decision

```text
## ADR-XXX — Title

Status:
Date:
Decision:
Reason:
Consequences:
```
