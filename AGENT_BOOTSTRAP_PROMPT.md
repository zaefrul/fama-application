# First Coding Agent Prompt

Use this as the first prompt after placing this context pack and the MockFlow reference inside the repository.

---

We are starting a new prototype application named **Sistem Jejak GPL** for FAMA.

Read these files before doing anything:

```text
AGENTS.md
README.md
docs/FAMA_Jejak_GPL_Prototype_Requirements.md
docs/01-product-scope.md
docs/02-actors-and-roles.md
docs/03-business-flows.md
docs/04-domain-model.md
docs/05-status-model.md
docs/06-api-contracts.md
docs/07-integration-mocks.md
docs/08-test-scenarios.md
docs/09-open-questions.md
docs/10-architecture-decisions.md
design/DESIGN_SYSTEM.md
design/SCREEN_MAP.md
design/COMPONENTS.md
design/DESIGN_DECISIONS.md
design/ASSET_MANIFEST.md
design/VISUAL_QA.md
```

Also inspect:

```text
design/original/Wireframe-Skrin-Flow.pdf
```

Do **not** implement business screens yet.

Your first task is to propose the repository bootstrap and implementation foundation.

Preferred stack:

- Next.js
- TypeScript
- Tailwind CSS
- PostgreSQL
- Prisma
- Docker Compose
- Playwright

Prepare a Checkpoint #1 plan covering:

1. target architecture;
2. directory structure;
3. exact dependencies;
4. Prisma/domain model;
5. route structure;
6. authentication approach for prototype;
7. mock DagangNet provider;
8. mock iFAMA provider;
9. seed strategy;
10. design-token/component setup;
11. testing strategy;
12. implementation sequence;
13. risks;
14. unresolved assumptions.

Rules:

- Do not invent business requirements.
- Do not silently resolve open questions.
- Do not introduce microservices.
- Do not integrate real DagangNet or iFAMA.
- Do not hardcode demo data into React components.
- Application and QR statuses are separate.
- Preserve the documented FAMA design.
- For ambiguous design, report the ambiguity rather than redesigning.

Do not make implementation changes until the plan has been reviewed.
