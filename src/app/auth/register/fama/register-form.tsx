"use client";

import { useState } from "react";
import { registerFamaAction } from "@/app/actions/auth";
import { Button, Card, ErrorText, Field, Input } from "@/components/ui";

type Staff = {
  identifier: string;
  fullName: string;
  email: string;
  position: string;
};

export function FamaRegisterForm({ error }: { error?: string }) {
  const [step, setStep] = useState(1);
  const [identifier, setIdentifier] = useState("");
  const [lookupError, setLookupError] = useState<string | null>(
    error === "notfound" ? "Tiada rekod dijumpai" : null,
  );
  const [staff, setStaff] = useState<Staff | null>(null);

  async function lookup() {
    setLookupError(null);
    const response = await fetch(`/api/integrations/ifama/staff/${encodeURIComponent(identifier)}`);
    if (!response.ok) {
      setLookupError("Tiada rekod dijumpai");
      return;
    }
    setStaff((await response.json()) as Staff);
    setStep(2);
  }

  return (
    <Card>
      <p className="mb-2 text-xs font-semibold text-muted">Pendaftaran Pegawai FAMA · Langkah {step} / 3</p>
      <div className="mb-4 grid grid-cols-3 gap-1">
        {[1, 2, 3].map((value) => (
          <div key={value} className={`h-1.5 rounded-full ${value <= step ? "bg-brand" : "bg-border"}`} />
        ))}
      </div>

      {step === 1 ? (
        <div className="space-y-4">
          <Field label="Nombor Kad Pengenalan" hint="Carian mock iFAMA. Cuba 770101145533" required>
            <Input value={identifier} onChange={(event) => setIdentifier(event.target.value)} />
          </Field>
          <ErrorText>{lookupError}</ErrorText>
          <Button type="button" className="w-full" onClick={lookup}>
            Seterusnya
          </Button>
        </div>
      ) : null}

      {step === 2 && staff ? (
        <div className="space-y-3">
          <h2 className="font-semibold">Maklumat Pegawai</h2>
          <Field label="Nama Penuh">
            <Input readOnly value={staff.fullName} />
          </Field>
          <Field label="Emel">
            <Input readOnly value={staff.email} />
          </Field>
          <Field label="Jawatan">
            <Input readOnly value={staff.position} />
          </Field>
          <Button type="button" className="w-full" onClick={() => setStep(3)}>
            Seterusnya
          </Button>
        </div>
      ) : null}

      {step === 3 && staff ? (
        <form action={registerFamaAction} className="space-y-3">
          <input type="hidden" name="identifier" value={identifier} />
          <Field label="Kata Laluan" required>
            <Input name="password" type="password" required minLength={8} />
          </Field>
          <Field label="Sahkan Kata Laluan" required>
            <Input name="confirmPassword" type="password" required minLength={8} />
          </Field>
          {error === "validation" ? <ErrorText>Sila semak kata laluan.</ErrorText> : null}
          <Button type="submit" className="w-full">
            Daftar
          </Button>
        </form>
      ) : null}
    </Card>
  );
}
