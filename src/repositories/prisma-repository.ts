import bcrypt from "bcryptjs";
import { assertApplicationTransition, assertQrTransition } from "@/domain/transitions";
import type {
  Certificate,
  Company,
  CompanyProduce,
  ExportApplication,
  GalleryItem,
  QrCodeRecord,
  SessionUser,
  User,
} from "@/domain/types";
import { createId, nextApplicationNo, nextQrCode } from "@/lib/ids";
import { getPrisma } from "@/lib/prisma";
import { nutritionByProduce } from "@/fixtures/store";
import type { Repositories } from "./contracts";

function iso(value: Date | null | undefined) {
  return value ? value.toISOString() : null;
}

function toUser(row: {
  id: string;
  name: string;
  email: string;
  passwordHash: string;
  role: User["role"];
  identityReference: string;
  status: string;
  companyId: string | null;
  createdAt: Date;
}): User {
  return {
    id: row.id,
    name: row.name,
    email: row.email,
    password: row.passwordHash,
    role: row.role,
    identityReference: row.identityReference,
    status: row.status as User["status"],
    companyId: row.companyId,
    createdAt: row.createdAt.toISOString(),
  };
}

function toCompany(row: {
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
  externalSource: string;
  externalStatus: string;
  createdAt: Date;
}): Company {
  return {
    ...row,
    externalSource: "DAGANGNET",
    externalStatus: row.externalStatus === "Tidak Aktif" ? "Tidak Aktif" : "Aktif",
    createdAt: row.createdAt.toISOString(),
  };
}

function toApp(row: {
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
  exportDate: Date;
  farmName: string;
  importerName: string;
  importerAddress: string;
  status: ExportApplication["status"];
  submittedAt: Date | null;
  reviewedAt: Date | null;
  createdAt: Date;
  updatedAt: Date;
}): ExportApplication {
  return {
    ...row,
    exportDate: row.exportDate.toISOString().slice(0, 10),
    submittedAt: iso(row.submittedAt),
    reviewedAt: iso(row.reviewedAt),
    createdAt: row.createdAt.toISOString(),
    updatedAt: row.updatedAt.toISOString(),
  };
}

