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

## DD-009 — Government portal chrome

**Decision:** Wrap existing MockFlow screens in a shared official FAMA/government shell:

- Jalur Gemilang hairline (national colours, not a new FAMA brand);
- “Laman Rasmi FAMA” masthead with Kementerian Pertanian dan Keterjaminan Makanan;
- identity header (FAMA logo + Sistem Jejak GPL);
- official footer;
- SVG icons instead of emoji;
- “Disahkan oleh FAMA” banner on active public QR pages;
- KPI cards use a left status accent on a white surface.

**Reason:** The approved interiors stay intact. The chrome is what makes Malaysian government sites feel official without introducing SaaS styling.

**Assumption:** Uses the existing approved FAMA PNG and current semantic tokens. Exact logo pack and brand hex values remain open questions in `docs/09-open-questions.md`. Authenticated UI stays Bahasa Melayu (ADR-013); no language switcher is added there. The public QR page may show cropped Jata Negara / KPKM / FAMA marks from stakeholder-supplied lockups in a separate card; that does not approve a final logo pack.

## DD-010 — Public QR page is a consumer trust surface

**Decision:** The public `/trace` page may use a stronger FAMA-green treatment than authenticated screens: large official FAMA mark, dark identity header, solid green “Produk Disahkan Tulen” bar, product photo, dark section headers, and a certificate/pamphlet cover so buyers feel the record is official.

**Reason:** Customer feedback that the public scan page was too bland. The consumer reference is used for visual hierarchy only.

**Do not copy from the reference:** MAPC branding, “Jejak Balik Durian” product name, collection/packaging centres, journey events, or scan locations. Those are not Jejak GPL data.

**Assumption:** Existing public fields stay the same. Gallery image is shown when the company has one; otherwise the approved buah placeholder is used. Scan count is page views (ADR-016), without location.

## DD-011 — FAMA dashboard operational charts may use a categorical palette

**Decision:** The FAMA Utama dashboard may show extra operational aggregates and use a 10-colour chart palette. Layout chrome and primary actions stay FAMA green. Chart colours are data tokens, not new brand colours.

Added tiles (aggregates of existing records only):

- Permohonan QR — all export applications, including draf;
- Pengeksport — users with role `EXPORTER` (not the same as syarikat);
- Buah unik — distinct produce types registered on any company;
- Destinasi — distinct destination countries on applications;
- 10 buah paling kerap — produce types ranked by application count;
- Destinasi eksport and syarikat mengikut negeri — ranked counts;
- Status permohonan — mix of canonical application statuses.

QR Aktif / Belum Aktif daily bars still use success/warning because those are status colours.

**Reason:** Stakeholders asked for QR request, exporter, unique fruit and top-fruit monitoring. Status colours (5) are not enough for a top-10 categorical chart.

**Assumption:** Does not change the definition of an active company (`external_status = Aktif`). Does not resolve unique-visitor counting. Daily QR generated uses real `generated_at` in Asia/Kuala_Lumpur for the last 7 calendar days, replacing the earlier placeholder weekday numbers.
