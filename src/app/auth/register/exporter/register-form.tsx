"use client";

import { useState } from "react";
import { registerExporterAction } from "@/app/actions/auth";
import { Button, Card, ErrorText, Field, Input } from "@/components/ui";

type Lookup = {
  identifier: string;
  name: string;
  email: string;
  status: string;
};

export function ExporterRegisterForm({ error }: { error?: string }) {
  const [step, setStep] = useState(1);
  const [identifier, setIdentifier] = useState("");
  const [lookupError, setLookupError] = useState<string | null>(
    error === "notfound" ? "Tiada rekod dijumpai" : null,
  );
  const [company, setCompany] = useState<Lookup | null>(null);

  async function lookup() {
    setLookupError(null);
    const response = await fetch(`/api/integrations/dagangnet/company/${encodeURIComponent(identifier)}`);
    if (!response.ok) {
      setLookupError("Tiada rekod dijumpai");
      return;
    }
    const data = (await response.json()) as Lookup;
    setCompany(data);
    setStep(2);
  }

  return (
    <Card>
      <p className="mb-2 text-xs font-semibold text-muted">Pendaftaran Pengeksport · Langkah {step} / 4</p>
      <div className="mb-4 grid grid-cols-4 gap-1">
        {[1, 2, 3, 4].map((value) => (
          <div key={value} className={`h-1.5 rounded-full ${value <= step ? "bg-brand" : "bg-border"}`} />
        ))}
      </div>

      {step === 1 ? (
        <div className="space-y-4">
          <Field label="Nombor Akaun" hint="Carian mock DagangNet. Cuba H0B00001" required>
            <Input value={identifier} onChange={(event) => setIdentifier(event.target.value)} />
          </Field>
          <ErrorText>{lookupError}</ErrorText>
          <Button type="button" onClick={lookup} className="w-full">
            Seterusnya
          </Button>
        </div>
      ) : null}

      {step === 2 && company ? (
        <div className="space-y-3">
          <h2 className="font-semibold">Maklumat Syarikat</h2>
          <Field label="Nama Syarikat">
            <Input readOnly value={company.name} />
          </Field>
          <Field label="Emel syarikat">
            <Input readOnly value={company.email} />
          </Field>
          <Field label="Status">
            <Input readOnly value={company.status} />
          </Field>
          <Button type="button" className="w-full" onClick={() => setStep(3)}>
            Seterusnya
          </Button>
        </div>
      ) : null}

      {step >= 3 && company ? (
        <form action={registerExporterAction} className="space-y-3">
          <input type="hidden" name="identifier" value={identifier} />
          {step === 3 ? (
            <>
              <h2 className="font-semibold">Maklumat Pengguna</h2>
              <Field label="No Kad Pengenalan Pengguna" required>
                <Input name="identityReference" required defaultValue="660113021111" />
              </Field>
              <Field label="Nama Pengguna" required>
                <Input name="name" required defaultValue="Ali bin Abu" />
              </Field>
              <Button type="button" className="w-full" onClick={() => setStep(4)}>
                Seterusnya
              </Button>
            </>
          ) : (
            <>
              <h2 className="font-semibold">Kata Laluan</h2>
              <Field label="No Kad Pengenalan Pengguna" required>
                <Input name="identityReference" required defaultValue="660113021111" />
              </Field>
              <Field label="Nama Pengguna" required>
                <Input name="name" required defaultValue="Ali bin Abu" />
              </Field>
              <Field label="Kata Laluan" required>
                <Input name="password" type="password" required minLength={8} />
              </Field>
              <Field label="Sahkan Kata Laluan" required>
                <Input name="confirmPassword" type="password" required minLength={8} />
              </Field>
              {error === "validation" ? <ErrorText>Sila semak kata laluan dan maklumat pengguna.</ErrorText> : null}
              <Button type="submit" className="w-full">
                Daftar
              </Button>
            </>
          )}
        </form>
      ) : null}
    </Card>
  );
}
