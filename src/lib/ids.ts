function nextSerial(existing: string[], prefix: string) {
  const numbers = existing
    .map((value) => {
      const suffix = value.split("-").pop() ?? "";
      if (!/^\d{1,6}$/.test(suffix)) return Number.NaN;
      return Number(suffix);
    })
    .filter((value) => Number.isFinite(value));
  const next = (numbers.length ? Math.max(...numbers) : 0) + 1;
  return `${prefix}${String(next).padStart(6, "0")}`;
}

export function nextApplicationNo(existing: string[]): string {
  return nextSerial(existing, "FAMA-2026-");
}

export function nextQrCode(existing: string[]): string {
  return nextSerial(existing, "GPL-QR-");
}

export function createId(prefix: string) {
  return `${prefix}_${crypto.randomUUID()}`;
}
