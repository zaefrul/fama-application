import type {
  Approval,
  AuditLog,
  Certificate,
  Company,
  CompanyProduce,
  ExportApplication,
  GalleryItem,
  Notification,
  NutritionFact,
  ProduceType,
  QrCodeRecord,
  Role,
  SessionUser,
  User,
} from "@/domain/types";

export interface CompanyRegistryResult {
  identifier: string;
  registrationNo: string;
  name: string;
  email: string;
  status: "Aktif" | "Tidak Aktif";
  address: string;
  state: string;
  district: string;
  postcode: string;
  phone: string;
}

export interface StaffDirectoryResult {
  identifier: string;
  fullName: string;
  email: string;
  position: string;
  active: boolean;
}

export interface CompanyRegistryProvider {
  findCompany(identifier: string): Promise<CompanyRegistryResult | null>;
}

export interface StaffDirectoryProvider {
  findStaff(identifier: string): Promise<StaffDirectoryResult | null>;
}

export interface ApplicationInput {
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
}

export interface CreateCompanyInput {
  name: string;
  registrationNo: string;
  email: string;
  phone: string;
  address: string;
  state: string;
  district: string;
  postcode: string;
  website: string;
}

export type ManagedCompanyPatch = Partial<
  Pick<
    Company,
    "name" | "registrationNo" | "address" | "state" | "district" | "postcode" | "phone" | "email" | "website" | "logoPath"
  >
>;

export interface Repositories {
  findUserByEmail(email: string): Promise<User | null>;
  findUserById(id: string): Promise<User | null>;
  createExporterUser(input: {
    name: string;
    email: string;
    password: string;
    identityReference: string;
    companyId: string;
  }): Promise<User>;
  createFamaUser(input: {
    name: string;
    email: string;
    password: string;
    identityReference: string;
  }): Promise<User>;

  listCompanies(): Promise<Company[]>;
  getCompany(id: string): Promise<Company | null>;
  createCompany(input: CreateCompanyInput, actor: SessionUser): Promise<Company>;
  updateCompany(id: string, patch: Partial<Company>): Promise<Company>;
  updateManagedCompany(id: string, patch: ManagedCompanyPatch, actor: SessionUser): Promise<Company>;

  listProduceTypes(): Promise<ProduceType[]>;
  listCompanyProduce(companyId: string): Promise<CompanyProduce[]>;
  addCompanyProduce(companyId: string, produceTypeId: string): Promise<CompanyProduce>;
  removeCompanyProduce(id: string): Promise<void>;

  listCertificates(companyId: string): Promise<Certificate[]>;
  addCertificate(input: Omit<Certificate, "id">): Promise<Certificate>;
  deleteCertificate(id: string): Promise<void>;
  listGallery(companyId: string): Promise<GalleryItem[]>;
  addGalleryItem(input: Omit<GalleryItem, "id">): Promise<GalleryItem>;
  updateGalleryItem(id: string, patch: Partial<GalleryItem>): Promise<GalleryItem>;
  deleteGalleryItem(id: string): Promise<void>;

  listApplications(filter?: {
    companyId?: string;
    status?: ExportApplication["status"];
  }): Promise<ExportApplication[]>;
  getApplication(id: string): Promise<ExportApplication | null>;
  createApplication(input: ApplicationInput): Promise<ExportApplication>;
  createAndActivateQr(
    companyId: string,
    input: ApplicationInput,
    actor: SessionUser,
  ): Promise<{ application: ExportApplication; qr: QrCodeRecord }>;
  updateApplication(id: string, patch: Partial<ApplicationInput>): Promise<ExportApplication>;
  updateManagedApplication(
    id: string,
    patch: Partial<ApplicationInput>,
    actor: SessionUser,
  ): Promise<ExportApplication>;
  submitApplication(id: string, actor: SessionUser): Promise<ExportApplication>;
  startReview(id: string, actor: SessionUser): Promise<ExportApplication>;
  approveApplication(id: string, actor: SessionUser, remarks: string): Promise<ExportApplication>;
  rejectApplication(id: string, actor: SessionUser, remarks: string): Promise<ExportApplication>;

  listQrCodes(filter?: { companyId?: string }): Promise<QrCodeRecord[]>;
  getQrById(id: string): Promise<QrCodeRecord | null>;
  getQrByCode(qrCode: string): Promise<QrCodeRecord | null>;
  generateQr(applicationId: string, actor: SessionUser): Promise<QrCodeRecord>;

  listApprovals(applicationId: string): Promise<Approval[]>;
  listAudit(filter?: { companyId?: string }): Promise<AuditLog[]>;
  listNotifications(userId: string): Promise<Notification[]>;
  getNutrition(produceTypeId: string): Promise<NutritionFact[]>;

  dashboardExporter(companyId: string): Promise<{
    qrActive: number;
    qrInactive: number;
    totalApplications: number;
    approved: number;
    rejected: number;
  }>;
  dashboardFama(): Promise<{
    activeCompanies: number;
    qrActive: number;
    approved: number;
    pending: number;
    rejected: number;
    dailyQr: { day: string; active: number; inactive: number }[];
  }>;
}

export interface AuthSessionStore {
  getSession(): Promise<SessionUser | null>;
  requireRole(role: Role): Promise<SessionUser>;
}
