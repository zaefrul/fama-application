# Screen Map — Route to MockFlow Reference

Source PDF:

```text
design/original/Wireframe-Skrin-Flow.pdf
```

Page numbers below refer to the 29-page exported PDF.

## Reference priority

The PDF contains multiple generations of the design.

### FINAL / PRIMARY direction

Use the later structured flows as primary references:

- page 25 — latest login/onboarding concept
- page 26 — latest QR/application direction and combined dashboard concept
- page 27 — latest exporter dashboard, mobile + desktop
- page 28 — latest application list
- pages 18–21 — detailed company, QR/export, public traceability
- page 16 — FAMA dashboard
- page 15 — navigation/menu

### SUPPORTING functional references

Use only when a final screen does not show enough detail:

- pages 5–12
- page 14
- page 17
- page 23

### DO NOT use as GPL business-screen source

- page 13 — banking UI sample
- page 29 — NovaFunds financial dashboard sample

These must not introduce banking/finance content into Jejak GPL.

## Route map

| Route | Actor | Screen | Primary PDF Reference | Supporting Reference |
|---|---|---|---:|---:|
| `/auth/login` | All | Login | 25 | 5, 14 |
| `/auth/register/exporter` | Exporter | Company lookup + onboarding | 25 | 5, 14 |
| `/auth/register/fama` | FAMA | Staff lookup + onboarding | 25 | 5, 14 |
| `/exporter` | Exporter | Dashboard | 27 | 17, 11, 12 |
| `/exporter/company` | Exporter | Company profile | 18 / 23 | 10 |
| `/exporter/company/produce` | Exporter | Agricultural produce | 18 | 11, 12 |
| `/exporter/company/certificates` | Exporter | Certificate management | 18 | 10 |
| `/exporter/company/gallery` | Exporter | Gallery | 18 / 23 | 10 |
| `/exporter/applications` | Exporter | Application list | 28 | 27 |
| `/exporter/applications/new` | Exporter | New export application | 19 / 20 / 26 | 12 |
| `/exporter/applications/:id` | Exporter | Application detail/summary | 19 / 20 / 26 | 9 |
| `/exporter/qr` | Exporter | QR list | 19 / 26 | 6, 12 |
| `/exporter/qr/:id` | Exporter | QR detail | 19 / 26 | 9 |
| `/exporter/qr/:id/download` | Exporter | QR download options | 19 | 12 |
| `/fama` | FAMA | Monitoring dashboard | 16 | 12 |
| `/fama/companies` | FAMA | Company list | 18 / 23 | — |
| `/fama/companies/new` | FAMA | Daftar vendor | no MockFlow — FAMA ops reuse | 18 / 23 |
| `/fama/companies/:id` | FAMA | Company detail / edit | 18 / 23 | 10 |
| `/fama/companies/:id/qr/new` | FAMA | Cipta QR | no MockFlow — FAMA ops reuse | 19 / 20 / 26 |
| `/fama/companies/:id/qr/:applicationId` | FAMA | Edit public QR fields | no MockFlow — FAMA ops reuse | 19 / 20 / 26 |
| `/fama/applications` | FAMA | Review queue | 28 adapted for FAMA | 7 |
| `/fama/applications/:id` | FAMA | Review + approve/reject | 20 | 6, 7 |
| `/fama/qr` | FAMA | QR monitoring | 16 / 26 | 7 |
| `/trace/:qrCode` | Public | Active QR (mobile pamphlet) | 21 | 12 |
| `/trace/:qrCode` | Public | Active QR desktop — Profil Produk Disahkan | 21 (DD-012) | 12 |
| `/trace/:qrCode` | Public | Inactive QR state | 21 | 12 |

## Navigation

### Latest exporter mobile concept

Page 27/28 suggests:

- Utama
- Permohonan
- Kod QR
- Profil

with a prominent search control.

Use this as the preferred exporter navigation direction unless later changed.

### Earlier navigation

Earlier screens show:

- Utama
- Jejak Produk
- Sejarah
- Tetapan

Treat this as supporting/legacy unless a particular screen depends on it.

### FAMA navigation

Page 15/16 supports a menu concept including:

- Utama
- Pengurusan QR
- Kelulusan QR
- Maklumat Syarikat
- Log Keluar

The exact final wording may be normalized with the final functional route map.

## Implementation rule

Before implementing a route:

1. read this row;
2. inspect the primary PDF page/reference PNG;
3. inspect supporting page only if needed;
4. do not merge competing designs without documenting a decision.
