import { Card, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function FamaAuditPage() {
  await requireRole("FAMA_OFFICER");
  const logs = await getRepositories().listAudit();

  return (
    <div className="space-y-4">
      <PageTitle title="Jejak Audit" />
      <ul className="space-y-2">
        {logs.map((log) => (
          <li key={log.id}>
            <Card>
              <p className="text-sm font-semibold">{log.action}</p>
              <p className="text-xs text-muted">
                {log.actorRole} · {log.objectType} · {new Date(log.createdAt).toLocaleString("ms-MY")}
              </p>
              {log.remarks ? <p className="text-sm">{log.remarks}</p> : null}
            </Card>
          </li>
        ))}
      </ul>
    </div>
  );
}
