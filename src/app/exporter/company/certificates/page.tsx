import { addCertificateAction, deleteCertificateAction } from "@/app/actions/exporter";
import { DocumentPreview } from "@/components/document-preview";
import { CompanyNav } from "@/components/nav";
import { Button, Card, ErrorText, Field, Input, PageTitle, Select } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function CertificatesPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const session = await requireRole("EXPORTER");
  const { error } = await searchParams;
  const certificates = session.companyId ? await getRepositories().listCertificates(session.companyId) : [];

  return (
    <div className="space-y-4">
      <PageTitle
        title="Sijil"
        subtitle="Muat naik salinan sijil (JPG, PNG, WEBP atau PDF, maksimum 5MB). Tiada pengesahan pihak berkuasa untuk V1."
      />
      <CompanyNav />
      <Card>
        <form action={addCertificateAction} className="grid gap-3 md:grid-cols-2">
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
          <div className="md:col-span-2">
            <ErrorText>{error}</ErrorText>
          </div>
        </form>
      </Card>
      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
        {certificates.map((certificate) => (
          <Card key={certificate.id} className="min-w-0 space-y-2">
            <DocumentPreview src={certificate.documentPath} alt={certificate.type} />
            <p className="truncate text-sm font-semibold">SIJIL {certificate.type}</p>
            <p className="truncate text-xs text-muted">{certificate.certificateNo}</p>
            <a href={certificate.documentPath} target="_blank" rel="noreferrer" className="text-xs font-semibold text-brand">
              Buka fail
            </a>
            <form action={deleteCertificateAction}>
              <input type="hidden" name="id" value={certificate.id} />
              <Button type="submit" variant="danger">
                Buang
              </Button>
            </form>
          </Card>
        ))}
      </div>
    </div>
  );
}
