import { rmSync } from "node:fs";
import path from "node:path";

export default function globalSetup() {
  rmSync(path.join(process.cwd(), ".data", "fixture-store.json"), { force: true });
}
