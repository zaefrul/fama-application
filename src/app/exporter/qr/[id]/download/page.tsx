import { QrPreview } from "@/components/qr-preview";
import { Button, Card, Field, PageTitle, Select } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { traceUrl } from "@/lib/app-url";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function QrDownloadPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requireRole("EXPORTER");
  const qr = await getRepositories().getQrById(id);
  if (!qr) notFound();
  const application = await getRepositories().getApplication(qr.applicationId);
  if (!application || application.companyId !== session.companyId) notFound();
  const publicUrl = await traceUrl(qr.qrCode);

  return (
    <div className="space-y-4">
      <PageTitle title="Muat Turun QR" />
      <Card className="space-y-4">
        <QrPreview value={publicUrl} />
        <form action={`/api/exporter/qr/${qr.id}/download`} method="get" className="space-y-3">
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
          <Button type="submit" className="w-full">
            Muat Turun QR
          </Button>
        </form>
      </Card>
    </div>
  );
}
