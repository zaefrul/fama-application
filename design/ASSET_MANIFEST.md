# Asset Manifest

This file tracks visual resources required by the implementation.

The developer/agent must not silently substitute random internet assets.

## Required asset folders

```text
design/assets/
├── logos/
├── icons/
├── products/
├── farms/
├── certificates/
├── qr/
└── misc/
```

Production-served copies may later be placed under `public/`.

## Assets observed in MockFlow

### Branding

- FAMA logo
- Sistem Jejak GPL / Jejak GPL identity/logo

Status: **Partial — FAMA logo copied from local approved PNG**

```text
File: design/assets/logos/logo-fama.png
Source: /Users/zaefrul/Downloads/fama-logo-hd-png.png
Owner: FAMA
License/permission: supplied project asset
Used for: header / login branding
Final or placeholder: approved original PNG
```

Jejak GPL wordmark is composed from this FAMA logo plus text. No separate official Jejak GPL SVG was supplied.

### Product/agricultural imagery

Examples visible:

- farm/orchard
- fruit/product imagery
- packing/process imagery
- storage/shipping imagery

Status: **Prototype image resources required**

### Gallery categories

- Kebun
- Lot Kebun
- Buah

Status: **Placeholder SVGs in public/placeholders/**

```text
File: public/placeholders/gallery-kebun.svg
Source: generated placeholder
Owner: project
License/permission: internal prototype
Used for: gallery demo
Final or placeholder: placeholder
```

### Certificate examples

- HACCP
- MyGAP
- CoC
- Fitosanitasi
- ISO 22000 appears in an earlier concept

Status: **Placeholder SVGs clearly marked as not official certificates**

Do not present a sample certificate as genuine production verification.

### QR labels

The wireframe contains generic QR graphics, proposed FAMA/export label examples and a physical-label photo.

Status: **Use generated QR for functionality; retain label artwork only as design reference unless approved.**

## Recommended asset names

```text
logo-fama.svg
logo-jejak-gpl.svg
farm-demo-01.jpg
farm-lot-demo-01.jpg
produce-durian-demo-01.jpg
certificate-haccp-demo.png
certificate-mygap-demo.png
certificate-coc-demo.png
certificate-fitosanitasi-demo.png
```

## Provenance fields

For every non-generated asset, record:

```text
File:
Source:
Owner:
License/permission:
Used for:
Final or placeholder:
```

## Missing asset behavior

If an approved original is unavailable:

- use a clearly identified placeholder;
- do not fabricate an official logo/certificate;
- report the missing asset in the task result.
