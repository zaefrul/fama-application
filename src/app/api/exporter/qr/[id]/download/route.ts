import { NextResponse } from "next/server";
import { PDFDocument } from "pdf-lib";
import { brandedQrPng } from "@/lib/qr-branded";
import { traceUrl } from "@/lib/app-url";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> },
) {
  const session = await requireRole(["EXPORTER", "FAMA_OFFICER"]);
  const { id } = await params;
  const qr = await getRepositories().getQrById(id);
  if (!qr) return NextResponse.json({ error: "Not found" }, { status: 404 });
  const application = await getRepositories().getApplication(qr.applicationId);
  if (!application) return NextResponse.json({ error: "Not found" }, { status: 404 });
  if (session.role === "EXPORTER" && application.companyId !== session.companyId) {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }

  const url = new URL(request.url);
  const sizeCm = Number(url.searchParams.get("size") ?? "5");
  const format = url.searchParams.get("format") ?? "png";
  const publicUrl = await traceUrl(qr.qrCode);
  const png = await brandedQrPng(publicUrl, Math.round(sizeCm * 48));

  if (format === "pdf") {
    const pdf = await PDFDocument.create();
    const page = pdf.addPage([200, 260]);
    const image = await pdf.embedPng(png);
    page.drawImage(image, { x: 30, y: 70, width: 140, height: 140 });
    page.drawText(qr.qrCode, { x: 30, y: 40, size: 10 });
    const bytes = await pdf.save();
    return new NextResponse(Buffer.from(bytes), {
      headers: {
        "Content-Type": "application/pdf",
        "Content-Disposition": `attachment; filename="${qr.qrCode}.pdf"`,
      },
    });
  }

  return new NextResponse(new Uint8Array(png), {
    headers: {
      "Content-Type": "image/png",
      "Content-Disposition": `attachment; filename="${qr.qrCode}.png"`,
    },
  });
}
