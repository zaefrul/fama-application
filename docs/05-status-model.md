# 05 — Status Model

Application status and QR status MUST remain separate.

## Application Status

```text
DRAFT
  ↓
SUBMITTED
  ↓
UNDER_REVIEW
  ├── APPROVED
  └── REJECTED
```

### DRAFT

- editable by exporter;
- not waiting for FAMA decision.

### SUBMITTED

- submitted by exporter;
- pending review.

### UNDER_REVIEW

- being reviewed by FAMA.

### APPROVED

- accepted by FAMA;
- approval audit required.

### REJECTED

- rejected by FAMA;
- rejection audit required.

Resubmission behavior is an open requirement.

## QR Status

```text
NOT_GENERATED
      ↓
GENERATED_INACTIVE
      ↓
ACTIVE
```

### NOT_GENERATED

No QR exists.

### GENERATED_INACTIVE

QR ID and public URL exist, but QR is not yet active. The public page must show an inactive message.

### ACTIVE

Related record has reached the approved prototype condition and public traceability is visible.

## V1 transition rule

Preferred prototype decision:

```text
Application APPROVED
→ QR ACTIVE
```

A rejected application does not activate its QR.

Do not introduce without approval:

- EXPIRED
- SUSPENDED
- REVOKED
- REACTIVATED

## UI terminology mapping

| Wireframe wording | Canonical concept |
|---|---|
| Draft | DRAFT |
| Review / Dalam Semakan | SUBMITTED / UNDER_REVIEW depending context |
| Approved / Diluluskan | APPROVED |
| Rejected / Ditolak | REJECTED |
| Belum Aktif | GENERATED_INACTIVE |
| Aktif | ACTIVE |

Use canonical values internally. UI labels may remain in Malay according to final design.
