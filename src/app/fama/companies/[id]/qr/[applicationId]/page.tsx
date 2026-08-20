import { updateManagedApplicationAction } from "@/app/actions/fama";
import { ApplicationForm } from "@/components/application-form";
import { QrPreview } from "@/components/qr-preview";
import { StatusBadge } from "@/components/status-badge";
import { Button, Card, ErrorText, Field, PageTitle, Select } from "@/components/ui";
import { traceUrl } from "@/lib/app-url";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function FamaEditQrPage({
  params,
  searchParams,
}: {
  params: Promise<{ id: string; applicationId: string }>;
  searchParams: Promise<{ error?: string }>;
}) {
  await requireRole("FAMA_OFFICER");
  const { id, applicationId } = await params;
  const { error } = await searchParams;
  const repos = getRepositories();
  const company = await repos.getCompany(id);
  const application = await repos.getApplication(applicationId);
  if (!company || !application || application.companyId !== id) notFound();
  const qrs = await repos.listQrCodes({ companyId: id });
  const qr = qrs.find((row) => row.applicationId === applicationId);
  const produceTypes = await repos.listProduceTypes();
  const certificates = await repos.listCertificates(id);
  const publicUrl = qr ? await traceUrl(qr.qrCode) : "";

  return (
    <div className="space-y-4">
      <div className="flex items-start justify-between gap-3">
        <PageTitle title="Kemaskini QR" subtitle={`${company.name} · ${qr?.qrCode ?? application.applicationNo}`} />
        {qr ? <StatusBadge qr={qr.status} /> : null}
      </div>
      {qr ? <QrPreview value={publicUrl} /> : null}
      {qr ? (
        <Card className="space-y-3">
          <form action={`/api/exporter/qr/${qr.id}/download`} method="get" className="grid gap-3 sm:grid-cols-2">
            <Field label="Saiz QR">
              <Select name="size" defaultValue="5">
                <option value="3">3 cm</option>
                <option value="5">5 cm</option>
                <option value="8">Custom (8 cm)</option>
              </Select>
            </Field>
            <Field label="Format Muat Turun">
              <Select name="format" defaultValue="png">
                <option value="png">PNG</option>
                <option value="pdf">PDF</option>
              </Select>
            </Field>
            <div className="sm:col-span-2">
              <Button type="submit" className="w-full">
                Muat Turun QR
              </Button>
            </div>
          </form>
        </Card>
      ) : null}
      <ErrorText>{error}</ErrorText>
      <ApplicationForm
        action={updateManagedApplicationAction.bind(null, id, applicationId)}
        application={application}
        produceTypes={produceTypes}
        certificates={certificates}
        editable
        hideSecondary
        primaryLabel="Simpan"
      />
    </div>
  );
}
