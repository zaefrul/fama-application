const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "..");
const linkDir = path.join(root, "_next");
const link = path.join(linkDir, "static");
const target = path.join(root, ".next", "static");

if (!fs.existsSync(target)) {
  console.warn("No .next/static yet; skip static link.");
  process.exit(0);
}

fs.mkdirSync(linkDir, { recursive: true });
try {
  fs.lstatSync(link);
  fs.rmSync(link, { recursive: true, force: true });
} catch {
  // no existing link
}

try {
  fs.symlinkSync(path.relative(linkDir, target), link);
  console.log("Linked _next/static -> .next/static");
} catch (error) {
  fs.cpSync(target, link, { recursive: true });
  console.log("Copied .next/static to _next/static", error instanceof Error ? error.message : "");
}
