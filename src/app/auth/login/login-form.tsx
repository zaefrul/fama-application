"use client";

import { useState } from "react";
import Link from "next/link";
import { loginAction } from "@/app/actions/auth";
import { Button, ErrorText, Field, Input } from "@/components/ui";

export function LoginForm({
  error,
  defaultRole,
}: {
  error: string | null;
  defaultRole?: string;
}) {
  const [showPassword, setShowPassword] = useState(false);
  const exporterSelected = defaultRole !== "FAMA_OFFICER";

  return (
    <form action={loginAction} className="space-y-5">
      <fieldset>
        <legend className="mb-2 text-sm font-medium">Log masuk sebagai</legend>
        <div className="grid grid-cols-2 rounded-2xl bg-surface-muted p-1">
          <label className="relative cursor-pointer text-center text-sm font-semibold">
            <input
              type="radio"
              name="role"
              value="FAMA_OFFICER"
              defaultChecked={!exporterSelected}
              className="peer absolute inset-0 z-10 cursor-pointer opacity-0"
            />
            <span className="pointer-events-none block rounded-xl px-3 py-2 text-muted peer-checked:bg-white peer-checked:text-brand peer-checked:shadow-sm">
              FAMA
            </span>
          </label>
          <label className="relative cursor-pointer text-center text-sm font-semibold">
            <input
              type="radio"
              name="role"
              value="EXPORTER"
              defaultChecked={exporterSelected}
              className="peer absolute inset-0 z-10 cursor-pointer opacity-0"
            />
            <span className="pointer-events-none block rounded-xl px-3 py-2 text-muted peer-checked:bg-white peer-checked:text-brand peer-checked:shadow-sm">
              PENGEKSPORT
            </span>
          </label>
        </div>
      </fieldset>

      <Field label="Emel" required>
        <div className="relative">
          <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">✉</span>
          <Input name="email" type="email" required placeholder="nama@contoh.com" className="pl-9" />
        </div>
      </Field>
      <Field label="Kata Laluan" required>
        <div className="relative">
          <Input name="password" type={showPassword ? "text" : "password"} required className="pr-12" />
          <button
            type="button"
            className="absolute inset-y-0 right-3 text-xs font-semibold text-muted"
            onClick={() => setShowPassword((value) => !value)}
          >
            {showPassword ? "Hide" : "Lihat"}
          </button>
        </div>
      </Field>
      <ErrorText>{error}</ErrorText>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <p className="text-sm">
          <Link href="/auth/register/exporter" className="font-semibold text-brand">
            Daftar
          </Link>
          <span className="text-muted"> | </span>
          <Link href="/auth/forgot-password" className="font-semibold text-brand">
            Lupa kata laluan
          </Link>
        </p>
        <Button type="submit" className="w-full sm:w-auto sm:min-w-28">
          Log Masuk
        </Button>
      </div>
      <p className="text-center text-xs text-muted">
        Pegawai FAMA?{" "}
        <Link href="/auth/register/fama" className="font-semibold text-brand">
          Daftar FAMA
        </Link>
      </p>
    </form>
  );
}
