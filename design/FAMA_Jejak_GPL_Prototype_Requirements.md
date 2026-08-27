# Sistem Jejak GPL — Prototype Requirements & Development Blueprint

**Project:** Sistem Jejak GPL  
**Client / Owner:** FAMA  
**Document Type:** Prototype Functional Requirement & Development Blueprint  
**Version:** 0.1 Draft  
**Date:** 19 August 2026  
**Primary Reference:** `Wireframe-Skrin Flow.pdf`

---

## 1. Document Purpose

This document defines the functional and technical baseline for developing the **Sistem Jejak GPL prototype** based on the current FAMA MockFlow wireframe and the design decisions discussed during requirement review.

The prototype is intended to demonstrate the end-to-end concept of agricultural export traceability using QR codes, including exporter onboarding, company/product setup, export application, FAMA review, QR activation, QR printing, and public traceability.

This is a **prototype development baseline**, not a final production specification. Where the wireframe is unclear or inconsistent, the development team and coding agent MUST NOT silently invent business rules. Unresolved items MUST be recorded under **Open Questions / Stakeholder Confirmation**.

---

## 2. Source of Requirements

Primary source:

- `Wireframe-Skrin Flow.pdf`

The wireframe contains multiple iterations of the proposed UI and process. Later screens are generally more complete, while earlier screens contain useful concepts such as QR generation, approval, scanning, product information, audit trail and company certification.

Generic banking/finance dashboard samples appearing in the export are considered **visual references only** and are not GPL business requirements unless explicitly confirmed by FAMA.

---

# 3. Product Vision

Sistem Jejak GPL is a traceability platform that allows agricultural exporters to register product/export information, obtain FAMA verification, generate and activate unique QR codes, attach the QR to agricultural export packaging, and allow buyers/consumers to view verified traceability information.

Target business flow:

```text
Exporter Registration
        ↓
Company Verification
        ↓
Company / Produce / Certification Setup
        ↓
Create Export Application
        ↓
Generate QR
        ↓
Submit for FAMA Review
        ↓
FAMA Approval / Rejection
        ↓
QR Activation
        ↓
QR Download / Print
        ↓
QR Attached to Product / Packaging
        ↓
Public QR Scan
        ↓
Verified Traceability Information
```

---

# 4. Prototype Objectives

The prototype SHALL demonstrate:

- exporter registration and login;
- FAMA officer registration and login;
- mock DagangNet company lookup;
- mock iFAMA staff lookup;
- exporter company profile;
- agricultural produce information;
- certification information;
- company/product gallery;
- export application creation;
- QR generation;
- FAMA review;
- approval/rejection;
- QR activation;
- QR download/print;
- public QR verification;
- exporter dashboard;
- FAMA dashboard;
- audit trail;
- realistic seed/demo data.

The prototype SHALL support stakeholder validation, business process discussion, usability review, future integration planning and clarification of production requirements.

---

# 5. Scope

## 5.1 In Scope

| No. | Area | Prototype Requirement |
|---|---|---|
| 1 | Authentication | FAMA and Exporter login |
| 2 | Registration | Exporter and FAMA onboarding |
| 3 | External Verification | Mock DagangNet and mock iFAMA |
| 4 | User Management | Basic account and role handling |
| 5 | Company Profile | Exporter company information |
| 6 | Agricultural Produce | Produce / fruit information |
| 7 | Certification | MyGAP, HACCP, CoC, Fitosanitasi, etc. |
| 8 | Gallery | Company, farm, lot and product images |
| 9 | Export Application | Create/manage export-related application |
| 10 | QR Management | Generate/manage QR |
| 11 | Approval | FAMA review, approve, reject |
| 12 | QR Activation | Activate QR after approved state |
| 13 | QR Printing | Download/print prototype QR |
| 14 | Public Traceability | Public QR verification |
| 15 | Dashboard | Exporter and FAMA monitoring |
| 16 | Notifications | Lightweight prototype notifications |
| 17 | Audit Trail | Significant business activities |
| 18 | Seed Data | Realistic demo records |

## 5.2 Out of Scope for Prototype V1

Unless separately approved:

