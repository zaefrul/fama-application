# AGENTS.md — Sistem Jejak GPL

These instructions apply to every coding agent working in this repository.

## 1. Project goal

Build a functional prototype of **Sistem Jejak GPL** based on the supplied FAMA MockFlow wireframe and approved Markdown requirements.

The prototype demonstrates:

- Pengeksport onboarding;
- Pegawai FAMA onboarding;
- company and produce information;
- certification and gallery;
- export application;
- QR generation;
- FAMA review and approval;
- QR activation;
- QR download/print;
- public product traceability;
- dashboards;
- audit trail.

## 2. Mandatory sources

Before implementing a feature, read the relevant files:

```text
docs/FAMA_Jejak_GPL_Prototype_Requirements.md
docs/01-product-scope.md
docs/02-actors-and-roles.md
docs/03-business-flows.md
docs/04-domain-model.md
docs/05-status-model.md
docs/09-open-questions.md

design/DESIGN_SYSTEM.md
design/SCREEN_MAP.md
design/COMPONENTS.md
design/DESIGN_DECISIONS.md
design/ASSET_MANIFEST.md
```

For UI work, inspect the reference identified in `design/SCREEN_MAP.md`.

## 3. Requirement rules

- Do NOT invent business requirements.
- Do NOT silently resolve an item listed in `docs/09-open-questions.md`.
- If something is unclear, add it to `docs/09-open-questions.md`.
- If implementation requires a temporary assumption, state it explicitly before coding.
- Approved decisions must be recorded in `docs/10-architecture-decisions.md` or `design/DESIGN_DECISIONS.md`.
- Preserve FAMA / Malay terminology from the approved design where applicable.

## 4. Architecture rules

Prototype V1 uses a modular monolith.

Preferred stack:

```text
Next.js
TypeScript
Tailwind CSS
PostgreSQL
Prisma ORM
Docker Compose
Playwright
```

Do NOT introduce microservices, Kubernetes, production HA architecture, real DagangNet integration or real iFAMA integration unless scope is explicitly changed.

## 5. Business-logic rules

- Significant business logic must not live directly in presentation components.
- Application statuses must be centralized.
- QR statuses must be centralized.
- Status transitions must be validated.
- Application status and QR status are separate concepts.
- Approval and rejection must create audit entries.
- External integrations must use interfaces/providers/adapters.
- Demo records must come from seed data, not hardcoded JSX.

## 6. Design rules

The visual design is a requirement.

For each screen:

1. Find the route in `design/SCREEN_MAP.md`.
2. Inspect the designated MockFlow/reference image.
3. Use existing reusable components first.
4. Do not combine unrelated UI patterns from different MockFlow iterations.
5. Do not redesign the screen unless explicitly requested.
6. Do not introduce arbitrary brand colors, spacing, corner radii or typography.
7. Use semantic design tokens.
8. Preserve FAMA visual hierarchy.
9. Implement responsive behavior.
10. Perform screenshot verification before declaring UI complete.

If a current reference and an earlier concept conflict, follow the priority in `design/SCREEN_MAP.md`.

## 7. Asset rules

- Use local approved assets from `design/assets/` or `public/`.
- Do not use random web images as substitutes without approval.
- Do not redraw official logos unnecessarily.
- Maintain source/provenance in `design/ASSET_MANIFEST.md`.
- If an asset is missing, use a clearly marked placeholder and report it.

## 8. Coding rules

- TypeScript strict mode.
- Avoid `any` unless justified.
- Prefer small domain/service modules.
- Keep UI components reusable.
- Validate server-side input.
- Protect role-specific routes.
- Never store plaintext passwords.
- Do not expose private application data on the public QR route.
- Use database migrations.

## 9. Workflow

Every major task uses three checkpoints.

### Checkpoint 1 — Plan

Before implementation, report:

- requirement references;
- design reference;
- routes;
- files to create;
- files to modify;
- database changes;
- business rules;
- assumptions;
- test cases;
- risks.

Wait for approval when requested.

### Checkpoint 2 — Implement

Implement only the accepted scope.

### Checkpoint 3 — Verify

Report:

```text
Typecheck:
Lint:
Unit Tests:
E2E Tests:
```

Also report acceptance criteria, routes tested, visual screenshots checked, known limitations and unresolved questions.

## 10. Definition of done

A feature is NOT done merely because it compiles. It must satisfy acceptance criteria, respect state transitions, work with database/seed data, pass relevant checks, visually match the reference for UI work, and avoid undocumented business assumptions.
