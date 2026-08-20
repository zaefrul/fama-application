import { createCompanyAction } from "@/app/actions/fama";
import { FamaCompanyForm } from "@/components/fama-company-form";
import { Breadcrumb, Card, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";

export default async function FamaNewCompanyPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  await requireRole("FAMA_OFFICER");
  const { error } = await searchParams;

  return (
    <div className="space-y-4">
      <Breadcrumb items={["Senarai Syarikat", "Daftar Vendor"]} />
      <PageTitle title="Daftar Vendor" subtitle="Rekod syarikat diurus oleh FAMA. Tiada akaun pengeksport dicipta." />
      <Card>
        <FamaCompanyForm action={createCompanyAction} error={error} submitLabel="Simpan Vendor" />
      </Card>
    </div>
  );
}
