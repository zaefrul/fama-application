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

## 11. Responsive QA targets

```text
Mobile: 390 × 844
Desktop: 1440 × 900
```

Adjust only when a reference clearly requires another proportion.
