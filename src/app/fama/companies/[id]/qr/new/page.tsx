import { createAndActivateQrAction } from "@/app/actions/fama";
import { ApplicationForm } from "@/components/application-form";
import { ErrorText, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { notFound } from "next/navigation";

export default async function FamaNewQrPage({
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
  const produceTypes = await repos.listProduceTypes();
  const certificates = await repos.listCertificates(id);

  return (
    <div className="space-y-4">
      <PageTitle title="Cipta QR" subtitle={`${company.name} · QR akan diaktifkan serta-merta.`} />
      <ErrorText>{error}</ErrorText>
      <ApplicationForm
        action={createAndActivateQrAction.bind(null, id)}
        produceTypes={produceTypes}
        certificates={certificates}
        editable
        hideSecondary
        primaryLabel="Cipta dan Aktifkan QR"
      />
    </div>
  );
}
