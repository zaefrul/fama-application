import { Button, ErrorText, Field, Input } from "@/components/ui";
import type { Company } from "@/domain/types";

export function FamaCompanyForm({
  action,
  company,
  error,
  submitLabel,
}: {
  action: (formData: FormData) => void | Promise<void>;
  company?: Company;
  error?: string;
  submitLabel: string;
}) {
  const famaSourced = !company || company.externalSource === "FAMA";

  return (
    <form action={action} className="grid gap-3 md:grid-cols-2">
      <Field label="No. Pendaftaran" required>
        <Input name="registrationNo" defaultValue={company?.registrationNo} readOnly={!famaSourced} required={famaSourced} />
      </Field>
      <Field label="Nama Syarikat" required>
        <Input name="name" defaultValue={company?.name} readOnly={!famaSourced} required={famaSourced} />
      </Field>
      <Field label="Alamat" required>
        <Input name="address" defaultValue={company?.address} required />
      </Field>
      <Field label="Negeri">
        <Input name="state" defaultValue={company?.state} />
      </Field>
      <Field label="Daerah">
        <Input name="district" defaultValue={company?.district} />
      </Field>
      <Field label="Poskod">
        <Input name="postcode" defaultValue={company?.postcode} />
      </Field>
      <Field label="No. Telefon">
        <Input name="phone" defaultValue={company?.phone} />
      </Field>
      <Field label="Emel">
        <Input name="email" type="email" defaultValue={company?.email} />
      </Field>
      <Field label="Laman Web">
        <Input name="website" defaultValue={company?.website} />
      </Field>
      <div className="md:col-span-2">
        <ErrorText>{error}</ErrorText>
      </div>
      <div className="md:col-span-2">
        <Button type="submit">{submitLabel}</Button>
      </div>
    </form>
  );
}
