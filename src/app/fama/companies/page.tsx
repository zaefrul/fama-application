import Link from "next/link";
import { Card, Input, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function FamaCompaniesPage() {
  await requireRole("FAMA_OFFICER");
  const companies = await getRepositories().listCompanies();

  return (
    <div className="space-y-4">
      <PageTitle title="Senarai Syarikat" />
      <Input placeholder="Carian" name="q" readOnly />
      <ul className="space-y-2">
        {companies.map((company) => (
          <li key={company.id}>
            <Link href={`/fama/companies/${company.id}`}>
              <Card className="flex items-center justify-between">
                <div>
                  <p className="text-xs text-muted">{company.registrationNo}</p>
                  <p className="font-semibold">{company.name}</p>
                </div>
                <span className="text-brand">✎</span>
              </Card>
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
