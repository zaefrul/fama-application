import { existsSync, mkdirSync, readFileSync, writeFileSync } from "node:fs";
import path from "node:path";
import {
  approvals as seedApprovals,
  applications as seedApplications,
  auditLogs as seedAudit,
  certificates as seedCertificates,
  companies as seedCompanies,
  companyProduce as seedProduce,
  galleryItems as seedGallery,
  notifications as seedNotifications,
  nutritionByProduce,
  produceTypes,
  qrCodes as seedQr,
  users as seedUsers,
} from "@/fixtures/data";
import type {
  Approval,
  AuditLog,
  Certificate,
  Company,
  CompanyProduce,
  ExportApplication,
  GalleryItem,
  Notification,
  QrCodeRecord,
  User,
} from "@/domain/types";

export interface FixtureStore {
  users: User[];
  companies: Company[];
  companyProduce: CompanyProduce[];
  certificates: Certificate[];
  galleryItems: GalleryItem[];
  applications: ExportApplication[];
  qrCodes: QrCodeRecord[];
  approvals: Approval[];
  auditLogs: AuditLog[];
  notifications: Notification[];
}

const STORE_PATH = path.join(process.cwd(), ".data", "fixture-store.json");
let memoryStore: FixtureStore | null = null;

function clone<T>(value: T): T {
  return structuredClone(value);
}

function seedStore(): FixtureStore {
  return {
    users: clone(seedUsers),
    companies: clone(seedCompanies),
    companyProduce: clone(seedProduce),
    certificates: clone(seedCertificates),
    galleryItems: clone(seedGallery),
    applications: clone(seedApplications),
    qrCodes: clone(seedQr),
    approvals: clone(seedApprovals),
    auditLogs: clone(seedAudit),
    notifications: clone(seedNotifications),
  };
}

export function persistStore(store: FixtureStore) {
  memoryStore = store;
  try {
    mkdirSync(path.dirname(STORE_PATH), { recursive: true });
    writeFileSync(STORE_PATH, JSON.stringify(store));
  } catch {
    // Hosted/read-only filesystems keep the in-memory copy only.
  }
}

export function getStore(): FixtureStore {
  if (memoryStore) return memoryStore;
  try {
    if (existsSync(STORE_PATH)) {
      memoryStore = JSON.parse(readFileSync(STORE_PATH, "utf8")) as FixtureStore;
      return memoryStore;
    }
  } catch {
    // Fall through to seed data.
  }
  memoryStore = seedStore();
  persistStore(memoryStore);
  return memoryStore;
}

export function resetStore() {
  persistStore(seedStore());
}

export { produceTypes, nutritionByProduce };
