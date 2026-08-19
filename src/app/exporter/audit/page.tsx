import { Card, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function ExporterAuditPage() {
  const session = await requireRole("EXPORTER");
  const logs = session.companyId ? await getRepositories().listAudit({ companyId: session.companyId }) : [];

  return (
    <div className="space-y-4">
      <PageTitle title="Jejak Audit" />
      <ul className="space-y-2">
        {logs.map((log) => (
          <li key={log.id}>
            <Card>
              <p className="text-sm font-semibold">{log.action}</p>
              <p className="text-xs text-muted">
                {log.objectType} · {new Date(log.createdAt).toLocaleString("ms-MY")}
              </p>
              {log.remarks ? <p className="text-sm">{log.remarks}</p> : null}
            </Card>
          </li>
        ))}
      </ul>
    </div>
  );
}
