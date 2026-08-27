# 04 — Domain Model

## Initial entities

```text
User
Role

Company
CompanyUser

ProduceType
CompanyProduce

Certificate
GalleryItem

ExportApplication
QRCode
Approval

AuditLog
Notification
QrAccess
```

## Relationship overview

```text
User
 ├── Role
 └── CompanyUser
       └── Company
            ├── CompanyProduce
            │    └── ProduceType
            ├── Certificate
            ├── GalleryItem
            └── ExportApplication
                  ├── QRCode
                  │    └── QrAccess
                  ├── Approval
                  └── AuditLog
```

## User — suggested fields

- id
- name
- email
- passwordHash
- role
- identityReference
- status
- createdAt
- updatedAt

## Company — suggested fields

- id
- registrationNo
- externalAccountNo
- name
- email
- phone
- address
- state
- district
- postcode
- website
- logoPath
- externalSource
- externalStatus
- createdAt
- updatedAt

## CompanyProduce — suggested fields

- id
- companyId
- produceTypeId
- variety (if company-level)
- active
- createdAt
- updatedAt

Do not prematurely force all export-specific fields onto `CompanyProduce`.

## Certificate — suggested fields

- id
- companyId
- type
- certificateNo
- documentPath
- issueDate
- expiryDate (nullable until confirmed)
- status
- createdAt
- updatedAt

## GalleryItem — suggested fields

- id
- companyId
- category
- description
- filePath
- uploadedBy
- uploadedAt

Observed categories:

- KEBUN
- LOT_KEBUN
- BUAH

## ExportApplication — suggested fields

- id
- applicationNo
- companyId
- produceTypeId
- variety
- grade
- size
- quantity
- quantityUnit
- destinationCountry
- cocCertificateId / cocNumber
- exportDate (nullable)
- lotNo
- farmLocation
- farmLat / farmLng
- displayImagePath
- farmName
- importerName
- importerAddress
- status
- submittedAt
- reviewedAt
- createdAt
- updatedAt

## QRCode — suggested fields

- id
- qrCode
- applicationId
- publicToken / publicSlug
- status
- generatedAt
- activatedAt
- createdAt
- updatedAt

## QrAccess — prototype fields (ADR-016)

- id
- qrId
- qrCode
- accessedAt

One row per public HTML view of a known QR. No visitor identity.

## Approval — suggested fields

- id
- applicationId
- officerUserId
- decision
- remarks
- decidedAt

## AuditLog — suggested fields

- id
- actorUserId
- actorRole
- action
- objectType
- objectId
- beforeJson
- afterJson
- remarks
- ipAddress
- userAgent
- createdAt

## Important modeling rule

Farm and Importer are not yet confirmed as reusable master entities. For prototype V1 they MAY stay as application-level data. Do not create full Farm/Importer master modules unless confirmed.
