import { assertApplicationTransition, assertQrTransition } from "@/domain/transitions";
import type {
  Certificate,
  Company,
  CompanyProduce,
  ExportApplication,
  GalleryItem,
  QrCodeRecord,
  SessionUser,
} from "@/domain/types";
import { createId, nextApplicationNo, nextQrCode } from "@/lib/ids";
import { getStore, persistStore, nutritionByProduce, produceTypes } from "@/fixtures/store";
import type { Repositories } from "./contracts";

function now() {
  return new Date().toISOString();
}

export function createFixtureRepository(): Repositories {
  return {
    async findUserByEmail(email) {
      return getStore().users.find((user) => user.email.toLowerCase() === email.toLowerCase()) ?? null;
    },
    async findUserById(id) {
      return getStore().users.find((user) => user.id === id) ?? null;
    },
    async createExporterUser(input) {
      const store = getStore();
      const user = {
        id: createId("user"),
        name: input.name,
        email: input.email,
        password: input.password,
        role: "EXPORTER" as const,
        identityReference: input.identityReference,
        status: "ACTIVE" as const,
        companyId: input.companyId,
        createdAt: now(),
      };
      store.users.push(user);
      persistStore(store);
      return user;
    },
    async createFamaUser(input) {
      const store = getStore();
      const user = {
        id: createId("user"),
        name: input.name,
        email: input.email,
        password: input.password,
        role: "FAMA_OFFICER" as const,
        identityReference: input.identityReference,
        status: "ACTIVE" as const,
        companyId: null,
        createdAt: now(),
      };
      store.users.push(user);
      persistStore(store);
      return user;
    },

    async listCompanies() {
      return getStore().companies;
    },
    async getCompany(id) {
      return getStore().companies.find((company) => company.id === id) ?? null;
    },
    async updateCompany(id, patch) {
      const store = getStore();
      const company = store.companies.find((row) => row.id === id);
      if (!company) throw new Error("Syarikat tidak dijumpai");
      const locked = new Set(["id", "registrationNo", "name", "externalAccountNo", "externalSource", "externalStatus"]);
      Object.entries(patch).forEach(([key, value]) => {
        if (!locked.has(key) && value !== undefined) {
          (company as unknown as Record<string, unknown>)[key] = value;
        }
      });
      persistStore(store);
      return company;
    },

    async listProduceTypes() {
      return produceTypes;
    },
    async listCompanyProduce(companyId) {
      return getStore().companyProduce.filter((row) => row.companyId === companyId);
    },
    async addCompanyProduce(companyId, produceTypeId) {
      const store = getStore();
      const existing = store.companyProduce.find(
        (row) => row.companyId === companyId && row.produceTypeId === produceTypeId,
      );
      if (existing) return existing;
      const row: CompanyProduce = {
        id: createId("cp"),
        companyId,
        produceTypeId,
        variety: null,
        active: true,
      };
      store.companyProduce.push(row);
      persistStore(store);
      return row;
    },
    async removeCompanyProduce(id) {
      const store = getStore();
      store.companyProduce = store.companyProduce.filter((row) => row.id !== id);
      persistStore(store);
    },

    async listCertificates(companyId) {
      return getStore().certificates.filter((row) => row.companyId === companyId);
    },
    async addCertificate(input) {
      const store = getStore();
      const row: Certificate = { ...input, id: createId("cert") };
      store.certificates.push(row);
      persistStore(store);
      return row;
    },
    async deleteCertificate(id) {
      const store = getStore();
      store.certificates = store.certificates.filter((row) => row.id !== id);
      persistStore(store);
    },
    async listGallery(companyId) {
      return getStore().galleryItems.filter((row) => row.companyId === companyId);
    },
    async addGalleryItem(input) {
      const store = getStore();
      const row: GalleryItem = { ...input, id: createId("gal") };
      store.galleryItems.push(row);
      persistStore(store);
      return row;
    },
    async updateGalleryItem(id, patch) {
      const store = getStore();
      const row = store.galleryItems.find((item) => item.id === id);
      if (!row) throw new Error("Gambar tidak dijumpai");
      Object.assign(row, patch);
      persistStore(store);
      return row;
    },
    async deleteGalleryItem(id) {
      const store = getStore();
      store.galleryItems = store.galleryItems.filter((item) => item.id !== id);
      persistStore(store);
    },

    async listApplications(filter) {
      return getStore().applications.filter((application) => {
        if (filter?.companyId && application.companyId !== filter.companyId) return false;
        if (filter?.status && application.status !== filter.status) return false;
        return true;
      });
    },
    async getApplication(id) {
      return getStore().applications.find((application) => application.id === id) ?? null;
    },
    async createApplication(input) {
      const store = getStore();
      const application: ExportApplication = {
        id: createId("app"),
        applicationNo: nextApplicationNo(store.applications.map((row) => row.applicationNo)),
        status: "DRAFT",
        submittedAt: null,
        reviewedAt: null,
        createdAt: now(),
        updatedAt: now(),
        ...input,
      };
      store.applications.push(application);
      persistStore(store);
      return application;
    },
    async updateApplication(id, patch) {
      const store = getStore();
      const application = store.applications.find((row) => row.id === id);
      if (!application) throw new Error("Permohonan tidak dijumpai");
      if (application.status !== "DRAFT") {
        throw new Error("Hanya draf boleh dikemaskini");
      }
      Object.assign(application, patch, { updatedAt: now() });
      persistStore(store);
      return application;
    },
    async submitApplication(id, actor) {
      const store = getStore();
      const application = mustApplication(store, id);
      assertApplicationTransition(application.status, "SUBMITTED");
      application.status = "SUBMITTED";
      application.submittedAt = now();
      application.updatedAt = now();
      writeAudit(store, actor, "APPLICATION_SUBMITTED", "ExportApplication", id, { status: "DRAFT" }, { status: "SUBMITTED" });
      notifyFama(store, "Permohonan menunggu semakan", `${application.applicationNo} menunggu pengesahan.`);
      persistStore(store);
      return application;
    },
    async startReview(id, actor) {
      const store = getStore();
      const application = mustApplication(store, id);
      if (application.status === "UNDER_REVIEW") return application;
      assertApplicationTransition(application.status, "UNDER_REVIEW");
      application.status = "UNDER_REVIEW";
      application.updatedAt = now();
      writeAudit(store, actor, "APPLICATION_UNDER_REVIEW", "ExportApplication", id, null, { status: "UNDER_REVIEW" });
      persistStore(store);
      return application;
    },
    async approveApplication(id, actor, remarks) {
      const store = getStore();
      const application = mustApplication(store, id);
      if (application.status === "SUBMITTED") {
        application.status = "UNDER_REVIEW";
      }
      assertApplicationTransition(application.status, "APPROVED");
      application.status = "APPROVED";
      application.reviewedAt = now();
      application.updatedAt = now();
      store.approvals.push({
        id: createId("appr"),
        applicationId: id,
        officerUserId: actor.id,
        decision: "APPROVED",
        remarks,
        decidedAt: now(),
      });
      const qr = store.qrCodes.find((row) => row.applicationId === id);
      if (qr) {
        assertQrTransition(qr.status, "ACTIVE");
        qr.status = "ACTIVE";
        qr.activatedAt = now();
      }
      writeAudit(store, actor, "APPLICATION_APPROVED", "ExportApplication", id, null, { status: "APPROVED" }, remarks);
      notifyCompany(store, application.companyId, "Permohonan diluluskan", `${application.applicationNo} telah diluluskan. QR diaktifkan.`);
      persistStore(store);
      return application;
    },
    async rejectApplication(id, actor, remarks) {
      if (!remarks.trim()) throw new Error("Catatan penolakan diperlukan");
      const store = getStore();
      const application = mustApplication(store, id);
      if (application.status === "SUBMITTED") {
        application.status = "UNDER_REVIEW";
      }
      assertApplicationTransition(application.status, "REJECTED");
      application.status = "REJECTED";
      application.reviewedAt = now();
      application.updatedAt = now();
      store.approvals.push({
        id: createId("appr"),
        applicationId: id,
        officerUserId: actor.id,
        decision: "REJECTED",
        remarks,
        decidedAt: now(),
      });
      writeAudit(store, actor, "APPLICATION_REJECTED", "ExportApplication", id, null, { status: "REJECTED" }, remarks);
      notifyCompany(store, application.companyId, "Permohonan ditolak", `${application.applicationNo} telah ditolak.`);
      persistStore(store);
      return application;
    },

    async listQrCodes(filter) {
      const store = getStore();
      return store.qrCodes.filter((qr) => {
        if (!filter?.companyId) return true;
        const application = store.applications.find((row) => row.id === qr.applicationId);
        return application?.companyId === filter.companyId;
      });
    },
    async getQrById(id) {
      return getStore().qrCodes.find((qr) => qr.id === id) ?? null;
    },
    async getQrByCode(qrCode) {
      return getStore().qrCodes.find((qr) => qr.qrCode === qrCode || qr.publicSlug === qrCode) ?? null;
    },
    async generateQr(applicationId, actor) {
      const store = getStore();
      const existing = store.qrCodes.find((qr) => qr.applicationId === applicationId);
      if (existing) return existing;
      const qr: QrCodeRecord = {
        id: createId("qr"),
        qrCode: nextQrCode(store.qrCodes.map((row) => row.qrCode)),
        applicationId,
        publicSlug: "",
        status: "GENERATED_INACTIVE",
        generatedAt: now(),
        activatedAt: null,
      };
      qr.publicSlug = qr.qrCode;
      store.qrCodes.push(qr);
      writeAudit(store, actor, "QR_GENERATED", "QRCode", qr.id, { status: "NOT_GENERATED" }, { status: "GENERATED_INACTIVE" });
      persistStore(store);
      return qr;
    },

    async listApprovals(applicationId) {
      return getStore().approvals.filter((row) => row.applicationId === applicationId);
    },
    async listAudit(filter) {
      const store = getStore();
      if (!filter?.companyId) return store.auditLogs;
      const applicationIds = new Set(
        store.applications.filter((row) => row.companyId === filter.companyId).map((row) => row.id),
      );
      return store.auditLogs.filter((row) => applicationIds.has(row.objectId) || row.objectId === filter.companyId);
    },
    async listNotifications(userId) {
      return getStore().notifications.filter((row) => row.userId === userId);
    },
    async getNutrition(produceTypeId) {
      return nutritionByProduce[produceTypeId] ?? [];
    },

    async dashboardExporter(companyId) {
      const store = getStore();
      const apps = store.applications.filter((row) => row.companyId === companyId);
      const qrs = store.qrCodes.filter((qr) => apps.some((app) => app.id === qr.applicationId));
      return {
        qrActive: qrs.filter((qr) => qr.status === "ACTIVE").length,
        qrInactive: qrs.filter((qr) => qr.status === "GENERATED_INACTIVE").length,
        totalApplications: apps.length,
        approved: apps.filter((app) => app.status === "APPROVED").length,
        rejected: apps.filter((app) => app.status === "REJECTED").length,
      };
    },
    async dashboardFama() {
      const store = getStore();
      const days = ["ISNIN", "SELASA", "RABU", "KHAMIS", "JUMAAT", "SABTU", "AHAD"];
      return {
        activeCompanies: store.companies.filter((company) => company.externalStatus === "Aktif").length,
        qrActive: store.qrCodes.filter((qr) => qr.status === "ACTIVE").length,
        approved: store.applications.filter((app) => app.status === "APPROVED").length,
        pending: store.applications.filter((app) => app.status === "SUBMITTED" || app.status === "UNDER_REVIEW").length,
        rejected: store.applications.filter((app) => app.status === "REJECTED").length,
        dailyQr: days.map((day, index) => ({
          day,
          active: 18 + ((index * 5) % 17),
          inactive: 12 + ((index * 3) % 11),
        })),
      };
    },
  };
}

