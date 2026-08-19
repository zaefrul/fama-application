import { createApplicationAction } from "@/app/actions/exporter";
import { ApplicationForm } from "@/components/application-form";
import { PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function NewApplicationPage() {
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const produceTypes = await repos.listProduceTypes();
  const certificates = session.companyId ? await repos.listCertificates(session.companyId) : [];

  return (
    <div>
      <PageTitle title="Permohonan Baharu" subtitle="QR tidak aktif akan dijana apabila draf disimpan." />
      <ApplicationForm action={createApplicationAction} produceTypes={produceTypes} certificates={certificates} />
    </div>
  );
}