export function createPrismaRepository(): Repositories {
  const client = getPrisma();
  if (!client) throw new Error("DATABASE_URL is required for Prisma repository");
  const prisma = client;

  async function writeAudit(actor: SessionUser, action: string, objectType: string, objectId: string, remarks?: string) {
    await prisma.auditLog.create({
      data: {
        id: createId("audit"),
        actorUserId: actor.id,
        actorRole: actor.role,
        action,
        objectType,
        objectId,
        beforeJson: null,
        afterJson: null,
        remarks: remarks ?? null,
      },
    });
  }

  return {
    async findUserByEmail(email) {
      const row = await prisma.user.findUnique({ where: { email } });
      return row ? toUser(row) : null;
    },
    async findUserById(id) {
      const row = await prisma.user.findUnique({ where: { id } });
      return row ? toUser(row) : null;
    },
    async createExporterUser(input) {
      const row = await prisma.user.create({
        data: {
          id: createId("user"),
          name: input.name,
          email: input.email,
          passwordHash: await bcrypt.hash(input.password, 10),
          role: "EXPORTER",
          identityReference: input.identityReference,
          status: "ACTIVE",
          companyId: input.companyId,
        },
      });
      return toUser(row);
    },
    async createFamaUser(input) {
      const row = await prisma.user.create({
        data: {
          id: createId("user"),
          name: input.name,
          email: input.email,
          passwordHash: await bcrypt.hash(input.password, 10),
          role: "FAMA_OFFICER",
          identityReference: input.identityReference,
          status: "ACTIVE",
        },
      });
      return toUser(row);
    },
    async listCompanies() {
      return (await prisma.company.findMany()).map((row) => toCompany(row));
    },
    async getCompany(id) {
      const row = await prisma.company.findUnique({ where: { id } });
      return row ? toCompany(row) : null;
    },
    async updateCompany(id, patch) {
      const safe = patch;
      const row = await prisma.company.update({
        where: { id },
        data: {
          address: safe.address,
          state: safe.state,
          district: safe.district,
          postcode: safe.postcode,
          phone: safe.phone,
          email: safe.email,
          website: safe.website,
          logoPath: safe.logoPath,
        },
      });
      return toCompany(row);
    },
    async listProduceTypes() {
      return prisma.produceType.findMany();
    },
    async listCompanyProduce(companyId) {
      return prisma.companyProduce.findMany({ where: { companyId } }) as Promise<CompanyProduce[]>;
    },
    async addCompanyProduce(companyId, produceTypeId) {
      return prisma.companyProduce.create({
        data: { id: createId("cp"), companyId, produceTypeId, active: true },
      });
    },
    async removeCompanyProduce(id) {
      await prisma.companyProduce.delete({ where: { id } });
    },
    async listCertificates(companyId) {
      const rows = await prisma.certificate.findMany({ where: { companyId } });
      return rows.map((row) => ({
        ...row,
        type: row.type as Certificate["type"],
        issueDate: row.issueDate.toISOString().slice(0, 10),
        expiryDate: row.expiryDate ? row.expiryDate.toISOString().slice(0, 10) : null,
        status: row.status as Certificate["status"],
      }));
    },
    async addCertificate(input) {
      const row = await prisma.certificate.create({
        data: {
          id: createId("cert"),
          ...input,
          issueDate: new Date(input.issueDate),
          expiryDate: input.expiryDate ? new Date(input.expiryDate) : null,
        },
      });
      return {
        ...row,
        type: row.type as Certificate["type"],
        issueDate: row.issueDate.toISOString().slice(0, 10),
        expiryDate: row.expiryDate ? row.expiryDate.toISOString().slice(0, 10) : null,
        status: row.status as Certificate["status"],
      };
    },
    async deleteCertificate(id) {
      await prisma.certificate.delete({ where: { id } });
    },
    async listGallery(companyId) {
      const rows = await prisma.galleryItem.findMany({ where: { companyId } });
      return rows.map((row) => ({
        ...row,
        category: row.category as GalleryItem["category"],
        uploadedAt: row.uploadedAt.toISOString(),
      }));
    },
    async addGalleryItem(input) {
      const row = await prisma.galleryItem.create({
        data: { id: createId("gal"), ...input, uploadedAt: new Date(input.uploadedAt) },
      });
      return { ...row, category: row.category as GalleryItem["category"], uploadedAt: row.uploadedAt.toISOString() };
    },
    async updateGalleryItem(id, patch) {
      const row = await prisma.galleryItem.update({ where: { id }, data: patch });
      return { ...row, category: row.category as GalleryItem["category"], uploadedAt: row.uploadedAt.toISOString() };
    },
    async deleteGalleryItem(id) {
      await prisma.galleryItem.delete({ where: { id } });
    },
    async listApplications(filter) {
      const rows = await prisma.exportApplication.findMany({
        where: { companyId: filter?.companyId, status: filter?.status },
      });
      return rows.map((row) => toApp(row));
    },
    async getApplication(id) {
      const row = await prisma.exportApplication.findUnique({ where: { id } });
      return row ? toApp(row) : null;
    },
    async createApplication(input) {
      const existing = await prisma.exportApplication.findMany({ select: { applicationNo: true } });
      const row = await prisma.exportApplication.create({
        data: {
          id: createId("app"),
          applicationNo: nextApplicationNo(existing.map((item) => item.applicationNo)),
          status: "DRAFT",
          ...input,
          exportDate: new Date(input.exportDate),
        },
      });
      return toApp(row);
    },
    async updateApplication(id, patch) {
      const current = await prisma.exportApplication.findUnique({ where: { id } });
      if (!current || current.status !== "DRAFT") throw new Error("Hanya draf boleh dikemaskini");
      const row = await prisma.exportApplication.update({
        where: { id },
        data: {
          ...patch,
          exportDate: patch.exportDate ? new Date(patch.exportDate) : undefined,
        },
      });
      return toApp(row);
    },
    async submitApplication(id, actor) {
      const current = await prisma.exportApplication.findUnique({ where: { id } });
      if (!current) throw new Error("Permohonan tidak dijumpai");
      assertApplicationTransition(current.status, "SUBMITTED");
      const row = await prisma.exportApplication.update({
        where: { id },
        data: { status: "SUBMITTED", submittedAt: new Date() },
      });
      await writeAudit(actor, "APPLICATION_SUBMITTED", "ExportApplication", id);
      return toApp(row);
    },
    async startReview(id, actor) {
      const current = await prisma.exportApplication.findUnique({ where: { id } });
      if (!current) throw new Error("Permohonan tidak dijumpai");
      if (current.status === "UNDER_REVIEW") return toApp(current);
      assertApplicationTransition(current.status, "UNDER_REVIEW");
      const row = await prisma.exportApplication.update({
        where: { id },
        data: { status: "UNDER_REVIEW" },
      });
      await writeAudit(actor, "APPLICATION_UNDER_REVIEW", "ExportApplication", id);
      return toApp(row);
    },
    async approveApplication(id, actor, remarks) {
      const current = await prisma.exportApplication.findUnique({ where: { id } });
      if (!current) throw new Error("Permohonan tidak dijumpai");
      let status = current.status;
      if (status === "SUBMITTED") status = "UNDER_REVIEW";
      assertApplicationTransition(status, "APPROVED");
      const row = await prisma.exportApplication.update({
        where: { id },
        data: { status: "APPROVED", reviewedAt: new Date() },
      });
      await prisma.approval.create({
        data: {
          id: createId("appr"),
          applicationId: id,
          officerUserId: actor.id,
          decision: "APPROVED",
          remarks,
          decidedAt: new Date(),
        },
      });
      const qr = await prisma.qrCode.findUnique({ where: { applicationId: id } });
      if (qr) {
        assertQrTransition(qr.status, "ACTIVE");
        await prisma.qrCode.update({
          where: { id: qr.id },
          data: { status: "ACTIVE", activatedAt: new Date() },
        });
      }
      await writeAudit(actor, "APPLICATION_APPROVED", "ExportApplication", id, remarks);
      return toApp(row);
    },
    async rejectApplication(id, actor, remarks) {
      if (!remarks.trim()) throw new Error("Catatan penolakan diperlukan");
      const current = await prisma.exportApplication.findUnique({ where: { id } });
      if (!current) throw new Error("Permohonan tidak dijumpai");
      let status = current.status;
      if (status === "SUBMITTED") status = "UNDER_REVIEW";
      assertApplicationTransition(status, "REJECTED");
      const row = await prisma.exportApplication.update({
        where: { id },
        data: { status: "REJECTED", reviewedAt: new Date() },
      });
      await prisma.approval.create({
        data: {
          id: createId("appr"),
          applicationId: id,
          officerUserId: actor.id,
          decision: "REJECTED",
          remarks,
          decidedAt: new Date(),
        },
      });
      await writeAudit(actor, "APPLICATION_REJECTED", "ExportApplication", id, remarks);
      return toApp(row);
    },
    async listQrCodes(filter) {
      const rows = await prisma.qrCode.findMany({
        include: { application: true },
      });
      return rows
        .filter((row) => !filter?.companyId || row.application.companyId === filter.companyId)
        .map((row) => ({
          id: row.id,
          qrCode: row.qrCode,
          applicationId: row.applicationId,
          publicSlug: row.publicSlug,
          status: row.status,
          generatedAt: row.generatedAt.toISOString(),
          activatedAt: iso(row.activatedAt),
        }));
    },
    async getQrById(id) {
      const row = await prisma.qrCode.findUnique({ where: { id } });
      return row
        ? {
            id: row.id,
            qrCode: row.qrCode,
            applicationId: row.applicationId,
            publicSlug: row.publicSlug,
            status: row.status,
            generatedAt: row.generatedAt.toISOString(),
            activatedAt: iso(row.activatedAt),
          }
        : null;
    },
    async getQrByCode(qrCode) {
      const row = await prisma.qrCode.findFirst({
        where: { OR: [{ qrCode }, { publicSlug: qrCode }] },
      });
      return row
        ? {
            id: row.id,
            qrCode: row.qrCode,
            applicationId: row.applicationId,
            publicSlug: row.publicSlug,
            status: row.status,
            generatedAt: row.generatedAt.toISOString(),
            activatedAt: iso(row.activatedAt),
          }
        : null;
    },
    async generateQr(applicationId, actor) {
      const existing = await prisma.qrCode.findUnique({ where: { applicationId } });
      if (existing) {
        return {
          ...existing,
          generatedAt: existing.generatedAt.toISOString(),
          activatedAt: iso(existing.activatedAt),
        } satisfies QrCodeRecord;
      }
      const all = await prisma.qrCode.findMany({ select: { qrCode: true } });
      const code = nextQrCode(all.map((row) => row.qrCode));
      const row = await prisma.qrCode.create({
        data: {
          id: createId("qr"),
          qrCode: code,
          applicationId,
          publicSlug: code,
          status: "GENERATED_INACTIVE",
          generatedAt: new Date(),
        },
      });
      await writeAudit(actor, "QR_GENERATED", "QRCode", row.id);
      return {
        ...row,
        generatedAt: row.generatedAt.toISOString(),
        activatedAt: iso(row.activatedAt),
      };
    },
    async listApprovals(applicationId) {
      const rows = await prisma.approval.findMany({ where: { applicationId } });
      return rows.map((row) => ({
        ...row,
        decision: row.decision as "APPROVED" | "REJECTED",
        decidedAt: row.decidedAt.toISOString(),
      }));
    },
    async listAudit(filter) {
      const rows = await prisma.auditLog.findMany({ orderBy: { createdAt: "desc" } });
      if (!filter?.companyId) {
        return rows.map((row) => ({
          ...row,
          actorRole: row.actorRole as never,
          createdAt: row.createdAt.toISOString(),
        }));
      }
      const apps = await prisma.exportApplication.findMany({
        where: { companyId: filter.companyId },
        select: { id: true },
      });
      const ids = new Set(apps.map((row) => row.id));
      return rows
        .filter((row) => ids.has(row.objectId) || row.objectId === filter.companyId)
        .map((row) => ({
          ...row,
          actorRole: row.actorRole as never,
          createdAt: row.createdAt.toISOString(),
        }));
    },
    async listNotifications(userId) {
      const rows = await prisma.notification.findMany({ where: { userId } });
      return rows.map((row) => ({ ...row, createdAt: row.createdAt.toISOString() }));
    },
    async getNutrition(produceTypeId) {
      return nutritionByProduce[produceTypeId] ?? [];
    },
    async dashboardExporter(companyId) {
      const apps = await prisma.exportApplication.findMany({ where: { companyId } });
      const qrs = await prisma.qrCode.findMany({
        where: { applicationId: { in: apps.map((row) => row.id) } },
      });
      return {
        qrActive: qrs.filter((qr) => qr.status === "ACTIVE").length,
        qrInactive: qrs.filter((qr) => qr.status === "GENERATED_INACTIVE").length,
        totalApplications: apps.length,
        approved: apps.filter((app) => app.status === "APPROVED").length,
        rejected: apps.filter((app) => app.status === "REJECTED").length,
      };
    },
    async dashboardFama() {
      const days = ["ISNIN", "SELASA", "RABU", "KHAMIS", "JUMAAT", "SABTU", "AHAD"];
      const [companies, qrs, apps] = await Promise.all([
        prisma.company.count({ where: { externalStatus: "Aktif" } }),
        prisma.qrCode.findMany(),
        prisma.exportApplication.findMany(),
      ]);
      return {
        activeCompanies: companies,
        qrActive: qrs.filter((qr) => qr.status === "ACTIVE").length,
        approved: apps.filter((app) => app.status === "APPROVED").length,
        pending: apps.filter((app) => app.status === "SUBMITTED" || app.status === "UNDER_REVIEW").length,
        rejected: apps.filter((app) => app.status === "REJECTED").length,
        dailyQr: days.map((day, index) => ({
          day,
          active: 18 + ((index * 5) % 17),
          inactive: 12 + ((index * 3) % 11),
        })),
      };
    },
  };
}
