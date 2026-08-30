# Shared UI Components

Build reusable components rather than cloning markup per screen.

## GovMasthead

Official top chrome: Jalur Gemilang hairline and “Laman Rasmi FAMA” utility bar.

## GovFooter

Official footer: FAMA, ministry, copyright.

## Icon

Shared inline SVG set. Do not use emoji as navigation or header icons.

## GovVerifiedBanner

Public active-QR trust banner: “Disahkan oleh FAMA”.

## AppHeader

Used across authenticated areas.

Supports:

- FAMA mark and Sistem Jejak GPL identity;
- menu/back control depending screen;
- notification indicator;
- signed-in officer/exporter name on desktop.

## MobileBottomNav

Preferred latest exporter items:

- Utama
- Permohonan
- Kod QR
- Profil

Do not mix with earlier nav labels on the same experience unless explicitly decided.

## Sidebar

Desktop/FAMA navigation component.

## PageHeader

Contains page title, optional breadcrumb and optional action.

## Card

Base white surface. No page should invent an unrelated card radius/shadow.

## StatCard

Used for:

- QR Aktif
- QR Belum Aktif
- Permohonan / Permohonan QR
- Approved
- Rejected
- Syarikat Aktif
- Usahawan
- Buah unik
- Destinasi
- Imbasan QR

## RankBars

Horizontal ranked bars for categorical dashboard lists (top fruits, destinations, negeri). Each row uses the next chart-palette token (`--chart-1` … `--chart-10`).

## QrAccessChart

FAMA dashboard comparison:

- this week vs last week;
- 7-day imbasan bars;
- top scanned QRs.

Suggested inputs:

```text
label
value
semantic/status
icon
href?
```

## StatusBadge

Canonical application statuses:

- DRAFT
- SUBMITTED
- UNDER_REVIEW
- APPROVED
- REJECTED

Canonical QR statuses:

- GENERATED_INACTIVE
- ACTIVE

UI label can be Malay/English according to the selected screen reference.

## SearchInput

Used on company, application, QR and approval lists.

## FormField

Shared label/input/helper/error structure.

## ReadOnlyField

For information retrieved from DagangNet/iFAMA or otherwise authoritative.

## PrimaryButton

Main affirmative action.

## SecondaryButton

Back/cancel/secondary action.

## DangerButton

Reject/delete.

## ApplicationCard

Mobile application list item exposing:

- application no;
- produce;
- submission date;
- status;
- navigation/action indicator.

## QRCard

Shows QR image/identifier, status, product and primary action.

## CertificateCard

Shows certificate type, status, number/validity if available and document thumbnail if available.

## GalleryCard

Shows image, category, description/date and edit action.

## ApprovalPanel

FAMA-only action area supporting approve, reject, optional remarks and status summary.

## AuditTimeline / AuditList

Shows action, actor, timestamp, object/context and remarks.

## EmptyState

Use for no applications, no QR, no gallery, etc.

## ErrorState

Use for invalid QR, external lookup failure and not-found records.

## QRViewer

Common QR rendering wrapper with safe quiet-zone sizing.

## PublicTraceabilitySection

Reusable public-page sections:

- Product
- Export
- Supplier
- Certificates
- Nutrition

Desktop `/trace` also uses a Profil Keluaran Pertanian shell: identity hero, main record column, sticky QR / Hubungi Kami rail. Mobile keeps the pamphlet.
