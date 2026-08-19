process.chdir(__dirname);
process.env.NODE_ENV = "production";

const { existsSync } = require("fs");
const { createServer } = require("http");
const { join } = require("path");
const { parse } = require("url");

let handle;
let ready = false;
let bootError;

function errorPage(error) {
  const message = String(error && (error.stack || error.message || error));
  return `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Jejak GPL — server error</title>
  <style>
    body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 2rem; color: #111; }
    pre { white-space: pre-wrap; background: #f4f4f5; padding: 1rem; border-radius: 8px; }
  </style>
</head>
<body>
  <h1>Sistem Jejak GPL</h1>
  <p>The Node app is running, but Next.js did not start.</p>
  <pre>${message.replace(/[&<>]/g, (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;" })[char])}</pre>
  <p>On Plesk: Stop the app → <code>npm run build</code> → Restart App.</p>
</body>
</html>`;
}

const server = createServer((req, res) => {
  if (bootError) {
    res.writeHead(500, { "Content-Type": "text/html; charset=utf-8" });
    res.end(errorPage(bootError));
    return;
  }

  if (!ready || !handle) {
    res.writeHead(200, { "Content-Type": "text/html; charset=utf-8" });
    res.end(
      "<!doctype html><meta charset=utf-8><meta http-equiv=refresh content=3><title>Jejak GPL</title><p style=\"font-family:sans-serif;padding:2rem\">Memuatkan Sistem Jejak GPL…</p>",
    );
    return;
  }

  handle(req, res, parse(req.url, true));
});

const bind = process.env.PORT || process.env.LSNODE_SOCK || 3000;

if (typeof PhusionPassenger !== "undefined") {
  PhusionPassenger.configure({ autoInstall: false });
  server.listen("passenger");
} else {
  server.listen(bind, () => {
    console.log("Listening on", bind, "cwd", process.cwd());
  });
}

server.on("error", (error) => {
  bootError = error;
  console.error("Listen error", error);
});

process.on("uncaughtException", (error) => {
  bootError = error;
  console.error("uncaughtException", error);
});

if (!existsSync(join(__dirname, ".next", "BUILD_ID"))) {
  bootError = new Error(
    "Could not find a production build in the '.next' directory. Try building your app with 'next build' before starting the production server. https://nextjs.org/docs/messages/production-start-no-build-id",
  );
} else {
  setImmediate(() => {
    try {
      const next = require("next");
      const app = next({ dev: false, dir: __dirname });
      app
        .prepare()
        .then(() => {
          handle = app.getRequestHandler();
          ready = true;
          console.log("Sistem Jejak GPL ready");
        })
        .catch((error) => {
          bootError = error;
          console.error(error);
        });
    } catch (error) {
      bootError = error;
      console.error(error);
    }
  });
}
