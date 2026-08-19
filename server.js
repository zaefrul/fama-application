process.env.NODE_ENV = "production";

const { existsSync } = require("fs");
const { join } = require("path");

const dir = __dirname;
const manifest = join(dir, ".next", "prerender-manifest.json");

if (!existsSync(manifest)) {
  console.error("Missing .next/prerender-manifest.json. Run npm run build first.");
  process.exit(1);
}

const port = Number(process.env.PORT) || 3000;

try {
  const { nextStart } = require("next/dist/cli/next-start");
  nextStart({
    port,
    hostname: "0.0.0.0",
    dir,
  });
} catch (error) {
  console.error(error);
  process.exit(1);
}
