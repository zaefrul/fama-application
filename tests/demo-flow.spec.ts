import { expect, test } from "@playwright/test";

test.describe.configure({ mode: "serial" });

test("public inactive QR does not require login", async ({ page }) => {
  await page.goto("/trace/GPL-QR-000123");
  await expect(page.getByText("QR Belum Diaktifkan")).toBeVisible();
});

test("public active QR shows traceability", async ({ page }) => {
  await page.goto("/trace/GPL-QR-000109");
  await expect(page.getByRole("heading", { name: "Tembikai" })).toBeVisible();
  await expect(page.getByText("MTS Fruits Sdn. Bhd.")).toBeVisible();
});

test("invalid QR is safe", async ({ page }) => {
  await page.goto("/trace/GPL-QR-DOES-NOT-EXIST");
  await expect(page.getByText("Kod QR tidak sah.")).toBeVisible();
});

test("exporter login and cannot open FAMA", async ({ page }) => {
  await page.goto("/auth/login");
  await page.getByLabel("PENGEKSPORT").check();
  await page.getByLabel("Emel").fill("ali@abcfruits.example");
  await page.getByLabel("Kata Laluan").fill("Exporter123!");
  await page.getByRole("button", { name: "Log Masuk" }).click();
  await expect(page.getByText("ABC Fruits Sdn. Bhd.")).toBeVisible();
  await page.goto("/fama");
  await expect(page).toHaveURL(/\/exporter/);
});

test("FAMA can approve pending application and activate QR", async ({ page }) => {
  await page.goto("/auth/login");
  await page.getByLabel("FAMA").check();
  await page.getByLabel("Emel").fill("aliabu@fama.gov.my");
  await page.getByLabel("Kata Laluan").fill("Fama123!");
  await page.getByRole("button", { name: "Log Masuk" }).click();
  await expect(page.getByText("Utama FAMA")).toBeVisible();
  await page.goto("/fama/applications/app_123");
  await page.getByRole("button", { name: "Sahkan" }).click();
  await expect(page.getByText("Keputusan")).toBeVisible();
  await page.goto("/trace/GPL-QR-000123");
  await expect(page.getByRole("heading", { name: "Durian" })).toBeVisible();
});

test("exporter unknown DagangNet identifier", async ({ page }) => {
  await page.goto("/auth/register/exporter");
  await page.getByLabel("Nombor Akaun").fill("HPB00001");
  await page.getByRole("button", { name: "Seterusnya" }).click();
  await expect(page.getByText("Tiada rekod dijumpai")).toBeVisible();
});

test("FAMA can register vendor, activate QR, and edit public fields", async ({ page }) => {
  const suffix = Date.now().toString().slice(-6);
  const vendor = `MAHA Vendor ${suffix}`;
  const registration = `MH${suffix}`;

  await page.goto("/auth/login");
  await page.getByLabel("FAMA").check();
  await page.getByLabel("Emel").fill("aliabu@fama.gov.my");
  await page.getByLabel("Kata Laluan").fill("Fama123!");
  await page.getByRole("button", { name: "Log Masuk" }).click();
  await expect(page.getByText("Utama FAMA")).toBeVisible();

  await page.goto("/fama/companies");
  await page.getByRole("button", { name: "Daftar Vendor" }).click();
  await page.getByLabel("Nama Syarikat").fill(vendor);
  await page.getByLabel("No. Pendaftaran").fill(registration);
  await page.getByLabel("Alamat").fill("Dewan MAHA, Serdang");
  await page.getByLabel("Negeri").fill("Selangor");
  await page.getByRole("button", { name: "Simpan Vendor" }).click();
  await expect(page.getByRole("heading", { name: vendor })).toBeVisible();

  await page.getByRole("button", { name: "Cipta QR" }).click();
  await page.getByLabel("Jenis Keluaran Pertanian").selectOption({ label: "Durian" });
  await page.getByLabel("Varieti").fill("Musang King");
  await page.getByLabel("Gred").fill("A");
  await page.getByLabel("Saiz").fill("L");
  await page.getByLabel("Bilangan Eksport / Berat (kg)").fill("100");
  await page.getByLabel("Destinasi").fill("MAHA");
  await page.getByLabel("Tarikh Eksport").fill("2026-08-21");
  await page.getByLabel("Nama Ladang").fill("Ladang MAHA");
  await page.getByRole("textbox", { name: "Pengimport *", exact: true }).fill("Pengunjung MAHA");
  await page.getByLabel("Alamat Pengimport").fill("MAEPS Serdang");
  await page.getByRole("button", { name: "Cipta dan Aktifkan QR" }).click();
  await expect(page.getByText("Kemaskini QR")).toBeVisible();
  await expect(page.getByText("Aktif")).toBeVisible();

  const qrMatch = (await page.getByText(/GPL-QR-\d+/).first().textContent())?.match(/GPL-QR-\d+/);
  expect(qrMatch?.[0]).toBeTruthy();
  const qrCode = qrMatch![0];

  await page.goto(`/trace/${qrCode}`);
  await expect(page.getByRole("heading", { name: "Durian" })).toBeVisible();
  await expect(page.getByText(vendor)).toBeVisible();
  await expect(page.getByText("Ladang MAHA")).toBeVisible();

  await page.goto("/fama/companies");
  await page.getByText(vendor).click();
  await page.getByRole("link", { name: "Kemaskini" }).click();
  await page.getByLabel("Nama Ladang").fill("Ladang MAHA Kemaskini");
  await page.getByRole("button", { name: "Simpan" }).click();
  await page.waitForURL(/\?saved=1/);
  await expect(page.getByLabel("Nama Ladang")).toHaveValue("Ladang MAHA Kemaskini");

  await page.goto(`/trace/${qrCode}?lang=bm&t=${Date.now()}`);
  await expect(page.getByText("Ladang MAHA Kemaskini")).toBeVisible();
});
