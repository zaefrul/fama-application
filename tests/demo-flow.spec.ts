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
