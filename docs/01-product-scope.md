# 01 — Product Scope

## Product

**Sistem Jejak GPL**

## Prototype purpose

Demonstrate a FAMA agricultural-product traceability workflow in which an exporter provides product/export information, FAMA reviews it, a QR becomes active after the approved state, and a public user can view verified traceability information through the QR.

## V1 end-to-end outcome

```text
Usahawan registration
→ company lookup
→ company/produce/certificate setup
→ export application
→ QR generation
→ submit
→ FAMA review
→ approve/reject
→ QR activation
→ QR download/print
→ public traceability
```

## In scope

- Login
- Exporter registration
- FAMA registration
- Mock DagangNet lookup
- Mock iFAMA lookup
- Exporter dashboard
- FAMA dashboard
- Company profile
- Agricultural produce
- Certificates
- Gallery
- Export application
- QR generation
- FAMA review
- Approval / rejection
- QR activation
- QR list/detail
- QR download/print prototype
- Public active/inactive QR pages
- Audit trail
- Seed/demo data
- Basic notifications

## Not in V1

- Real DagangNet
- Real iFAMA
- Production SSO
- Microservices
- Kubernetes
- HA/DR
- Production SIEM
- Live certificate authority validation
- Native mobile app
- Hardware printer integration
- Production label manufacturing workflow

## Guiding question

Prototype V1 should answer:

> Does the proposed Jejak GPL business process make sense to FAMA and stakeholders?

It is not intended to prove final production infrastructure.
