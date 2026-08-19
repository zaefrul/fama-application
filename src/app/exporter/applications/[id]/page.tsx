import { generateQrAction, submitApplicationAction, updateApplicationAction } from "@/app/actions/exporter";
import { ApplicationForm } from "@/components/application-form";
import { QrPreview } from "@/components/qr-preview";
import { StatusBadge } from "@/components/status-badge";
import { Breadcrumb, Button, Card, DataRow, PageTitle } from "@/components/ui";
import { hydrateApplication } from "@/lib/lookups";
import { traceUrl } from "@/lib/app-url";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function ApplicationDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const application = await repos.getApplication(id);
  if (!application || application.companyId !== session.companyId) notFound();

  const { company, produce, qr } = await hydrateApplication(application);
  const produceTypes = await repos.listProduceTypes();
  const certificates = await repos.listCertificates(application.companyId);
  const publicUrl = qr ? await traceUrl(qr.qrCode) : "";

  return (
    <div className="space-y-4">
      <Breadcrumb items={["Senarai QR", "Maklumat Eksport", "Ringkasan"]} />
      <div className="flex items-start justify-between gap-3">
        <PageTitle title={application.applicationNo} subtitle={produce ? `${produce.name} ${application.variety}` : ""} />
        <StatusBadge application={application.status} />
      </div>
      {application.status === "DRAFT" ? (
        <ApplicationForm
          action={updateApplicationAction.bind(null, application.id)}
          application={application}
          produceTypes={produceTypes}
          certificates={certificates}
        />
      ) : (
        <Card className="px-5">
          <dl>
            <DataRow label="Pengeksport" value={company?.name} />
            <DataRow label="Alamat" value={company?.address} />
            <DataRow label="Gred" value={application.grade} />
            <DataRow label="Saiz" value={application.size} />
            <DataRow label="Kuantiti" value={`${application.quantity} ${application.quantityUnit}`} />
            <DataRow label="Destinasi" value={application.destinationCountry} />
            <DataRow label="Ladang" value={application.farmName} />
            <DataRow label="Pengimport" value={application.importerName} />
            <DataRow label="Alamat pengimport" value={application.importerAddress} />
            <DataRow label="No. Sijil CoC" value={application.cocNumber} />
          </dl>
        </Card>
      )}

      {qr ? (
        <Card className="space-y-4 px-5 py-5">
          <div className="flex items-center justify-between">
            <h2 className="font-semibold">Kod QR</h2>
            <StatusBadge qr={qr.status} />
          </div>
          <QrPreview value={publicUrl} />
          {application.status === "DRAFT" ? (
            <form action={submitApplicationAction.bind(null, application.id)}>
              <Button type="submit" className="w-full">
                Hantar
              </Button>
            </form>
          ) : null}
        </Card>
      ) : (
        <form action={generateQrAction.bind(null, application.id)}>
          <Button type="submit">Jana QR</Button>
        </form>
      )}
    </div>
  );
}