function mustApplication(store: ReturnType<typeof getStore>, id: string) {
  const application = store.applications.find((row) => row.id === id);
  if (!application) throw new Error("Permohonan tidak dijumpai");
  return application;
}

function writeAudit(
  store: ReturnType<typeof getStore>,
  actor: SessionUser,
  action: string,
  objectType: string,
  objectId: string,
  before: unknown,
  after: unknown,
  remarks?: string,
) {
  store.auditLogs.unshift({
    id: createId("audit"),
    actorUserId: actor.id,
    actorRole: actor.role,
    action,
    objectType,
    objectId,
    beforeJson: before ? JSON.stringify(before) : null,
    afterJson: after ? JSON.stringify(after) : null,
    remarks: remarks ?? null,
    createdAt: now(),
  });
}

function notifyFama(store: ReturnType<typeof getStore>, title: string, body: string) {
  store.users
    .filter((user) => user.role === "FAMA_OFFICER")
    .forEach((officer) => {
      store.notifications.unshift({
        id: createId("nt"),
        userId: officer.id,
        title,
        body,
        read: false,
        createdAt: now(),
      });
    });
}

function notifyCompany(store: ReturnType<typeof getStore>, companyId: string, title: string, body: string) {
  store.users
    .filter((user) => user.companyId === companyId)
    .forEach((user) => {
      store.notifications.unshift({
        id: createId("nt"),
        userId: user.id,
        title,
        body,
        read: false,
        createdAt: now(),
      });
    });
}

export type { Company };