- real DagangNet production integration;
- real iFAMA production integration;
- payment processing;
- production SSO/federation;
- PKI/digital signature implementation;
- high availability;
- Kubernetes;
- microservices;
- disaster recovery design;
- SIEM integration;
- native mobile apps;
- production-scale analytics;
- live external certificate verification;
- label-printer hardware integration.

---

# 6. Primary Actors

## 6.1 Usahawan

The usahawan (internal role `EXPORTER`) SHALL be able to:

- register;
- log in;
- retrieve/verify company information through mock DagangNet;
- view/update company profile;
- maintain agricultural produce;
- maintain certification information;
- upload gallery images;
- create export applications;
- save draft;
- generate QR;
- submit application for FAMA review;
- view application status;
- view QR status;
- view approved/rejected applications;
- download/print QR;
- view relevant audit history;
- view exporter dashboard.

## 6.2 Pegawai FAMA

The FAMA officer SHALL be able to:

- register using mock iFAMA;
- log in;
- view FAMA dashboard;
- view companies;
- view pending applications;
- inspect application details;
- inspect company/product/certification information;
- approve;
- reject;
- view QR status;
- view audit trail and approval history.

## 6.3 Public / Consumer

The public user SHALL NOT require authentication.

The public user SHALL be able to:

- open a QR verification URL;
- see whether QR is active;
- see an inactive/not-activated state;
- view verified product information for active QR;
- view exporter information;
- view export information;
- view farm/importer information where approved for public display;
- view certifications where available;
- view nutritional information where available.

---

# 7. Authentication and Login

The system SHALL provide a login experience for:

- FAMA;
- Usahawan.

Minimum fields:

- Email / User ID;
- Password.

Minimum actions:

- Log Masuk;
- Daftar;
- Lupa Kata Laluan.

Actor separation may be implemented through an actor selector or role-aware login.

---

# 8. Exporter Registration

The exporter registration process SHALL simulate DagangNet verification.

```text
Enter Exporter / Company Identifier
        ↓
Mock DagangNet Lookup
        ↓
Record Found?
   ├── No → "Tiada rekod dijumpai"
   └── Yes
          ↓
Display Company Information
          ↓
Capture User Information
          ↓
Create Password
          ↓
Create Exporter Account
```

## 8.1 Lookup

The prototype SHALL support both:

- record found;
- record not found.

## 8.2 Retrieved Company Information

Mock data may include:

- Nama Syarikat;
- Email Syarikat;
- Status;
- No. Pendaftaran;
- other basic company information.

Retrieved authoritative fields SHOULD be read-only during onboarding unless later confirmed otherwise.

## 8.3 User Information

The prototype SHALL capture:

- No. Kad Pengenalan Pengguna;
- Nama Pengguna;
- Password;
- Password confirmation.

---

# 9. FAMA Officer Registration

The FAMA registration process SHALL simulate identity lookup from iFAMA.

```text
Enter Staff Identity / IC
        ↓
Mock iFAMA Lookup
        ↓
Record Found
        ↓
Display Staff Information
        ↓
Create Password
        ↓
Activate Account
```

Potential returned fields:

- Nama Penuh;
- Email;
- Jawatan;
- staff status.

---

# 10. External Integration Design

External systems SHALL be implemented behind interfaces/adapters.

Recommended pattern:

```text
CompanyRegistryProvider
    └── MockDagangNetProvider

StaffDirectoryProvider
    └── MockIFAMAProvider
```

Future real implementations may replace the mock providers without changing the core UI/business flow.

Hardcoding DagangNet/iFAMA demo responses directly inside pages is NOT allowed.

---

# 11. Exporter Dashboard

The exporter dashboard SHOULD include:

- company welcome/profile summary;
- QR Aktif;
- QR Belum Aktif;
- application summary;
- quick action: Maklumat Keluaran Pertanian;
- quick action: Cetak QR;
- quick action: Kelulusan QR;
- company gallery;
- notifications/recent activity where useful.

Potential KPI cards:

- QR Aktif;
- QR Belum Aktif;
- Jumlah Permohonan;
- Permohonan Lulus;
- Permohonan Ditolak.

---

# 12. FAMA Dashboard

The FAMA dashboard SHOULD display:

- Syarikat Aktif;
- QR Aktif;
- Permohonan Telah Diluluskan;
- Permohonan Menunggu Pengesahan;
- Permohonan Ditolak.

