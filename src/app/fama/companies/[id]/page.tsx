import { DocumentPreview } from "@/components/document-preview";
import { Card, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { produceName } from "@/lib/lookups";
import { notFound } from "next/navigation";

export default async function FamaCompanyDetailPage({ params }: { params: Promise<{ id: string }> }) {
  await requireRole("FAMA_OFFICER");
  const { id } = await params;
  const repos = getRepositories();
  const company = await repos.getCompany(id);
  if (!company) notFound();
  const produce = await repos.listCompanyProduce(id);
  const types = await repos.listProduceTypes();
  const certificates = await repos.listCertificates(id);
  const gallery = await repos.listGallery(id);

  return (
    <div className="space-y-4">
      <PageTitle title={company.name} subtitle={company.registrationNo} />
      <Card className="space-y-1 text-sm">
        <p>Alamat: {company.address}</p>
        <p>Negeri: {company.state}</p>
        <p>Emel: {company.email}</p>
        <p>Telefon: {company.phone}</p>
        <p>Status luaran: {company.externalStatus}</p>
      </Card>
      <Card>
        <h2 className="mb-2 font-semibold">Keluaran</h2>
        <p className="text-sm">{produce.map((row) => produceName(types, row.produceTypeId)).join(", ") || "—"}</p>
      </Card>
      <Card>
        <h2 className="mb-2 font-semibold">Sijil</h2>
        <div className="grid grid-cols-2 gap-2">
          {certificates.map((certificate) => (
            <a
              key={certificate.id}
              href={certificate.documentPath}
              target="_blank"
              rel="noreferrer"
              className="overflow-hidden rounded-xl border border-border bg-surface-muted p-2 text-xs"
            >
              <DocumentPreview src={certificate.documentPath} alt={certificate.type} className="mb-2 h-20 w-full object-cover" />
              <p className="font-semibold">SIJIL {certificate.type}</p>
              <p className="text-muted">{certificate.certificateNo}</p>
            </a>
          ))}
        </div>
      </Card>
      <Card>
        <h2 className="mb-2 font-semibold">Galeri</h2>
        <div className="grid grid-cols-3 gap-2">
          {gallery.map((item) => (
            // eslint-disable-next-line @next/next/no-img-element
            <img key={item.id} src={item.filePath} alt={item.description} className="h-20 w-full rounded-lg object-cover" />
          ))}
        </div>
      </Card>
    </div>
  );
}
