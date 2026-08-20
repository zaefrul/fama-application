import { Breadcrumb, Button, Card, Field, Input, ProgressSteps, Select, Textarea } from "@/components/ui";
import type { Certificate, ExportApplication, ProduceType } from "@/domain/types";

export function ApplicationForm({
  action,
  application,
  produceTypes,
  certificates,
  editable = false,
  primaryLabel = "Seterusnya",
  hideSecondary = false,
}: {
  action: (formData: FormData) => void | Promise<void>;
  application?: ExportApplication;
  produceTypes: ProduceType[];
  certificates: Certificate[];
  editable?: boolean;
  primaryLabel?: string;
  hideSecondary?: boolean;
}) {
  const readOnly = editable ? false : application ? application.status !== "DRAFT" : false;

  return (
    <div className="space-y-4">
      <Breadcrumb items={["Senarai QR", "Maklumat Eksport"]} />
      <ProgressSteps current={2} total={2} />
      <Card className="px-5 py-5">
        <form action={action} className="grid gap-4">
          <section className="grid gap-3">
            <h2 className="text-sm font-bold text-brand">Maklumat Keluaran</h2>
            <Field label="Jenis Keluaran Pertanian" required>
              <Select name="produceTypeId" defaultValue={application?.produceTypeId} disabled={readOnly} required>
                {produceTypes.map((type) => (
                  <option key={type.id} value={type.id}>
                    {type.name}
                  </option>
                ))}
              </Select>
            </Field>
            <Field label="Varieti" required>
              <Input name="variety" defaultValue={application?.variety} readOnly={readOnly} required />
            </Field>
            <div className="grid grid-cols-2 gap-3">
              <Field label="Gred" required>
                <Input name="grade" defaultValue={application?.grade} readOnly={readOnly} required />
              </Field>
              <Field label="Saiz">
                <Input name="size" defaultValue={application?.size} readOnly={readOnly} />
              </Field>
            </div>
            <Field label="Bilangan Eksport / Berat (kg)" required>
              <Input name="quantity" type="number" defaultValue={application?.quantity} readOnly={readOnly} required />
            </Field>
            <Field label="Destinasi" required>
              <Input name="destinationCountry" defaultValue={application?.destinationCountry} readOnly={readOnly} required />
            </Field>
            <Field label="No Sijil CoC" required>
              <Select name="cocCertificateId" defaultValue={application?.cocCertificateId ?? ""} disabled={readOnly}>
                <option value="">—</option>
                {certificates
                  .filter((certificate) => certificate.type === "CoC")
                  .map((certificate) => (
                    <option key={certificate.id} value={certificate.id}>
                      {certificate.certificateNo}
                    </option>
                  ))}
              </Select>
            </Field>
            <input type="hidden" name="cocNumber" defaultValue={application?.cocNumber} />
          </section>

          <section className="grid gap-3 border-t border-border pt-4">
            <h2 className="text-sm font-bold text-brand">Maklumat Eksport</h2>
            <Field label="Tarikh Eksport" required>
              <Input name="exportDate" type="date" defaultValue={application?.exportDate} readOnly={readOnly} required />
            </Field>
            <Field label="Nama Ladang" required>
              <Input name="farmName" defaultValue={application?.farmName} readOnly={readOnly} required />
            </Field>
            <Field label="Pengimport" required>
              <Input name="importerName" defaultValue={application?.importerName} readOnly={readOnly} required />
            </Field>
            <Field label="Alamat Pengimport" required>
              <Textarea name="importerAddress" defaultValue={application?.importerAddress} readOnly={readOnly} required />
            </Field>
          </section>

          {!readOnly ? (
            <div className="sticky bottom-20 flex gap-2 bg-white/90 py-2 md:bottom-0">
              {hideSecondary ? null : (
                <Button type="submit" variant="secondary" className="flex-1">
                  Simpan
                </Button>
              )}
              <Button type="submit" className="flex-1">
                {primaryLabel}
              </Button>
            </div>
          ) : (
            <p className="text-sm text-muted">Permohonan ini bukan draf dan tidak boleh dikemaskini.</p>
          )}
        </form>
      </Card>
    </div>
  );
}