It SHOULD also demonstrate simple monitoring such as:

**Pemantauan Harian Bilangan QR Yang Dijana**

with:

- QR Aktif;
- QR Belum Aktif.

Dashboard cards SHOULD link to the corresponding lists where practical.

---

# 13. Company Information Management

Company profile fields may include:

- No. Pendaftaran;
- Nama Syarikat;
- Alamat;
- Negeri;
- Daerah;
- Poskod;
- No. Telefon;
- Tarikh Pendaftaran;
- Email;
- Laman Web;
- Logo Syarikat.

Some data may originate from DagangNet. Field ownership/editability MUST remain configurable until confirmed.

---

# 14. Agricultural Produce Management

One exporter MAY have multiple agricultural produce records.

Examples from the wireframe:

- Durian;
- Nangka;
- Manggis;
- Tembikai;
- Nanas;
- Pisang;
- Betik.

Recommended relationship:

```text
Company
  └── one-to-many CompanyProduce
```

Possible attributes:

- Jenis Keluaran Pertanian;
- Jenis Buah;
- Varieti;
- Gred;
- Saiz;
- Berat / Kuantiti;
- Tarikh Penuaian where applicable;
- Lokasi Ladang where applicable.

---

# 15. Certification Management

The prototype SHALL support certification records such as:

- MyGAP;
- HACCP;
- ISO 22000;
- CoC;
- Fitosanitasi.

Functions:

- add certificate;
- list/display certificates;
- view certificate image/document;
- associate relevant certificates to company/application;
- store certificate number where applicable;
- include audit history.

No live certificate-authority validation is required for V1.

---

# 16. Gallery / Image Management

The prototype SHALL support image categories such as:

- Kebun;
- Lot Kebun;
- Buah.

Suggested fields:

- image file;
- category;
- description;
- uploaded date;
- uploaded by;
- related company;
- optional related produce/application.

Required actions:

- Add;
- View;
- Edit;
- Delete;
- Gallery listing.

---

# 17. Export Application / Export Batch

The primary transactional object SHOULD be an **Export Application / Export Batch**, not only a generic product record.

Recommended relationship:

```text
Company
  └── ExportApplication
        ├── Agricultural Produce
        ├── Export Information
        ├── Farm Information
        ├── Importer Information
        ├── Certificate Reference
        ├── Approval
        └── QRCode
```

---

# 18. Export Application Data

## 18.1 Product / Produce

- Jenis Keluaran Pertanian;
- Varieti;
- Gred;
- Saiz;
- Berat / Kuantiti;
- Destinasi;
- No. Sijil CoC.

## 18.2 Export Information

- Tarikh Eksport;
- Bilangan Eksport / Quantity;
- Nama Ladang;
- Pengimport;
- Alamat Pengimport.

## 18.3 Exporter Information

Exporter/company details SHOULD be derived from the company profile where possible and not repeatedly re-entered.

---

# 19. Application Status Model

The wireframe uses terms including Draft, Review, Approved, Rejected, Belum Lulus, Dalam Semakan, Diluluskan and Ditolak.

The prototype SHALL standardize the application lifecycle separately from QR status.

Recommended lifecycle:

```text
DRAFT
  ↓
SUBMITTED
  ↓
UNDER_REVIEW
  ├── APPROVED
  └── REJECTED
```

Rules:

- new applications start as `DRAFT`;
- draft can be edited;
- submission creates a status transition;
- FAMA review leads to approval/rejection;
- status transitions SHALL use centralized business logic;
- approval/rejection SHALL create audit records;
- resubmission after rejection remains an open question.

---

# 20. QR Status Model

Recommended QR lifecycle:

```text
NOT_GENERATED
GENERATED_INACTIVE
ACTIVE
```

Do NOT implement additional statuses such as:

- REVOKED;
- EXPIRED;
- SUSPENDED;

unless confirmed by FAMA.

---

# 21. QR Generation

The prototype SHALL:

- generate a unique QR identifier;
- generate a QR image;
- map the QR to a stable public traceability URL;
- allow the QR to exist before activation;
- show inactive public state before approval/activation.

Conceptual URL:

```text
https://<prototype-domain>/trace/<qr-code>
```

