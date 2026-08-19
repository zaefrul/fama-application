"use server";

import { redirect } from "next/navigation";
import bcrypt from "bcryptjs";
import { signIn, signOut } from "@/auth";
import { getCompanyRegistry, getStaffDirectory } from "@/integrations";
import { clearSessionCookie, setSessionCookie } from "@/lib/session";
import { getRepositories } from "@/repositories";

async function passwordsMatch(stored: string, incoming: string) {
  if (stored.startsWith("$2")) return bcrypt.compare(incoming, stored);
  return stored === incoming;
}

export async function loginAction(formData: FormData) {
  const email = String(formData.get("email") ?? "").trim();
  const password = String(formData.get("password") ?? "");
  const role = String(formData.get("role") ?? "");
  const repos = getRepositories();
  const user = await repos.findUserByEmail(email);

  if (!user || !(await passwordsMatch(user.password, password))) {
    redirect("/auth/login?error=invalid");
  }
  if (role && user.role !== role) {
    redirect("/auth/login?error=role");
  }

  await signIn("credentials", { email, password, redirect: false });
  await setSessionCookie(user.id);
  redirect(user.role === "FAMA_OFFICER" ? "/fama" : "/exporter");
}

export async function logoutAction() {
  await clearSessionCookie();
  await signOut({ redirect: false });
  redirect("/auth/login");
}

export async function registerExporterAction(formData: FormData) {
  const identifier = String(formData.get("identifier") ?? "").trim();
  const name = String(formData.get("name") ?? "").trim();
  const identityReference = String(formData.get("identityReference") ?? "").trim();
  const password = String(formData.get("password") ?? "");
  const confirm = String(formData.get("confirmPassword") ?? "");

  if (!name || !identityReference || password.length < 8 || password !== confirm) {
    redirect("/auth/register/exporter?error=validation");
  }

  const company = await getCompanyRegistry().findCompany(identifier);
  if (!company) {
    redirect("/auth/register/exporter?error=notfound");
  }

  const repos = getRepositories();
  const companies = await repos.listCompanies();
  const match = companies.find((row) => row.externalAccountNo === identifier);
  if (!match) {
    redirect("/auth/register/exporter?error=notfound");
  }

  const user = await repos.createExporterUser({
    name,
    email: company.email,
    password,
    identityReference,
    companyId: match.id,
  });
  await setSessionCookie(user.id);
  redirect("/exporter");
}

export async function registerFamaAction(formData: FormData) {
  const identifier = String(formData.get("identifier") ?? "").trim();
  const password = String(formData.get("password") ?? "");
  const confirm = String(formData.get("confirmPassword") ?? "");

  if (password.length < 8 || password !== confirm) {
    redirect("/auth/register/fama?error=validation");
  }

  const staff = await getStaffDirectory().findStaff(identifier);
  if (!staff) {
    redirect("/auth/register/fama?error=notfound");
  }

  const repos = getRepositories();
  const existing = await repos.findUserByEmail(staff.email);
  if (existing) {
    await setSessionCookie(existing.id);
    redirect("/fama");
  }

  const user = await repos.createFamaUser({
    name: staff.fullName,
    email: staff.email,
    password,
    identityReference: identifier,
  });
  await setSessionCookie(user.id);
  redirect("/fama");
}
