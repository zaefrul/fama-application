# Design Decisions

## DD-001 — Later MockFlow generation is primary

**Decision:** Prefer pages 25–28 and detailed pages 18–21 over earlier variants.

**Reason:** They represent the more structured/latest workflow in the exported file.

## DD-002 — Banking examples are excluded

**Decision:** PDF pages 13 and 29 are not Jejak GPL functional designs.

**Reason:** They contain generic banking/financial content.

## DD-003 — Latest exporter navigation

**Decision:** Use the later mobile navigation direction:

- Utama
- Permohonan
- Kod QR
- Profil

unless changed by stakeholder decision.

## DD-004 — Shared status system

**Decision:** All status visuals use one `StatusBadge` design system.

## DD-005 — Responsive web implementation

**Decision:** Build responsive web UI rather than separate mobile/native implementations.

The MockFlow handset frames are the mobile UI reference.

## DD-006 — Do not invent exact colors from screenshots

**Decision:** Use semantic tokens and populate exact values from approved assets when extracted.

**Reason:** Screenshot/PDF appearance is not a reliable specification of exact brand hex values.

## DD-007 — Preserve information hierarchy

Where a reference is not pixel-specification quality, match in this priority:

1. page structure;
2. navigation;
3. card grouping;
4. action hierarchy;
5. status semantics;
6. typography hierarchy;
7. relative spacing;
8. cosmetic micro-adjustments.

## DD-008 — Desktop derivation

Where only mobile exists, desktop MAY adapt the same hierarchy into sidebar/grid layout.

Do not invent new business functions merely to fill desktop space.