Conceptual ID:

```text
GPL-QR-000123
```

Exact production format remains to be confirmed.

---

# 22. QR Approval and Activation

Preferred prototype flow:

```text
Exporter completes application
        ↓
QR generated as inactive
        ↓
Exporter submits application
        ↓
FAMA reviews
        ↓
Approve / Reject
        ↓
Approved → QR ACTIVE
Rejected → QR remains inactive
```

Prototype decision:

> QR becomes ACTIVE only after the related application is approved by FAMA.

This is a prototype assumption and MUST remain documented until formally confirmed.

---

# 23. FAMA Approval Screen

The FAMA approval function SHOULD support:

- search by QR ID;
- search by product name;
- application/QR listing;
- generation/submission date;
- current status;
- application details;
- supporting information;
- Approve;
- Reject;
- Audit Trail.

Approval details may include:

- QR ID;
- product;
- date;
- status;
- validation checklist;
- approving officer;
- approval date;
- remarks.

---

# 24. QR Download / Print

The prototype SHALL demonstrate:

- QR selection;
- QR size selection;
- output format;
- download/print.

Wireframe options include:

- 3 cm;
- 5 cm;
- Custom;
- PDF;
- PNG.

A final physical FAMA label format is not assumed.

---

# 25. Public QR Traceability

The public traceability page is a core requirement.

## 25.1 Active QR

The system SHOULD display:

### Product
- Product / Fruit Name;
- Country;
- Grade;
- Size;
- Weight / Quantity.

### Export
- Export Date;
- Destination;
- Exporter;
- Exporter Address;
- Farm Name;
- Importer;
- Importer Address;
- CoC Number.

### Certification
- HACCP;
- MyGAP;
- CoC;
- Fitosanitasi;
- other approved certificates.

### Nutrition
- nutritional information where available.

## 25.2 Inactive QR

An inactive QR SHALL display a clear not-active state.

Concept:

```text
QR Belum Diaktifkan

QR ini telah dijana / dicetak tetapi belum diaktifkan.
```

Final wording MUST be confirmed.

---

# 26. Audit Trail

Significant actions SHALL generate audit records.

Suggested fields:

- Actor User ID;
- Actor Name;
- Actor Role;
- Action;
- Object Type;
- Object ID;
- Old Value;
- New Value;
- Timestamp;
- Remarks;
- optional IP Address;
- optional User Agent.

Audit events SHOULD include:

- registration;
- company update;
- certificate upload;
- gallery changes;
- application create/update;
- application submission;
- QR generation;
- approval;
- rejection;
- QR activation;
- QR download;
- important status transitions.

---

# 27. Notifications

Prototype notifications MAY include:

- application submitted;
- pending review;
- approved;
- rejected;
- QR activated;
- QR pending activation.

Real SMS/email/push integration is not required for V1.

---

# 28. Proposed Domain Model

Suggested initial entities:

```text
User
Role

Company
CompanyUser

ProduceType
CompanyProduce

Certificate
GalleryItem

ExportApplication
ExportApplicationItem

QRCode
Approval

AuditLog
Notification
```

Simplified relationship:

```text
User
 └── CompanyUser
      └── Company
           ├── CompanyProduce
           ├── Certificate
           ├── GalleryItem
           └── ExportApplication
                 ├── QRCode
                 ├── Approval
                 └── AuditLog
```

Do not over-normalize prototype V1.

Until confirmed, Farm and Importer MAY initially be application-level fields rather than reusable master entities.

---

# 29. Proposed Routes

## Public

```text
/
/auth/login
/auth/register/exporter
/auth/register/fama
/trace/:qrCode
```

## Exporter

```text
/exporter
/exporter/company
/exporter/company/produce
/exporter/company/certificates
/exporter/company/gallery

/exporter/applications
/exporter/applications/new
/exporter/applications/:id
/exporter/applications/:id/edit

/exporter/qr
/exporter/qr/:id
/exporter/qr/:id/download

/exporter/audit
```

## FAMA

```text
/fama
/fama/companies
/fama/companies/:id

/fama/applications
/fama/applications/:id

/fama/qr
/fama/qr/:id

/fama/audit
```

---

# 30. Recommended Technology Stack

For prototype V1:

