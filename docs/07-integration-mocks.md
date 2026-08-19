# 07 — Integration Mocks

Prototype V1 simulates external systems.

## DagangNet

### Purpose

Validate/retrieve exporter company information during onboarding.

### Interface

```ts
interface CompanyRegistryProvider {
  findCompany(identifier: string): Promise<CompanyRegistryResult | null>;
}
```

### Prototype provider

```text
MockDagangNetProvider
```

Seed at least:

- one successful company lookup;
- one unknown identifier case;
- optionally one inactive company case if useful for demo.

Example company:

```text
ABC Fruits Sdn. Bhd.
```

Do not hardcode the response inside a page component.

## iFAMA

### Purpose

Retrieve FAMA staff information during registration.

### Interface

```ts
interface StaffDirectoryProvider {
  findStaff(identifier: string): Promise<StaffDirectoryResult | null>;
}
```

### Prototype provider

```text
MockIFAMAProvider
```

Seed at least:

- one valid officer;
- one unknown identifier.

Suggested return information:

- full name;
- email;
- position;
- active status.

## Adapter rule

Application code depends on interfaces, not mock classes.

Future replacement should conceptually be:

```text
MockDagangNetProvider → RealDagangNetProvider
MockIFAMAProvider     → RealIFAMAProvider
```

without redesigning the onboarding UI.

## Error states to demonstrate

- record found;
- record not found;
- provider unavailable (optional prototype state);
- inactive/invalid external status only if agreed as a requirement.
