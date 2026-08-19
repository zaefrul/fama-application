import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Card, PageTitle } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function QrListPage() {
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const qrs = session.companyId ? await repos.listQrCodes({ companyId: session.companyId }) : [];
  const applications = session.companyId ? await repos.listApplications({ companyId: session.companyId }) : [];
  const produceTypes = await repos.listProduceTypes();

  return (
    <div className="space-y-4">
      <PageTitle title="Senarai QR" />
      <ul className="space-y-2">
        {qrs.map((qr) => {
          const application = applications.find((row) => row.id === qr.applicationId);
          return (
            <li key={qr.id}>
              <Link href={`/exporter/qr/${qr.id}`}>
                <Card className="flex items-center justify-between">
                  <div>
                    <p className="font-semibold">{qr.qrCode}</p>
                    <p className="text-xs text-muted">
                      {application ? produceName(produceTypes, application.produceTypeId) : "—"}
                    </p>
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
