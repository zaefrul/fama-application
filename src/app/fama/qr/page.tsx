import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Card, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function FamaQrPage() {
  await requireRole("FAMA_OFFICER");
  const repos = getRepositories();
  const qrs = await repos.listQrCodes();
  const applications = await repos.listApplications();
  const companies = await repos.listCompanies();

  return (
    <div className="space-y-4">
      <PageTitle title="Pengurusan QR" />
      <ul className="space-y-2">
        {qrs.map((qr) => {
          const application = applications.find((row) => row.id === qr.applicationId);
          const company = companies.find((row) => row.id === application?.companyId);
          return (
            <li key={qr.id}>
              <Link href={application ? `/fama/applications/${application.id}` : "/fama/qr"}>
                <Card className="flex items-center justify-between">
                  <div>
                    <p className="font-semibold">{qr.qrCode}</p>
                    <p className="text-xs text-muted">{company?.name}</p>
                  </div>
                  <StatusBadge qr={qr.status} />
                </Card>
              </Link>
            </li>
          );
        })}
      </ul>
    </div>
  );
}
