import path from "node:path";
import QRCode from "qrcode";
import { Jimp } from "jimp";

const LOGO_PATH = path.join(process.cwd(), "public/logos/jejak-gpl.png");

/** Prototype QR look. Official FAMA label layout is still an open question. */
const STYLE = {
  dark: 0x0f6b4cff,
  light: 0xffffffff,
  marginModules: 2,
  logoScale: 0.32,
  moduleGap: 0.16,
  moduleRadius: 0.38,
} as const;

type Bitmap = InstanceType<typeof Jimp>;

function setPixel(image: Bitmap, x: number, y: number, color: number) {
  if (x < 0 || y < 0 || x >= image.bitmap.width || y >= image.bitmap.height) return;
  image.setPixelColor(color, x, y);
}

function fillRoundedRect(
  image: Bitmap,
  x: number,
  y: number,
  width: number,
  height: number,
  radius: number,
  color: number,
) {
  const r = Math.max(0, Math.min(radius, Math.floor(Math.min(width, height) / 2)));
  const r2 = r * r;
  const x1 = Math.round(x);
  const y1 = Math.round(y);
  const x2 = Math.round(x + width);
  const y2 = Math.round(y + height);

  for (let py = y1; py < y2; py++) {
    for (let px = x1; px < x2; px++) {
      const dx = px < x1 + r ? x1 + r - px : px >= x2 - r ? px - (x2 - 1 - r) : 0;
      const dy = py < y1 + r ? y1 + r - py : py >= y2 - r ? py - (y2 - 1 - r) : 0;
      const corner = (px < x1 + r || px >= x2 - r) && (py < y1 + r || py >= y2 - r);
      if (!corner || dx * dx + dy * dy <= r2) {
        setPixel(image, px, py, color);
      }
    }
  }
}

function finderOrigin(row: number, col: number, count: number) {
  if (row < 7 && col < 7) return [0, 0] as const;
  if (row < 7 && col >= count - 7) return [0, count - 7] as const;
  if (row >= count - 7 && col < 7) return [count - 7, 0] as const;
  return null;
}

function drawFinder(image: Bitmap, originCol: number, originRow: number, module: number, margin: number) {
  const x = margin + originCol * module;
  const y = margin + originRow * module;
  const outer = module * 7;
  fillRoundedRect(image, x, y, outer, outer, module * 1.1, STYLE.dark);
  fillRoundedRect(image, x + module, y + module, module * 5, module * 5, module * 0.7, STYLE.light);
  fillRoundedRect(image, x + module * 2, y + module * 2, module * 3, module * 3, module * 0.45, STYLE.dark);
}

export async function brandedQrPng(data: string, size = 512): Promise<Buffer> {
  const qr = QRCode.create(data, { errorCorrectionLevel: "H" });
  const count = qr.modules.size;
  const cells = count + STYLE.marginModules * 2;
  const module = size / cells;
  const canvas = Math.max(size, Math.ceil(module * cells));
  const image = new Jimp({ width: canvas, height: canvas, color: STYLE.light });
  const margin = STYLE.marginModules * module;
  const drawnFinders = new Set<string>();

  for (let row = 0; row < count; row++) {
    for (let col = 0; col < count; col++) {
      const finder = finderOrigin(row, col, count);
      if (finder) {
        const key = finder.join(",");
        if (!drawnFinders.has(key)) {
          drawnFinders.add(key);
          drawFinder(image, finder[1], finder[0], module, margin);
        }
        continue;
      }
      if (!qr.modules.get(row, col)) continue;

      const gap = module * STYLE.moduleGap;
      const box = module - gap;
      fillRoundedRect(
        image,
        margin + col * module + gap / 2,
        margin + row * module + gap / 2,
        box,
        box,
        box * STYLE.moduleRadius,
        STYLE.dark,
      );
    }
  }

  const logo = await Jimp.read(LOGO_PATH);
  const logoBox = Math.round(canvas * STYLE.logoScale);
  const pad = Math.round(logoBox * 0.14);
  const inner = logoBox - pad * 2;
  logo.contain({ w: inner, h: inner });

  const plate = new Jimp({ width: logoBox, height: logoBox, color: STYLE.light });
  plate.composite(
    logo,
    Math.round((logoBox - logo.bitmap.width) / 2),
    Math.round((logoBox - logo.bitmap.height) / 2),
  );

  image.composite(
    plate,
    Math.round((image.bitmap.width - logoBox) / 2),
    Math.round((image.bitmap.height - logoBox) / 2),
  );

  if (canvas !== size) {
    image.resize({ w: size, h: size });
  }

  return image.getBuffer("image/png");
}
