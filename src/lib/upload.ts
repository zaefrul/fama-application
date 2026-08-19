import { mkdir, writeFile } from "node:fs/promises";
import path from "node:path";

const MAX_BYTES = 5 * 1024 * 1024;

const IMAGE_TYPES = new Set(["image/jpeg", "image/png", "image/webp"]);
const CERT_TYPES = new Set([...IMAGE_TYPES, "application/pdf"]);

function extensionFor(type: string, originalName: string) {
  const fromName = path.extname(originalName).toLowerCase();
  if (fromName && [".jpg", ".jpeg", ".png", ".webp", ".pdf"].includes(fromName)) {
    return fromName;
  }
  if (type === "image/jpeg") return ".jpg";
  if (type === "image/png") return ".png";
  if (type === "image/webp") return ".webp";
  if (type === "application/pdf") return ".pdf";
  return "";
}

export async function saveUpload(
  file: File | null,
  folder: "certificates" | "gallery" | "logos",
  options: { allowPdf?: boolean; required?: boolean } = {},
) {
  if (!file || file.size === 0) {
    if (options.required) {
      throw new Error("Sila muat naik fail");
    }
    return null;
  }

  if (file.size > MAX_BYTES) {
    throw new Error("Fail melebihi 5MB");
  }

  const allowed = options.allowPdf ? CERT_TYPES : IMAGE_TYPES;
  if (!allowed.has(file.type)) {
    throw new Error(options.allowPdf ? "Format dibenarkan: JPG, PNG, WEBP atau PDF" : "Format dibenarkan: JPG, PNG atau WEBP");
  }

  const ext = extensionFor(file.type, file.name);
  const filename = `${folder}_${Date.now()}_${Math.random().toString(36).slice(2, 8)}${ext}`;
  const dir = path.join(process.cwd(), "public", "uploads", folder);
  await mkdir(dir, { recursive: true });
  const bytes = Buffer.from(await file.arrayBuffer());
  await writeFile(path.join(dir, filename), bytes);
  return `/uploads/${folder}/${filename}`;
}

export function isPdfPath(filePath: string) {
  return filePath.toLowerCase().endsWith(".pdf");
}
