import { approveAction, rejectAction } from "@/app/actions/fama";
import { QrPreview } from "@/components/qr-preview";
import { StatusBadge } from "@/components/status-badge";
import { Breadcrumb, Button, Card, DataRow, ErrorText, Field, PageTitle, Textarea } from "@/components/ui";
import { hydrateApplication } from "@/lib/lookups";
import { traceUrl } from "@/lib/app-url";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function FamaApplicationDetailPage({
  params,
  searchParams,
}: {
  params: Promise<{ id: string }>;
  searchParams: Promise<{ error?: string }>;
}) {
  const session = await requireRole("FAMA_OFFICER");
  const { id } = await params;
  const { error } = await searchParams;
  const repos = getRepositories();
  let application = await repos.getApplication(id);
  if (!application) notFound();
  if (application.status === "SUBMITTED") {
    application = await repos.startReview(id, session);
  }
  const { company, produce, qr } = await hydrateApplication(application);
  const approvals = await repos.listApprovals(id);
  const publicUrl = qr ? await traceUrl(qr.qrCode) : "";
  const canDecide = application.status === "UNDER_REVIEW" || application.status === "SUBMITTED";

  return (
    <div className="space-y-4">
      <Breadcrumb items={["Senarai QR", "Maklumat Eksport", "Ringkasan"]} />
      <div className="flex items-start justify-between gap-3">
        <PageTitle title="Ringkasan" subtitle={application.applicationNo} />
        <StatusBadge application={application.status} />
      </div>
      {qr ? <QrPreview value={publicUrl} /> : null}
      <Card className="px-5">
        <dl>
          <DataRow label="Tarikh eksport" value={application.exportDate} />
          <DataRow label="Jenis keluaran" value={produce?.name} />
          <DataRow label="Gred" value={application.grade} />
          <DataRow label="Saiz" value={application.size} />
          <DataRow label="Pengeksport" value={company?.name} />
          <DataRow label="Alamat pengeksport" value={company?.address} />
          <DataRow label="Nama ladang" value={application.farmName} />
          <DataRow label="Pengimport" value={application.importerName} />
          <DataRow label="Alamat pengimport" value={application.importerAddress} />
          <DataRow label="No. Sijil CoC" value={application.cocNumber} />
        </dl>
      </Card>
      {canDecide ? (
        <Card className="space-y-4 px-5 py-5">
          <Field label="Catatan" required>
            <Textarea name="remarks" form="reject-form" required placeholder="Wajib jika menolak" />
          </Field>
          {error === "remarks" ? <ErrorText>Catatan penolakan diperlukan.</ErrorText> : null}
          <div className="grid grid-cols-2 gap-2">
            <form id="reject-form" action={rejectAction.bind(null, id)}>
              <Button type="submit" variant="danger" className="w-full">
                Tolak
              </Button>
            </form>
            <form action={approveAction.bind(null, id)}>
              <input type="hidden" name="remarks" value="Diluluskan" />
              <Button type="submit" className="w-full">
                Sahkan
              </Button>
            </form>
          </div>
        </Card>
      ) : (
        <Card>
          <h2 className="mb-2 font-semibold">Keputusan</h2>
          {approvals.map((approval) => (
            <p key={approval.id} className="text-sm">
              {approval.decision === "APPROVED" ? "Diluluskan" : "Ditolak"} · {approval.remarks}
            </p>
          ))}
        </Card>
      )}
    </div>
  );
}
