import { headers } from "next/headers";

function strip(url: string) {
  return url.replace(/\/$/, "");
}

function isLocalHost(host: string) {
  return /^(localhost|127\.0\.0\.1)(:\d+)?$/i.test(host);
}

function firstHeader(value: string | null) {
  return value?.split(",")[0]?.trim() || "";
}

export async function appUrl() {
  try {
    const h = await headers();
    const hosts = [firstHeader(h.get("x-forwarded-host")), firstHeader(h.get("host"))].filter(Boolean);
    const publicHost = hosts.find((host) => !isLocalHost(host));
    const proto = firstHeader(h.get("x-forwarded-proto")) || (publicHost ? "https" : "http");
    if (publicHost) return strip(`${proto}://${publicHost}`);
  } catch {
    // No request context (scripts, tests).
  }

  const configured = process.env.NEXT_PUBLIC_APP_URL;
  if (configured) {
    try {
      if (!isLocalHost(new URL(configured).host)) return strip(configured);
    } catch {
      return strip(configured);
    }
  }
  if (process.env.VERCEL_URL) return strip(`https://${process.env.VERCEL_URL}`);
  if (configured) return strip(configured);
  return "http://localhost:3000";
}

export async function traceUrl(qrCode: string) {
  return `${await appUrl()}/trace/${qrCode}`;
}
