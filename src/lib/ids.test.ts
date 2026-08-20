import assert from "node:assert/strict";
import { test } from "node:test";
import { nextApplicationNo, nextQrCode } from "./ids";

test("nextApplicationNo uses the trailing serial only", () => {
  assert.equal(
    nextApplicationNo(["FAMA-2026-000123", "FAMA-2026-000124", "FAMA-2026-2.026e+22"]),
    "FAMA-2026-000125",
  );
});

test("nextQrCode increments the trailing serial", () => {
  assert.equal(nextQrCode(["GPL-QR-000123", "GPL-QR-000127"]), "GPL-QR-000128");
});
