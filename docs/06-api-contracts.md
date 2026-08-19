# 06 — Prototype API Contracts

These are prototype boundary contracts, not final production API specifications.

## Authentication

```text
POST /api/auth/login
POST /api/auth/register/exporter
POST /api/auth/register/fama
POST /api/auth/logout
```

## External mock lookups

```text
GET /api/integrations/dagangnet/company/:identifier
GET /api/integrations/ifama/staff/:identifier
```

## Company

```text
GET   /api/exporter/company
PATCH /api/exporter/company
```

## Produce

```text
GET    /api/exporter/produce
POST   /api/exporter/produce
PATCH  /api/exporter/produce/:id
DELETE /api/exporter/produce/:id
```

## Certificates

```text
GET    /api/exporter/certificates
POST   /api/exporter/certificates
PATCH  /api/exporter/certificates/:id
DELETE /api/exporter/certificates/:id
```

## Gallery

```text
GET    /api/exporter/gallery
POST   /api/exporter/gallery
PATCH  /api/exporter/gallery/:id
DELETE /api/exporter/gallery/:id
```

## Export applications

```text
GET   /api/exporter/applications
POST  /api/exporter/applications
GET   /api/exporter/applications/:id
PATCH /api/exporter/applications/:id
POST  /api/exporter/applications/:id/submit
```

## QR

```text
POST /api/exporter/applications/:id/qr
GET  /api/exporter/qr
GET  /api/exporter/qr/:id
GET  /api/exporter/qr/:id/download
```

## FAMA review

```text
GET  /api/fama/applications
GET  /api/fama/applications/:id
POST /api/fama/applications/:id/start-review
POST /api/fama/applications/:id/approve
POST /api/fama/applications/:id/reject
```

## Public traceability

```text
GET /api/public/trace/:qrCode
```

## Audit

```text
GET /api/exporter/audit
GET /api/fama/audit
```

## Contract rules

- Validate API input server-side.
- Enforce authorization server-side.
- Perform status changes through dedicated commands/actions.
- UI must not directly update status columns.
- Approval endpoint writes both decision and audit trail.
- Public API returns only public-safe fields.
- Final payload shape may evolve during implementation, but business boundaries should remain stable.
