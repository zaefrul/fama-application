# 02 — Actors and Roles

## PENGEKSPORT

External business user representing an exporter/company.

### Capabilities

- Register using mock company lookup
- Login
- View/update allowed company fields
- Maintain agricultural produce
- Maintain certificates
- Maintain gallery
- Create export application
- Save draft
- Generate inactive QR
- Submit application
- View status
- View rejection/approval
- Download/print QR
- View relevant audit history

### Restrictions

- Cannot approve own application
- Cannot directly force QR to active
- Cannot access another company's private application
- Cannot access FAMA-only screens

## PEGAWAI FAMA

Internal reviewing/monitoring user.

### Capabilities

- Register using mock iFAMA lookup
- Login
- View FAMA dashboard
- View companies
- View submitted applications
- Inspect export/product/company/certification data
- Approve
- Reject
- View QR status
- View audit history
- Temporary MAHA/ops extension (ADR-014): register a vendor as a company-only record (no exporter login)
- Temporary MAHA/ops extension (ADR-014): create and activate a QR in one flow
- Temporary MAHA/ops extension (ADR-014): edit FAMA-managed company fields and APPROVED public application fields
- Temporary MAHA/ops extension (ADR-014): add or remove certificates on a managed company
- Temporary MAHA/ops extension (ADR-014): download/print a QR

### Restrictions

- Exact authorization by job/department is not defined yet
- Must not alter exporter-authoritative data unless later confirmed
- DagangNet-sourced name and registration no. remain read-only
- Must not create an exporter login when registering a FAMA-managed vendor
- Must not change QR identity after activation

## PUBLIC / CONSUMER

Anonymous user who opens a QR traceability URL.

### Capabilities

- View active QR public information
- View inactive QR state
- View selected product/export/certificate information

### Restrictions

- No authenticated/private application details
- No editing
- No approval
- No private user/account information

## Potential future roles

The wireframe does not clearly establish these as V1 roles:

- System Administrator
- FAMA Supervisor
- Certificate Verifier
- State-level FAMA officer

Do not introduce these as business roles without confirmation.
