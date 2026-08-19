import { brandedQrPng } from "@/lib/qr-branded";
import { NextResponse } from "next/server";

export async function GET(request: Request) {
  const url = new URL(request.url);
  const data = url.searchParams.get("data") ?? "";
  const size = Math.min(1024, Math.max(160, Number(url.searchParams.get("size") ?? "360")));
  if (!data) {
    return NextResponse.json({ error: "missing data" }, { status: 400 });
  }

  const png = await brandedQrPng(data, size);
  return new NextResponse(new Uint8Array(png), {
    headers: {
      "Content-Type": "image/png",
      "Cache-Control": "no-store",
    },
  });
}