```text
Next.js
TypeScript
Tailwind CSS
PostgreSQL
Prisma ORM
Docker Compose
Playwright
QR generation library
```

Use a single web application unless a separate architecture is specifically required.

Do NOT introduce microservices for the prototype.

---

# 31. Recommended Repository Structure

```text
fama-jejak-gpl/
├── src/
│   ├── app/
│   ├── components/
│   ├── modules/
│   ├── services/
│   ├── domain/
│   ├── lib/
│   └── types/
├── public/
├── prisma/
│   ├── schema.prisma
│   └── seed.ts
├── tests/
├── docs/
├── scripts/
├── .env.example
├── AGENTS.md
├── README.md
└── package.json
```

---

# 32. Required Documentation

```text
docs/
├── 01-product-scope.md
├── 02-actors-and-roles.md
├── 03-screen-map.md
├── 04-business-flows.md
├── 05-domain-model.md
├── 06-status-model.md
├── 07-api-contracts.md
├── 08-integration-mocks.md
├── 09-test-scenarios.md
├── 10-open-questions.md
└── architecture-decisions.md
```

The coding agent SHALL read the relevant documents before implementing a major feature.

---

# 33. Seed Data

Use realistic demo data through a database seed script.

Suggested companies:

- ABC Fruits Sdn. Bhd.
- MTS Fruits Sdn. Bhd.

Suggested produce:

- Durian
- Nangka
- Manggis
- Tembikai
- Nanas

Suggested variety/grade:

- Musang King
- Premium
- Grade A

Suggested certificates:

- MyGAP
- HACCP
- CoC
- Fitosanitasi

Suggested application states:

- Draft;
- Under Review;
- Approved;
- Rejected.

Suggested QR states:

- Active;
- Inactive.

Do NOT hardcode demo records inside UI components.

---

# 34. UI Rules

The prototype SHALL follow the supplied wireframe closely enough for stakeholder validation.

Key screens SHOULD be checked at approximately:

- mobile: 390x844;
- desktop: 1440x900.

UI SHOULD prioritize:

- FAMA branding;
- clear information hierarchy;
- consistent status indicators;
- clear approval actions;
- responsive/mobile usability;
- clear QR state;
- readable forms and validation.

Major screens SHOULD be compared against their MockFlow reference.

---

# 35. Coding Agent Rules

The agent SHALL:

1. Not invent business requirements.
2. Put unclear items into `docs/10-open-questions.md`.
3. Not hardcode demo data in React components.
4. Use seed data.
5. Keep significant business logic outside UI components.
6. Use centralized enums/constants for statuses.
7. Put external integrations behind provider/adapter interfaces.
8. Create audit records for approval actions.
9. Validate important status transitions.
10. Define acceptance criteria for features.
11. Run lint/typecheck/tests before completion.
12. Avoid unnecessary infrastructure.
13. Avoid microservices for V1.
14. Follow the approved module/route structure.
15. Document architecture decisions.

---

# 36. Development Checkpoints

Every major task SHOULD follow:

## Checkpoint 1 — Plan

Before coding, provide:

- files to create;
- files to modify;
- routes involved;
- database changes;
- business rules;
- assumptions;
- dependencies;
- tests;
- risks.

## Checkpoint 2 — Implementation

Implement only the approved scope.

## Checkpoint 3 — Verification

Report:

```text
Typecheck: PASS / FAIL
Lint: PASS / FAIL
Unit Tests: PASS / FAIL
E2E Tests: PASS / FAIL
```

Also report:

- routes tested;
- acceptance criteria;
- known limitations;
- screenshots for major UI work where practical.

---

# 37. Implementation Sequence

```text
1. Project Bootstrap
2. Base Layout / Design System
3. Authentication
4. Exporter Registration
5. FAMA Registration
6. Exporter Dashboard
7. Company Profile
8. Agricultural Produce
9. Certificates
10. Gallery
11. Export Application
12. QR Generation
13. FAMA Dashboard
14. Application Review
15. Approval / Rejection
16. QR Activation
17. Public QR Traceability
18. QR Download / Print
19. Audit Trail
20. Seed / Demo Data
21. End-to-End Demo Validation
```

---

# 38. Acceptance Criteria

