# 09 — Open Questions

This file is intentionally authoritative. The agent MUST NOT silently choose answers for these items.

## QR

- Is one QR generated per shipment, batch, pallet, box, package or individual product?
- Can one application generate multiple QRs?
- Can an active QR be revoked?
- Can a QR expire?
- Can a QR be reactivated?
- Is QR approval exactly the same as application approval?
- Who is officially authorized to activate QR?
- Is serialized physical label management required?
- Is there an official mandatory FAMA label design?

## Application

- Can rejected applications be edited?
- Can rejected applications be resubmitted?
- Are rejection remarks mandatory?
- Is "return for correction" required?
- Is multi-level approval required?
- Is supervisor approval required?
- Is a digital/signature stamp required?

## DagangNet / Company

- What is the exact lookup identifier?
- Which company fields are returned?
- Which fields are authoritative/read-only?
- Which fields can exporter edit?
- What defines an active company?
- Can one company have multiple users?
- Can a FAMA-created company later bind to DagangNet and/or an exporter login?

## iFAMA

- What exact identifier is used?
- Which staff fields are provided?
- Does iFAMA provide roles?
- Are Jejak GPL roles maintained internally?

## Certificates

- Are certificates uploaded manually or integrated?
- Is expiry date required?
- What happens on expiry?
- Does FAMA validate certificate authenticity?
- Is CoC mandatory per application?

## Produce

- Who maintains produce master data? *(Prototype assumption ADR-019: usahawan and FAMA officers may add a missing type from the keluaran/application form. Official ownership is not decided.)*
- Who maintains variety?
- Who maintains grade?
- Are sizes standardized?
- Where does nutrition data come from?

## Farm

- Is Farm reusable master data?
- Does it have registration ID?
- Is geolocation required? *(Prototype assumption ADR-018: optional lat/lng on the application; not a Farm master. SA asked for an interactive map on the public QR.)*
- Is it integrated from another FAMA source?

## Importer

- Is importer reusable master data?
- Is importer entered for every application?
- Is destination country from a controlled list?

## Public page

- Which fields may legally/commercially be public?
- Should exporter full address be public?
- Should importer full address be public?
- Should certificate documents/images be public?
- Is multilingual BM / English / Chinese required?
- Should QR access statistics count unique visitors or page views?
- May IP address or user-agent be stored for scan analytics?
- Should usahawan see their own QR access counts?
- May farm location / lot number be public? *(Prototype assumption: yes on `/trace` because SA asked to display them.)*
- Must FAMA approve the usahawan display photo before it appears on the public QR?
- Official Hubungi Kami details beyond `https://www.fama.gov.my`?

## Design

- Which exact FAMA/Jejak GPL logo asset pack is approved?
- Are current MockFlow colors final or conceptual?
- Which desktop screens are authoritative where only mobile is shown?
