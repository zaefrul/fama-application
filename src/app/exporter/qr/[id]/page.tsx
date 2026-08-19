import Link from "next/link";
import { QrPreview } from "@/components/qr-preview";
import { StatusBadge } from "@/components/status-badge";
import { Button, Card, PageTitle } from "@/components/ui";
import { hydrateApplication } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { traceUrl } from "@/lib/app-url";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function QrDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const qr = await repos.getQrById(id);
  if (!qr) notFound();
  const application = await repos.getApplication(qr.applicationId);
  if (!application || application.companyId !== session.companyId) notFound();
  const { produce } = await hydrateApplication(application);
  const publicUrl = await traceUrl(qr.qrCode);

  return (
    <div className="space-y-4">
      <PageTitle title={qr.qrCode} subtitle={produce?.name} />
      <StatusBadge qr={qr.status} />
      <Card className="space-y-3">
        <QrPreview value={publicUrl} />
        <p className="text-sm">Produk: {produce?.name} {application.variety}</p>
        <p className="text-sm">Gred: {application.grade}</p>
        <p className="text-sm">Pengimport: {application.importerName}</p>
        <Link href={`/exporter/qr/${qr.id}/download`}>
          <Button className="w-full">Muat Turun QR</Button>
        </Link>
      </Card>
    </div>
  );
}
