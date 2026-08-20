import assert from "node:assert/strict";
import { test } from "node:test";
import { canTransitionApplication, canTransitionQr } from "./transitions";

test("application transitions", () => {
  assert.equal(canTransitionApplication("DRAFT", "SUBMITTED"), true);
  assert.equal(canTransitionApplication("SUBMITTED", "UNDER_REVIEW"), true);
  assert.equal(canTransitionApplication("UNDER_REVIEW", "APPROVED"), true);
  assert.equal(canTransitionApplication("UNDER_REVIEW", "REJECTED"), true);
  assert.equal(canTransitionApplication("APPROVED", "REJECTED"), false);
  assert.equal(canTransitionApplication("REJECTED", "DRAFT"), false);
  assert.equal(canTransitionApplication("DRAFT", "APPROVED"), false);
});

test("qr transitions", () => {
  assert.equal(canTransitionQr("NOT_GENERATED", "GENERATED_INACTIVE"), true);
  assert.equal(canTransitionQr("GENERATED_INACTIVE", "ACTIVE"), true);
  assert.equal(canTransitionQr("ACTIVE", "GENERATED_INACTIVE"), false);
});
