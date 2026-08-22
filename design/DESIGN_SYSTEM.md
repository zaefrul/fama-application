# Design System — Sistem Jejak GPL

This document describes the design language visible in the MockFlow prototype. It is not permission to invent a new design.

## 1. Visual direction

- FAMA / government-enterprise identity
- mobile-first
- clean white surfaces
- green as primary action/brand direction
- soft grey page background
- rounded cards
- restrained shadows/borders
- highly visible status states
- simple form hierarchy
- straightforward utilitarian navigation

## 2. Design-source policy

Exact brand colors, logo proportions and imagery SHOULD come from approved assets/reference screenshots.

Do not invent an exact brand hex value merely because the wireframe appears green.

Use semantic tokens in code:

```text
--brand-primary
--brand-primary-foreground
--surface
--surface-muted
--border
--text-primary
--text-secondary
--status-success
--status-warning
--status-danger
--status-info
```

Populate final exact values from approved design assets.

Charts on the FAMA dashboard MAY use a separate categorical palette (`--chart-1` … `--chart-10`). Those tokens are for data bars only. Do not reuse them as new brand or status colours on chrome, buttons or badges.

## 3. Status semantics

| State | Visual meaning |
|---|---|
| Active / Approved | Success / green |
| Pending / Belum Aktif / Review | Warning / amber-yellow |
| Rejected | Danger / red |
| Draft | Neutral/info |
| Inactive | Warning/neutral depending screen |

All pages must use one shared `StatusBadge` component.

## 4. Layout

### Mobile

The MockFlow is predominantly mobile.

Implementation baseline inferred from the design:

- responsive handset width;
- consistent horizontal content padding;
- cards stacked vertically;
- forms use full available width;
- actions remain thumb-friendly;
- bottom navigation where the selected final screen uses it.

### Desktop

Where desktop is shown:

- sidebar or left navigation;
- wider content canvas;
- cards grouped in columns;
- dashboards retain the same semantic hierarchy as mobile.

Do not merely stretch mobile cards to desktop width.

Public `/trace` at `lg` and above uses the Profil Produk Disahkan two-column profile (DD-012). The mobile pamphlet is unchanged.

## 5. Surfaces

Use:

- white cards;
- subtle border/shadow;
- consistent card radius;
- clear section separation.

Avoid:

- excessive gradients;
- glassmorphism;
- decorative animations;
- strong shadows;
- unrelated modern SaaS styling.

## 6. Typography

Use a readable sans-serif product font consistently.

Hierarchy:

```text
Page title
Section title
Card/KPI title
Body
Helper text
Caption
```

Do not use decorative display fonts.

## 7. Forms

- labels remain visible;
- required fields clearly identified;
- inputs align consistently;
- read-only externally sourced information looks read-only;
- validation appears near the relevant field;
- primary submit action uses consistent styling.

## 8. Buttons

Semantic variants:

```text
Primary
Secondary
Danger
Ghost/Text
```

Do not create page-specific button styling unless the reference requires it.

## 9. Tables and lists

Desktop may use tables where appropriate. Mobile should use card/list representations where shown in the reference.

Status and primary actions must remain easy to scan.

## 10. QR presentation

QR must:

- have sufficient quiet space;
- be rendered sharply;
- not be placed on noisy backgrounds;
- have clear state text;
- retain a visible identifier where the design requires it.

## 11. Official government chrome

Authenticated, auth and public pages share one portal frame:

```text
Jalur Gemilang hairline
Laman Rasmi FAMA utility bar
Identity header / page canvas
Official footer
```

Do not remove this chrome to “simplify” a screen. Do not add SaaS decoration (glass, heavy gradients, motion) to make it more attractive.

Attractiveness comes from restraint: official identity, clear type, status colour used sparingly, and a verification banner on the public QR page.

The public `/trace` page (DD-010) may use a stronger FAMA-green identity header and a solid verification bar. Do not copy that intensity onto authenticated exporter/FAMA screens.

## 12. Responsive QA targets

```text
Mobile: 390 × 844
Desktop: 1440 × 900
```

Adjust only when a reference clearly requires another proportion.
