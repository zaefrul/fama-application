import Link from "next/link";
import { ApplicationCard } from "@/components/application-card";
import { Button, PageTitle } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function ApplicationsPage() {
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const applications = session.companyId ? await repos.listApplications({ companyId: session.companyId }) : [];
  const produceTypes = await repos.listProduceTypes();

  return (
    <div className="space-y-4">
      <div className="flex items-start justify-between gap-3">
        <PageTitle title="Permohonan" />
        <Link href="/exporter/applications/new" className="shrink-0 pt-0.5">
          <Button>+ Tambah</Button>
        </Link>
      </div>
      <ul className="space-y-2">
        {applications.map((application) => (
          <li key={application.id}>
            <ApplicationCard
              href={`/exporter/applications/${application.id}`}
              title={`${application.applicationNo} · ${produceName(produceTypes, application.produceTypeId)} ${application.variety}`}
              subtitle={`Dihantar pada ${application.submittedAt ? new Date(application.submittedAt).toLocaleDateString("ms-MY", { day: "2-digit", month: "long", year: "numeric" }) : "—"}`}
              status={application.status}
            />
          </li>
        ))}
      </ul>
    </div>
  );
}
