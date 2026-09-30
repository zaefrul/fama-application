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

Prototype V1 uses a 1:1 relationship between `ExportApplication` and the root `QRCode`. Child lots from a later breakdown are separate QR rows (ADR-022). QR stock, bulk generation, and official FAMA label layouts remain open questions and are not implemented.

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

## ADR-020 — One usahawan may hold several DagangNet companies

**Status:** Temporary prototype decision  
**Date:** 2026-09-28

Registration can attach more than one DagangNet account number to a new usahawan login. `company_user` stores the links. `users.company_id` stays the active company, and the usahawan switches it from **Syarikat aktif**. The login email is the email of the first company in the list.

An account number already linked to another usahawan is rejected. Whether one company can have multiple users stays open.

Unused demo lookups for this path: `H0B00003` (Ladang Demo Utara) and `H0B00004` (Selatan Fresh). `H0B00001` and `H0B00002` stay with the seeded Ali and Siti logins.

**Reason:** The demo needs one usahawan registration to carry more than one DagangNet account.

**Consequences:** Exporter screens keep reading the active `company_id`. A login with one company does not show the switcher. This does not change FAMA-managed companies or the open question about several users on one company.

## ADR-021 — QR registration chooses keluaran or ternakan first

**Status:** Temporary prototype decision  
**Date:** 2026-09-28

Starting a QR, as usahawan or pegawai FAMA, shows a choice before the form: **Keluaran Pertanian** or **Haiwan Ternakan**. The choice is stored on `export_applications.product_kind`. After the draft is saved, the kind stays fixed.

Keluaran Pertanian keeps the existing form (varieti, gred, saiz, ladang, sijil CoC). Haiwan Ternakan uses a separate form: jenis ternakan, baka, bilangan (ekor), berat (kg), nama premis, rumah sembelih, tarikh sembelih, and no. sijil veterinar. Review, approval, and the public QR page follow the same pipeline and show the matching labels.

Jenis ternakan lives on `produce_types.category = LIVESTOCK`. The keluaran screen only lists `PRODUCE`. Seeded livestock names: Lembu, Kambing, Ayam, Kerbau. A missing name can still be added from the ternakan form.

**Reason:** The demo needs a livestock QR without replacing the buah and sayur application.

**Consequences:** This does not put livestock inside Peraturan GPL. Official field ownership stays open in `docs/09-open-questions.md`. The FAMA dashboard chart “10 buah paling kerap” still counts keluaran pertanian only.

## ADR-022 — Lot breakdown into child QR codes

**Status:** Temporary prototype decision  
**Date:** 2026-09-29

A verified export application still has one root QR. After that QR is `ACTIVE`, the company that holds a lot may split the remaining quantity into child QR codes. Each child is its own scannable code, points at its parent, and keeps `root_application_id` so the public page can show the original product, farm, and certificates.

The holder is the active company on the user (`users.company_id`). External logins stay `EXPORTER`. The layer — Pembekal, Pengeksport, Pemasar, Peruncit, or a named Lain-lain — is `companies.party_type` / `party_label`. There is no required sequence of parties. Depth is not fixed.

Child QR codes are created `ACTIVE`. FAMA does not approve each chunk. Selling the whole remainder marks that lot `SOLD` and blocks further splits. Selling part of the remainder creates terminal child QR codes with no holder login. Every node uses the root quantity unit as an integer. The public page shows this chunk and its ancestors. Downstream buyers stay on the holder screen and the FAMA application tree.

**Reason:** A single trunk, such as a banana lot, is broken into smaller lots as it moves along the chain, and each piece must stay traceable.

**Consequences:** `qr_codes.application_id` is unique only for the root in practice; the database unique constraint is removed so children can store null. Official per-child approval, public downstream lists, and unit conversion stay open in `docs/09-open-questions.md`.

## Adding a decision

```text
## ADR-XXX — Title

Status:
Date:
Decision:
Reason:
Consequences:
```