## 38.1 Export Application

- [ ] Exporter can create a new application.
- [ ] Exporter can select agricultural produce.
- [ ] Exporter can enter variety.
- [ ] Exporter can enter grade.
- [ ] Exporter can enter size.
- [ ] Exporter can enter quantity / weight.
- [ ] Exporter can enter destination.
- [ ] Exporter can enter/select CoC information.
- [ ] Exporter can enter farm information.
- [ ] Exporter can enter importer information.
- [ ] Application can be saved as draft.
- [ ] Draft can be edited.
- [ ] Application can be submitted.
- [ ] Submitted status is persisted.
- [ ] Audit entry is created.
- [ ] Validation errors are shown.

## 38.2 FAMA Approval

- [ ] FAMA can view pending applications.
- [ ] FAMA can open application detail.
- [ ] FAMA can review company information.
- [ ] FAMA can review export details.
- [ ] FAMA can review certificates.
- [ ] FAMA can approve.
- [ ] FAMA can reject.
- [ ] Approval/rejection is recorded.
- [ ] Audit entry is created.
- [ ] Exporter can see resulting status.

## 38.3 QR

- [ ] System generates a unique QR ID.
- [ ] QR resolves to a public route.
- [ ] Inactive QR shows inactive public state.
- [ ] Approved application can activate QR.
- [ ] Active QR shows traceability data.
- [ ] QR can be downloaded in at least one format.
- [ ] QR status is shown in exporter UI.

## 38.4 Public Traceability

- [ ] No login is required.
- [ ] Invalid QR shows a safe error state.
- [ ] Inactive QR shows inactive state.
- [ ] Active QR shows product details.
- [ ] Active QR shows exporter details.
- [ ] Active QR shows export details.
- [ ] Active QR shows certification where available.
- [ ] Main public view is mobile-friendly.

---

# 39. Non-Functional Prototype Requirements

## Maintainability

- TypeScript SHALL be used.
- Business rules SHOULD be separated from presentation.
- Status values SHALL be centralized.
- Integration logic SHALL be behind interfaces.

## Usability

- Main flows SHALL work on mobile.
- Statuses SHALL be clearly distinguishable.
- Actions SHALL provide feedback.
- Forms SHALL provide validation errors.

## Security Baseline

For prototype only:

- passwords SHALL NOT be stored in plaintext;
- standard secure framework authentication SHALL be used;
- role-protected routes SHALL enforce actor access;
- public QR SHALL expose only intended public data;
- file uploads SHOULD validate type/size.

This does not replace a future production security assessment.

---

# 40. Architecture Decisions

Record at least:

### ADR-001 — Monolithic Prototype
Use a single web application for V1.

### ADR-002 — Mock External Integrations
DagangNet and iFAMA use provider interfaces with mock implementations.

### ADR-003 — Separate Application and QR Status
Application lifecycle and QR lifecycle are independent.

### ADR-004 — QR Activation
Prototype assumption: QR becomes active only after FAMA approval.

### ADR-005 — Seed-Based Demo
Demo data is created through database seed scripts.

---

# 41. Open Questions / Stakeholder Confirmation

## QR

- Is one QR generated per shipment, batch, pallet, box, package or individual item?
- Can one application generate multiple QRs?
- Can an active QR be revoked?
- Can an active QR expire?
- Can a QR be reactivated?
- Is QR approval identical to application approval?
- Who is authorized to activate QR?
- Is physical QR serialisation required?
- Is an official FAMA label layout mandatory?

## Application

- Can a rejected application be edited?
- Can it be resubmitted?
- Are rejection remarks mandatory?
- Can FAMA return an application for correction?
- Is multi-level approval required?
- Is supervisor approval required?
- Are approval stamps/signatures required?

## Company

- Which fields come from DagangNet?
- Which fields may exporter edit?
- Which fields may FAMA edit?
- What makes a company active?
- Can a company have multiple user accounts?

## FAMA Staff

- What fields come from iFAMA?
- Are roles maintained in iFAMA or Jejak GPL?
- Are approvals based on job position, department or role assignment?

## Certificates

- Are certificates uploaded manually?
- Are certificates integrated?
- Is expiry date required?
- What happens when a certificate expires?
- Does FAMA verify certificate validity?
- Is CoC mandatory per export?

