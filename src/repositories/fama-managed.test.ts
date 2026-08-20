import assert from "node:assert/strict";
import { afterEach, test } from "node:test";
import type { SessionUser } from "@/domain/types";
import { resetStore, getStore } from "@/fixtures/store";
import { createFixtureRepository } from "./fixture-repository";

const fama: SessionUser = {
  id: "user_fama",
  name: "Ali bin Abu Ghani",
  email: "aliabu@fama.gov.my",
  role: "FAMA_OFFICER",
  companyId: null,
};

const applicationInput = {
  companyId: "",
  produceTypeId: "pt_durian",
  variety: "Musang King",
  grade: "A",
  size: "L",
  quantity: 120,
  quantityUnit: "kg",
  destinationCountry: "MAHA",
  cocCertificateId: null,
  cocNumber: "",
  exportDate: "2026-08-21",
  farmName: "Ladang MAHA",
  importerName: "Pengunjung MAHA",
  importerAddress: "MAEPS Serdang",
};

afterEach(() => {
  resetStore();
});

test("FAMA creates a company without a user row", async () => {
  resetStore();
  const repos = createFixtureRepository();
  const usersBefore = getStore().users.length;
  const company = await repos.createCompany(
    {
      name: "MAHA Durian Stall",
      registrationNo: "MH100001",
      email: "maha@example.com",
      phone: "03-1111 2222",
      address: "Dewan MAHA, Serdang",
      state: "Selangor",
      district: "Serdang",
      postcode: "43400",
      website: "",
    },
    fama,
  );
  assert.equal(company.externalSource, "FAMA");
  assert.equal(company.externalAccountNo, "FAMA-MH100001");
  assert.equal(getStore().users.length, usersBefore);
  assert.equal(
    getStore().users.some((user) => user.companyId === company.id),
    false,
  );
  assert.ok(getStore().auditLogs.some((row) => row.action === "COMPANY_CREATED" && row.objectId === company.id));
});

test("duplicate registration or FAMA account is rejected", async () => {
  resetStore();
  const repos = createFixtureRepository();
  await assert.rejects(
    () =>
      repos.createCompany(
        {
          name: "Copy",
          registrationNo: "AB34567",
          email: "",
          phone: "",
          address: "",
          state: "",
          district: "",
          postcode: "",
          website: "",
        },
        fama,
      ),
    /telah wujud/,
  );
});

test("FAMA create QR activates application and public QR", async () => {
  resetStore();
  const repos = createFixtureRepository();
  const company = await repos.createCompany(
    {
      name: "MAHA Nanas",
      registrationNo: "MH100002",
      email: "",
      phone: "",
      address: "Serdang",
      state: "Selangor",
      district: "Serdang",
      postcode: "43400",
      website: "",
    },
    fama,
  );
  const { application, qr } = await repos.createAndActivateQr(company.id, applicationInput, fama);
  assert.equal(application.status, "APPROVED");
  assert.equal(qr.status, "ACTIVE");
  assert.equal(qr.applicationId, application.id);
  assert.match(qr.qrCode, /^GPL-QR-\d+$/);
  assert.equal(qr.publicSlug, qr.qrCode);
});

test("FAMA can edit approved public fields without changing QR identity", async () => {
  resetStore();
  const repos = createFixtureRepository();
  const company = await repos.createCompany(
    {
      name: "MAHA Betik",
      registrationNo: "MH100003",
      email: "",
      phone: "",
      address: "Serdang",
      state: "Selangor",
      district: "Serdang",
      postcode: "43400",
      website: "",
    },
    fama,
  );
  const { application, qr } = await repos.createAndActivateQr(company.id, applicationInput, fama);
  const updated = await repos.updateManagedApplication(
    application.id,
    { ...applicationInput, farmName: "Ladang MAHA Kemaskini", variety: "Black Thorn" },
    fama,
  );
  const sameQr = await repos.getQrById(qr.id);
  assert.equal(updated.farmName, "Ladang MAHA Kemaskini");
  assert.equal(updated.variety, "Black Thorn");
  assert.equal(sameQr?.qrCode, qr.qrCode);
  assert.equal(sameQr?.publicSlug, qr.publicSlug);
  assert.equal(sameQr?.status, "ACTIVE");
});

test("exporter updateApplication stays draft-only", async () => {
  resetStore();
  const repos = createFixtureRepository();
  await assert.rejects(() => repos.updateApplication("app_109", { variety: "Tidak dibenarkan" }), /Hanya draf/);
});

test("DagangNet company name stays locked for FAMA edits", async () => {
  resetStore();
  const repos = createFixtureRepository();
  const updated = await repos.updateManagedCompany(
    "co_abc",
    { name: "Should Not Change", address: "Alamat baru FAMA" },
    fama,
  );
  assert.equal(updated.name, "ABC Fruits Sdn. Bhd.");
  assert.equal(updated.address, "Alamat baru FAMA");
  assert.equal(updated.externalSource, "DAGANGNET");
});
