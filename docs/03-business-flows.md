# 03 — Business Flows

## Flow A — Exporter onboarding

```text
Open Registration
→ Enter exporter/company identifier
→ Query Mock DagangNet
→ Record found?
   ├─ No → show "Tiada rekod dijumpai"
   └─ Yes
      → display company information
      → capture user identity/name
      → set password
      → create account
```

## Flow B — FAMA onboarding

```text
Open FAMA Registration
→ Enter identity / IC
→ Query Mock iFAMA
→ Record found?
   ├─ No → validation/error state
   └─ Yes
      → display staff details
      → set password
      → create account
```

## Flow C — Exporter profile setup

```text
Login
→ Company Profile
→ confirm/update allowed fields
→ add agricultural produce
→ add certificates
→ add gallery images
```

## Flow D — Export application

```text
New Application
→ select produce
→ enter variety
→ grade
→ size
→ quantity/weight
→ destination
→ CoC reference
→ export date
→ farm
→ importer
→ importer address
→ save DRAFT
```

## Flow E — QR generation

```text
Draft/application information available
→ generate unique QR ID
→ create QR URL
→ QR status = GENERATED_INACTIVE
→ public route already resolves
→ public route shows inactive state
```

## Flow F — Submission and FAMA review

```text
Exporter submits
→ application = SUBMITTED
→ FAMA review queue
→ officer opens application
→ application = UNDER_REVIEW
→ officer reviews details
→ Approve OR Reject
```

## Flow G — Approval

```text
APPROVED application
→ approval audit record
→ QR becomes ACTIVE
→ exporter sees approved/active state
→ QR is available for download/print
```

## Flow H — Rejection

```text
REJECTED application
→ rejection audit record
→ QR remains inactive
→ exporter sees rejected state
```

Behavior for editing/resubmitting rejected records is not yet confirmed.

## Flow I — Public traceability

```text
Scan/open QR URL
→ QR exists?
   ├─ No → invalid QR state
   └─ Yes
      → active?
         ├─ No → QR Belum Diaktifkan
         └─ Yes → display public traceability record
```

## Public traceability content suggested by wireframe

- agricultural product
- grade
- size
- weight/quantity
- export date
- exporter
- exporter address (subject to public-data confirmation)
- farm
- importer (subject to public-data confirmation)
- importer address (subject to public-data confirmation)
- CoC
- HACCP / MyGAP / Fitosanitasi
- nutrition where available
