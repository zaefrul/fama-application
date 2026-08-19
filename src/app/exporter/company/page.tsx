import { updateCompanyAction } from "@/app/actions/exporter";
import { CompanyNav } from "@/components/nav";
import { Button, Card, ErrorText, Field, Input, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function CompanyPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const session = await requireRole("EXPORTER");
  const { error } = await searchParams;
  const company = session.companyId ? await getRepositories().getCompany(session.companyId) : null;
  if (!company) return <p>Syarikat tidak dijumpai.</p>;

  return (
    <div>
      <PageTitle title="Maklumat Syarikat" subtitle="Medan dari DagangNet adalah baca sahaja." />
      <CompanyNav />
      <Card>
        <form action={updateCompanyAction} className="grid gap-3 md:grid-cols-2">
          <Field label="No. Pendaftaran">
            <Input readOnly defaultValue={company.registrationNo} />
          </Field>
          <Field label="Nama Syarikat">
            <Input readOnly defaultValue={company.name} />
          </Field>
          <Field label="Alamat" required>
            <Input name="address" defaultValue={company.address} />
          </Field>
          <Field label="Negeri">
            <Input name="state" defaultValue={company.state} />
          </Field>
          <Field label="Daerah">
            <Input name="district" defaultValue={company.district} />
          </Field>
          <Field label="Poskod">
            <Input name="postcode" defaultValue={company.postcode} />
          </Field>
          <Field label="No. Telefon">
            <Input name="phone" defaultValue={company.phone} />
          </Field>
          <Field label="Emel">
            <Input name="email" type="email" defaultValue={company.email} />
          </Field>
          <Field label="Laman Web">
            <Input name="website" defaultValue={company.website} />
          </Field>
          <Field label="Logo Syarikat">
            <Input name="logo" type="file" accept="image/jpeg,image/png,image/webp" />
          </Field>
          {company.logoPath ? (
            <div>
              <p className="mb-1 text-sm font-medium">Logo semasa</p>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={company.logoPath} alt={company.name} className="h-16 w-16 rounded-xl object-contain bg-surface-muted p-1" />
            </div>
          ) : null}
          <div className="md:col-span-2">
            <ErrorText>{error}</ErrorText>
          </div>
          <div className="md:col-span-2 flex gap-2">
            <Button type="submit">Simpan</Button>
            <a href="/exporter/company/produce" className="inline-flex items-center text-sm font-semibold text-brand">
              Seterusnya
            </a>
          </div>
        </form>
      </Card>
    </div>
  );
}
