import type { ApplicationStatus, QrStatus } from "./status";

export type Role = "EXPORTER" | "FAMA_OFFICER";

export type UserStatus = "ACTIVE" | "PENDING";

export interface User {
  id: string;
  name: string;
  email: string;
  password: string;
  role: Role;
  identityReference: string;
  status: UserStatus;
  companyId: string | null;
  createdAt: string;
}

export interface Company {
  id: string;
  registrationNo: string;
  externalAccountNo: string;
  name: string;
  email: string;
  phone: string;
  address: string;
  state: string;
  district: string;
  postcode: string;
  website: string;
  logoPath: string | null;
  externalSource: "DAGANGNET" | "FAMA";
  externalStatus: "Aktif" | "Tidak Aktif";
  createdAt: string;
}

export interface ProduceType {
  id: string;
  name: string;
}

export interface CompanyProduce {
  id: string;
  companyId: string;
  produceTypeId: string;
  variety: string | null;
  active: boolean;
}

export type CertificateType = "HACCP" | "MyGAP" | "CoC" | "FITOSANITASI" | "ISO_22000";

export interface Certificate {
  id: string;
  companyId: string;
  type: CertificateType;
  certificateNo: string;
  documentPath: string;
  issueDate: string;
  expiryDate: string | null;
  status: "ACTIVE" | "EXPIRED";
}

export type GalleryCategory = "KEBUN" | "LOT_KEBUN" | "BUAH";

export interface GalleryItem {
  id: string;
  companyId: string;
  category: GalleryCategory;
  description: string;
  filePath: string;
  uploadedBy: string;
  uploadedAt: string;
}

export interface ExportApplication {
  id: string;
  applicationNo: string;
  companyId: string;
  produceTypeId: string;
  variety: string;
  grade: string;
  size: string;
  quantity: number;
  quantityUnit: string;
  destinationCountry: string;
  cocCertificateId: string | null;
  cocNumber: string;
  exportDate: string;
  farmName: string;
  importerName: string;
  importerAddress: string;
  status: ApplicationStatus;
  submittedAt: string | null;
  reviewedAt: string | null;
  createdAt: string;
  updatedAt: string;
}

export interface QrCodeRecord {
  id: string;
  qrCode: string;
  applicationId: string;
  publicSlug: string;
  status: QrStatus;
  generatedAt: string;
  activatedAt: string | null;
}

export interface Approval {
  id: string;
  applicationId: string;
  officerUserId: string;
  decision: "APPROVED" | "REJECTED";
  remarks: string;
  decidedAt: string;
}

export interface AuditLog {
  id: string;
  actorUserId: string | null;
  actorRole: Role | "PUBLIC" | "SYSTEM";
  action: string;
  objectType: string;
  objectId: string;
  beforeJson: string | null;
  afterJson: string | null;
  remarks: string | null;
  createdAt: string;
}

export interface Notification {
  id: string;
  userId: string;
  title: string;
  body: string;
  read: boolean;
  createdAt: string;
}

export interface NutritionFact {
  name: string;
  amount: string;
  dailyPercent: string;
}

export interface SessionUser {
  id: string;
  name: string;
  email: string;
  role: Role;
  companyId: string | null;
}
