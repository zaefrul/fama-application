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

Stakeholder supplied combined FAMA + KPKM lockups (black/transparent, P3). Cropped for a public QR agency card only. This does not close the open question on the final approved logo pack.

```text
File: public/logos/LOGO FAMA-MAFS IN MALAY P3-P1-01.png
      public/logos/LOGO FAMA-MAFS IN MALAY N ENGLSIH P3-P1-01-01.png
      public/logos/LOGO FAMA-MAFS IN MALAY P3-P2-01-01.png
      public/logos/LOGO FAMA-MAFI IN MALAY N ENGLSIH P3-P2-01-01.png
Source: stakeholder-supplied official lockup
Owner: FAMA / KPKM
License/permission: supplied project asset
Used for: source of cropped agency marks
Final or placeholder: supplied original
```

```text
File: public/logos/logo-jata-negara.png
      public/logos/logo-kpkm-ms.png
      public/logos/logo-kpkm-en.png
      public/logos/logo-kpkm-ms-light.png
      public/logos/logo-kpkm-en-light.png
      public/logos/logo-fama-lockup.png
      design/assets/logos/ (copies)
Source: cropped from the supplied P3 lockups above
Owner: FAMA / KPKM
License/permission: supplied project asset
Used for: public QR “Agensi Kerajaan” card
Final or placeholder: cropped display extract; *-light variants recolor white lockup text to dark ink for the cream pamphlet card
```

### Product/agricultural imagery

Status: **Prototype demo photos added from Wikimedia Commons**

```text
File: design/assets/products/produce-durian-demo-01.jpg
      public/products/produce-durian-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Buka_buah_durian.jpg
Owner: Astari28
License/permission: CC BY-SA 4.0
Used for: public QR hero / gallery BUAH (Durian)
Final or placeholder: licensed demo photo
```

```text
File: design/assets/products/produce-tembikai-demo-01.jpg
      public/products/produce-tembikai-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Watermelon_sliced.jpg
Owner: Irvin calicut
License/permission: CC BY-SA 3.0
Used for: public QR hero (Tembikai)
Final or placeholder: licensed demo photo
```

```text
File: design/assets/products/produce-mangga-demo-01.jpg
      public/products/produce-mangga-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Mango_-_single.jpg
Owner: Ivar Leidus
License/permission: CC BY-SA 4.0
Used for: public QR hero (Mangga)
Final or placeholder: licensed demo photo
```

```text
File: design/assets/products/produce-nangka-demo-01.jpg
      public/products/produce-nangka-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Jackfruit_(Artocarpus_heterophyllus)_-_photo_of_the_inside.jpg
Owner: Susan Slater
License/permission: CC BY-SA 4.0
Used for: public QR hero (Nangka)
Final or placeholder: licensed demo photo
```

```text
File: design/assets/farms/farm-demo-01.jpg
      public/farms/farm-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Durian_tree_in_malaysia.jpg
Owner: Yun Huang Yong
License/permission: CC BY-SA 2.0
Used for: gallery KEBUN demo
Final or placeholder: licensed demo photo
```

```text
File: design/assets/farms/farm-lot-demo-01.jpg
      public/farms/farm-lot-demo-01.jpg
Source: https://commons.wikimedia.org/wiki/File:Mango_orchard_in_Lucknow.jpg
Owner: Wikimedia Commons contributor
License/permission: CC BY-SA 4.0
Used for: gallery LOT_KEBUN demo
Final or placeholder: licensed demo photo
```

These are prototype demonstration photos, not FAMA-owned product photography.

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

Status: **Demo samples for public QR. Marked CONTOH — not official verification of the demo companies.**

Typical exporter show-off set used in the prototype: myGAP, HACCP, Halal, Fitosanitasi, ISO 22000, CoC.

```text
File: design/assets/certificates/sijil-fitosanitasi-demo.jpg
      public/certificates/sijil-fitosanitasi-demo.jpg
Source: https://commons.wikimedia.org/wiki/File:Phytosanitary_certificate_(27169903590).jpg
Owner: Sasha India (Flickr)
License/permission: CC BY 2.0
Used for: public QR Fitosanitasi thumbnail (live phytosanitary example)
Final or placeholder: licensed live example, overlay CONTOH
```

```text
File: public/certificates/sijil-mygap-demo.svg
      public/certificates/sijil-haccp-demo.svg
      public/certificates/sijil-halal-demo.svg
      public/certificates/sijil-iso22000-demo.svg
      public/certificates/sijil-coc-demo.svg
      design/assets/certificates/ (copies)
Source: generated prototype samples
Owner: project
License/permission: internal prototype
Used for: public QR / exporter sijil grid
Final or placeholder: clearly marked CONTOH SIJIL
```

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
