# Jejak GPL — Product Passport Concept

Responsive design concept based on the public demonstration record:
https://jejakgpl.metadatasystem.my/trace/GPL-QR-000215

Open `index.html` in a browser. Fonts, images, icons and compiled Tailwind CSS are bundled; no installation is needed to view it. Use PC, Tablet or Phone in the preview toolbar.

## Responsive layout

| Container width | Layout |
| --- | --- |
| 900 px and above | Two-column product hero; main content with origin/export and QR sidebar |
| 600–899 px | Compact two-column hero; full-width content; paired supporting cards |
| Below 600 px | Image above product identity; vertical batch journey; stacked details; two-column document cards |

The preview toolbar constrains the same live layout to 834 px for tablet and 390 px for phone. It does not switch between separate page implementations. Container queries also let the design adapt when embedded in an existing application.

## Tailwind

Tailwind CSS 4.1.18 is precompiled into `assets/tailwind-compiled.css`. Brand colours, typography and base rules are in `tailwind-input.css`. Product-specific composition and container queries are in the `product-css` style in `index.html`.

To regenerate the utility stylesheet after changing classes:

```bash
npm install
npm run css
```

For integration, move the theme into your application stylesheet, copy the product markup and product CSS into the appropriate component, and remove the outer preview toolbar. Keep a container named `trace`, or translate the queries to your application's viewport breakpoints.

## Working interactions

- PC, tablet and phone preview controls
- Section anchors and local active navigation
- Share dialog with the original record QR and copy-link action
- Certificate information dialogs, retaining the source's CONTOH designation
- Nutrition accordion
- Links to the source's language variants, FAMA and the original OpenStreetMap location

## Content and asset notes

- The source distinguishes an 800 kg supplier record, GPL-QR-000213, and its 200 kg retail record, GPL-QR-000215. Both quantities remain separate in the redesign.
- Certificate examples are not presented as verified certificates for the batch. The record's CoC reference STB-CHAIN-DEMO differs from the gallery's example reference; the UI preserves that distinction.
- The hero fruit photograph is an AI-generated illustrative design asset, labelled as such. It is not evidence of this batch's appearance. Replace it with approved product photography before production use.
- FAMA and Malaysian government marks and the original QR were taken from the supplied public page solely for this design concept. Confirm brand usage for production.
- Nutrition values are reproduced from the supplied page without adding an unsupported serving basis.
- This concept is not connected to your production backend.

Image asset prompt, built-in Imagegen: premium editorial still life of opened Musang King durian with golden flesh and a whole green durian on pale limestone, warm ivory backdrop, natural morning light, no text or logos.
