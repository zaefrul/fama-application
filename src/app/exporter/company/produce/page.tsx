import { addProduceAction, removeProduceAction } from "@/app/actions/exporter";
import { CompanyNav } from "@/components/nav";
import { Button, Card, PageTitle, Select } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function ProducePage() {
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const types = await repos.listProduceTypes();
  const produce = session.companyId ? await repos.listCompanyProduce(session.companyId) : [];

  return (
    <div className="space-y-4">
      <PageTitle title="Maklumat Keluaran Pertanian" />
      <CompanyNav />
      <Card>
        <form action={addProduceAction} className="flex gap-2">
          <Select name="produceTypeId" className="flex-1">
            {types.map((type) => (
              <option key={type.id} value={type.id}>
                {type.name}
              </option>
            ))}
          </Select>
          <Button type="submit">+ Tambah</Button>
        </form>
        <ul className="mt-4 space-y-2">
          {produce.map((row) => (
            <li key={row.id} className="flex items-center justify-between rounded-xl border border-border px-3 py-2">
              <span className="font-semibold">{produceName(types, row.produceTypeId)}</span>
              <form action={removeProduceAction}>
                <input type="hidden" name="id" value={row.id} />
                <Button type="submit" variant="danger">
                  Buang
                </Button>
              </form>
            </li>
          ))}
        </ul>
      </Card>
    </div>
  );
}
