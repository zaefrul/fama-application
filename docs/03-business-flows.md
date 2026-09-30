# 03 — Business Flows

## Flow A — Usahawan onboarding

```text
Open Registration
→ Enter one or more usahawan/company identifiers
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

## Flow C — Usahawan profile setup

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
                  (product, farm, and certificates come from the root application)
                  → show this chunk and the ancestor chain back to the trunk
```

## Flow J — Lot breakdown

```text
Holder opens an ACTIVE lot they currently hold
→ remaining quantity > 0 and disposition is HOLDING?
   ├─ No → split and sale are blocked
   └─ Yes
      → split into chunks for registered companies
         → each chunk gets its own ACTIVE QR
         → parent remaining quantity decreases by the sum
      → or sell the whole remainder (this QR becomes SOLD, no child)
      → or sell part of the remainder (terminal SOLD child QR, no login)
```

Only the active company on the login may split or sell that lot. FAMA sees the tree on the application and does not split on behalf of a holder.

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