## Agricultural Produce

- Who maintains produce master data?
- Who maintains variety master data?
- Who maintains grade master data?
- Is size standardized by commodity?
- Who owns nutritional information?

## Farm

- Is farm a reusable master?
- Does farm require registration?
- Is GPS/location required?
- Is farm data integrated from another system?

## Importer

- Is importer reusable master data?
- Is importer entered for every application?
- Is destination validated against a master list?

## Public Information

- Which fields may be publicly exposed?
- Should importer full address be public?
- Should exporter full address be public?
- Should certificate images be public?
- Should the public page support BM / English / Chinese?

---

# 42. Prototype Demo Scenario

The final demo SHOULD support this complete scenario:

```text
1. Exporter registers using mock DagangNet identifier.
2. System retrieves ABC Fruits Sdn. Bhd.
3. Exporter completes company profile.
4. Exporter adds Durian.
5. Exporter adds MyGAP / HACCP / CoC.
6. Exporter creates Durian export application.
7. Exporter selects Musang King.
8. Exporter enters Premium grade.
9. Exporter enters quantity, destination, farm and importer.
10. System generates inactive QR.
11. Exporter submits application.
12. FAMA officer logs in.
13. FAMA sees pending application.
14. FAMA reviews information.
15. FAMA approves application.
16. QR becomes ACTIVE.
17. Exporter downloads QR.
18. Public user opens QR URL.
19. Public user sees verified traceability information.
20. Audit trail shows the end-to-end history.
```

A second demo SHOULD show rejection and inactive QR behavior.

---

# 43. Definition of Done — Prototype V1

- [ ] Exporter registration works.
- [ ] FAMA registration works.
- [ ] Mock integrations work.
- [ ] Exporter dashboard works.
- [ ] FAMA dashboard works.
- [ ] Company profile works.
- [ ] Agricultural produce works.
- [ ] Certificates work.
- [ ] Gallery works.
- [ ] Export application can be created.
- [ ] Application can be submitted.
- [ ] FAMA can approve/reject.
- [ ] QR can be generated.
- [ ] QR can be activated.
- [ ] QR can be downloaded/printed.
- [ ] Public traceability works.
- [ ] Active and inactive QR states are demonstrated.
- [ ] Audit trail exists.
- [ ] Realistic seed data exists.
- [ ] Main mobile and desktop flows are tested.
- [ ] Lint passes.
- [ ] Typecheck passes.
- [ ] Critical E2E demo flow passes.
- [ ] Open business questions remain documented.

---

# 44. First Coding Agent Instruction

```text
We are starting a new application called Sistem Jejak GPL.

The supplied Wireframe-Skrin Flow.pdf and this requirements document
are the primary functional references.

Do NOT start implementing business screens yet.

Your first task is to bootstrap the repository and establish the
development foundation.

Create:

1. Next.js TypeScript application
2. Tailwind configuration
3. PostgreSQL Docker Compose environment
4. Prisma setup
5. .env.example
6. README.md
7. AGENTS.md
8. docs/ structure
9. initial domain model proposal
10. route/screen mapping
11. status model
12. seed-data strategy
13. Playwright test setup
14. lint/typecheck scripts

Rules:

- Do not invent business requirements.
- Put unclear requirements in docs/10-open-questions.md.
- Do not integrate directly with real DagangNet or iFAMA.
- Use provider/adapter interfaces for external systems.
- Do not hardcode demo data in React components.
- Use database seed data.
- Do not introduce microservices.
- Do not implement production infrastructure.

Before making changes, provide a proposed implementation plan:

- architecture
- directory structure
- dependencies
- database entities
- routes
- implementation sequence
- assumptions
- risks
- test strategy

Wait for approval before implementation.
```

---

# 45. Guiding Principle

The prototype should answer:

> **Does this proposed business flow make sense to FAMA and its stakeholders?**

It should not attempt to answer:

> **Is this already the final production architecture?**

The prototype must therefore remain:

- realistic enough for stakeholder validation;
- structured enough to evolve;
- simple enough to change;
- disciplined enough that assumptions remain visible.

Anything not clearly supported by the wireframe or an approved stakeholder decision SHALL remain an explicit assumption or open question.
