export function nextApplicationNo(existing: string[]): string {
  const numbers = existing
    .map((value) => Number(value.replace(/\D/g, "")))
    .filter((value) => Number.isFinite(value));
  const next = (numbers.length ? Math.max(...numbers) : 0) + 1;
  return `FAMA-2026-${String(next).padStart(6, "0")}`;
}

export function nextQrCode(existing: string[]): string {
  const numbers = existing
    .map((value) => Number(value.replace(/\D/g, "")))
    .filter((value) => Number.isFinite(value));
  const next = (numbers.length ? Math.max(...numbers) : 0) + 1;
  return `GPL-QR-${String(next).padStart(6, "0")}`;
}

export function createId(prefix: string) {
  return `${prefix}_${crypto.randomUUID()}`;
}
