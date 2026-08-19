# 08 — Test Scenarios

## Scenario 1 — Successful exporter registration

1. Open exporter registration.
2. Enter seeded valid company identifier.
3. Company is found through mock DagangNet.
4. Company details are displayed.
5. Enter user details.
6. Set password.
7. Account is created.
8. Login succeeds.

Expected: PASS.

## Scenario 2 — Unknown exporter

1. Enter unknown identifier.
2. Lookup completes.

Expected: `Tiada rekod dijumpai` or approved equivalent.

## Scenario 3 — FAMA registration

1. Enter seeded FAMA identity.
2. Mock iFAMA returns staff information.
3. Complete account setup.

Expected: account created with FAMA role.

## Scenario 4 — Create draft application

1. Login as exporter.
2. Create application.
3. Fill produce/export information.
4. Save.

Expected:

- status `DRAFT`;
- editable;
- persisted in DB.

## Scenario 5 — Generate inactive QR

1. Open eligible application.
2. Generate QR.

Expected:

- unique QR code;
- status `GENERATED_INACTIVE`;
- public route resolves;
- public route displays inactive state.

## Scenario 6 — Submit application

Expected:

- application moves out of DRAFT;
- submission timestamp is recorded;
- audit record exists.

## Scenario 7 — FAMA approval

1. Login as FAMA.
2. Open submitted application.
3. Review.
4. Approve.

Expected:

- application `APPROVED`;
- QR `ACTIVE`;
- approval record;
- audit record;
- exporter sees approved state.

## Scenario 8 — Public active QR

Open active QR public URL.

Expected: approved public traceability fields are shown.

## Scenario 9 — Rejection

FAMA rejects an application.

Expected:

- application `REJECTED`;
- QR remains inactive;
- audit record;
- exporter sees rejected state.

## Scenario 10 — Authorization

- exporter cannot access FAMA route;
- public cannot access authenticated application;
- exporter A cannot access exporter B private record.

## Scenario 11 — Visual sanity

For every route in `design/SCREEN_MAP.md` marked primary/final:

- render designated viewport;
- capture screenshot;
- compare hierarchy, spacing, navigation, status and major components.

## Minimum automated E2E path

```text
register/login exporter
→ create application
→ generate QR
→ submit
→ login FAMA
→ approve
→ open public active QR
```
