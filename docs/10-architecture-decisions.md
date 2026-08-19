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

## Adding a decision

```text
## ADR-XXX — Title

Status:
Date:
Decision:
Reason:
Consequences:
```
