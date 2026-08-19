import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Card, PageTitle } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import type { ApplicationStatus } from "@/domain/status";

export default async function FamaApplicationsPage({
  searchParams,
}: {
  searchParams: Promise<{ status?: string }>;
}) {
  await requireRole("FAMA_OFFICER");
  const { status } = await searchParams;
  const repos = getRepositories();
  const applications = await repos.listApplications(
    status ? { status: status as ApplicationStatus } : undefined,
  );
  const companies = await repos.listCompanies();
  const produceTypes = await repos.listProduceTypes();

  return (
    <div className="space-y-4">
      <PageTitle title="Kelulusan QR" subtitle="Senarai permohonan untuk semakan FAMA" />
      <ul className="space-y-2">
        {applications.map((application) => {
          const company = companies.find((row) => row.id === application.companyId);
          return (
            <li key={application.id}>
              <Link href={`/fama/applications/${application.id}`}>
                <Card className="flex items-center justify-between">
                  <div>
                    <p className="font-semibold">
                      {application.applicationNo} · {produceName(produceTypes, application.produceTypeId)}
                    </p>
                    <p className="text-xs text-muted">{company?.name}</p>
                  </div>
                  <StatusBadge application={application.status} />
                </Card>
              </Link>
            </li>
          );
        })}
      </ul>
    </div>
  );
}
