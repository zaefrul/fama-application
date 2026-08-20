import Link from "next/link";
import {
  addManagedCertificateAction,
  deleteManagedCertificateAction,
  updateManagedCompanyAction,
} from "@/app/actions/fama";
import { DocumentPreview } from "@/components/document-preview";
import { FamaCompanyForm } from "@/components/fama-company-form";
import { StatusBadge } from "@/components/status-badge";
import { Button, Card, ErrorText, Field, Input, PageTitle, Select } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function FamaCompanyDetailPage({
  params,
  searchParams,
}: {
  params: Promise<{ id: string }>;
  searchParams: Promise<{ error?: string }>;
}) {
  await requireRole("FAMA_OFFICER");
  const { id } = await params;
  const { error } = await searchParams;
  const repos = getRepositories();
  const company = await repos.getCompany(id);
  if (!company) notFound();
  const produce = await repos.listCompanyProduce(id);
  const types = await repos.listProduceTypes();
  const certificates = await repos.listCertificates(id);
  const applications = await repos.listApplications({ companyId: id });
  const qrs = await repos.listQrCodes({ companyId: id });

  return (
    <div className="space-y-4">
      <PageTitle title={company.name} subtitle={`${company.registrationNo} · ${company.externalSource}`} />
      <Card>
        <FamaCompanyForm
          action={updateManagedCompanyAction.bind(null, id)}
          company={company}
          error={error}
          submitLabel="Simpan Maklumat"
        />
      </Card>

      <Card>
        <div className="mb-3 flex items-center justify-between gap-3">
          <h2 className="font-semibold">Kod QR</h2>
          <Link href={`/fama/companies/${id}/qr/new`}>
            <Button type="button">Cipta QR</Button>
          </Link>
        </div>
        {qrs.length === 0 ? (
          <p className="text-sm text-muted">Tiada QR. Cipta QR untuk paparan awam.</p>
        ) : (
          <ul className="space-y-2">
            {qrs.map((qr) => {
              const application = applications.find((row) => row.id === qr.applicationId);
              return (
                <li key={qr.id}>
                  <Card className="flex flex-wrap items-center justify-between gap-2">
                    <div>
                      <p className="font-semibold">{qr.qrCode}</p>
                      <p className="text-xs text-muted">{application?.variety || produceName(types, application?.produceTypeId ?? "")}</p>
                    </div>
                    <StatusBadge qr={qr.status} />
                    <div className="flex gap-2">
                      <Link href={`/fama/companies/${id}/qr/${qr.applicationId}`} className="text-sm font-semibold text-brand">
                        Kemaskini
                      </Link>
                      <a href={`/api/exporter/qr/${qr.id}/download?format=png&size=5`} className="text-sm font-semibold text-brand">
                        Muat turun
                      </a>
                    </div>
                  </Card>
                </li>
              );
            })}
          </ul>
        )}
      </Card>

      <Card>
        <h2 className="mb-2 font-semibold">Keluaran</h2>
        <p className="text-sm">{produce.map((row) => produceName(types, row.produceTypeId)).join(", ") || "—"}</p>
      </Card>

      <Card>
        <h2 className="mb-3 font-semibold">Sijil</h2>
        <form action={addManagedCertificateAction.bind(null, id)} className="mb-4 grid gap-3 md:grid-cols-2">
          <Field label="Jenis">
            <Select name="type">
              <option>HACCP</option>
              <option>MyGAP</option>
              <option>CoC</option>
              <option>FITOSANITASI</option>
              <option value="ISO_22000">ISO 22000</option>
            </Select>
          </Field>
          <Field label="No. Sijil" required>
            <Input name="certificateNo" required />
          </Field>
          <Field label="Tarikh Keluar">
            <Input name="issueDate" type="date" />
          </Field>
          <Field label="Tarikh Tamat">
            <Input name="expiryDate" type="date" />
          </Field>
          <Field label="Fail sijil" required>
            <Input name="document" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" required />
          </Field>
          <div className="flex items-end">
            <Button type="submit">+ Muat Naik Sijil</Button>
          </div>
        </form>
        {error ? <ErrorText>{error}</ErrorText> : null}
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
          {certificates.map((certificate) => (
            <div key={certificate.id} className="space-y-2 rounded-xl border border-border bg-surface-muted p-2">
              <DocumentPreview src={certificate.documentPath} alt={certificate.type} className="mb-2 h-20 w-full object-cover" />
              <p className="font-semibold">SIJIL {certificate.type}</p>
              <p className="text-xs text-muted">{certificate.certificateNo}</p>
              <form action={deleteManagedCertificateAction.bind(null, id)}>
                <input type="hidden" name="id" value={certificate.id} />
                <Button type="submit" variant="danger">
                  Buang
                </Button>
              </form>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}
