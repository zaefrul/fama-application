import { getRepositories } from "@/repositories";
import { hydrateApplication } from "@/lib/lookups";
import { NextResponse } from "next/server";

export async function GET(
  _request: Request,
  { params }: { params: Promise<{ qrCode: string }> },
) {
  const { qrCode } = await params;
  const repos = getRepositories();
  const qr = await repos.getQrByCode(qrCode);
  if (!qr) return NextResponse.json({ error: "invalid" }, { status: 404 });
  if (qr.status !== "ACTIVE") {
    return NextResponse.json({ status: qr.status, qrCode: qr.qrCode, active: false });
  }
  const application = await repos.getApplication(qr.applicationId);
  if (!application) return NextResponse.json({ error: "invalid" }, { status: 404 });
  const { company, produce } = await hydrateApplication(application);
  const certificates = await repos.listCertificates(application.companyId);
  return NextResponse.json({
    active: true,
    qrCode: qr.qrCode,
    product: produce?.name,
    grade: application.grade,
    size: application.size,
    quantity: application.quantity,
    destination: application.destinationCountry,
    exporter: company?.name,
    exporterAddress: company?.address,
    farmName: application.farmName,
    importer: application.importerName,
    importerAddress: application.importerAddress,
    cocNumber: application.cocNumber,
    certificates: certificates.map((certificate) => ({
      type: certificate.type,
      number: certificate.certificateNo,
      documentPath: certificate.documentPath,
    })),
  });
}
