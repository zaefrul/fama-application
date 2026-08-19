process.chdir(__dirname);
process.env.NODE_ENV = "production";

const { createServer } = require("http");
const { parse } = require("url");

let handle;
let ready = false;
let bootError;

const server = createServer((req, res) => {
  if (bootError) {
    res.writeHead(500, { "Content-Type": "text/plain; charset=utf-8" });
    res.end(String(bootError.stack || bootError));
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
  console.log("Listening on passenger");
} else {
  server.listen(bind, () => {
    console.log("Listening on", bind, "cwd", process.cwd());
  });
}

server.on("error", (error) => {
  console.error("Listen error", error);
});

process.on("uncaughtException", (error) => {
  console.error("uncaughtException", error);
});

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
