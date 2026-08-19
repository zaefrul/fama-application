process.chdir(__dirname);

const { createServer } = require("http");

const server = createServer((_req, res) => {
  res.writeHead(200, { "Content-Type": "text/plain; charset=utf-8" });
  res.end("Node OK — jejakgpl.metadatasystem.my");
});

const bind = process.env.PORT || 3000;
server.listen(bind, () => {
  console.log("app.js listening on", bind);
});

server.on("error", (error) => {
  console.error(error);
  process.exit(1);
});
