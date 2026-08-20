"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import type { CertificateType } from "@/domain/types";
import { requireRole } from "@/lib/session";
import { saveUpload } from "@/lib/upload";
import { getRepositories } from "@/repositories";
import type { ApplicationInput } from "@/repositories/contracts";

function applicationInputFromForm(companyId: string, formData: FormData): ApplicationInput {
  return {
    companyId,
    produceTypeId: String(formData.get("produceTypeId") ?? ""),
    variety: String(formData.get("variety") ?? ""),
    grade: String(formData.get("grade") ?? ""),
    size: String(formData.get("size") ?? ""),
    quantity: Number(formData.get("quantity") ?? 0),
    quantityUnit: "kg",
    destinationCountry: String(formData.get("destinationCountry") ?? ""),
    cocCertificateId: String(formData.get("cocCertificateId") ?? "") || null,
    cocNumber: String(formData.get("cocNumber") ?? ""),
    exportDate: String(formData.get("exportDate") ?? ""),
    farmName: String(formData.get("farmName") ?? ""),
    importerName: String(formData.get("importerName") ?? ""),
    importerAddress: String(formData.get("importerAddress") ?? ""),
  };
}

function companyFieldsFromForm(formData: FormData) {
  return {
    name: String(formData.get("name") ?? ""),
    registrationNo: String(formData.get("registrationNo") ?? ""),
    address: String(formData.get("address") ?? ""),
    state: String(formData.get("state") ?? ""),
    district: String(formData.get("district") ?? ""),
    postcode: String(formData.get("postcode") ?? ""),
    phone: String(formData.get("phone") ?? ""),
    email: String(formData.get("email") ?? ""),
    website: String(formData.get("website") ?? ""),
  };
}

export async function startReviewAction(id: string) {
  const session = await requireRole("FAMA_OFFICER");
  await getRepositories().startReview(id, session);
  revalidatePath(`/fama/applications/${id}`);
}

export async function approveAction(id: string, formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  await getRepositories().approveApplication(id, session, String(formData.get("remarks") ?? ""));
  revalidatePath("/fama/applications");
  redirect(`/fama/applications/${id}`);
}

export async function rejectAction(id: string, formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  const remarks = String(formData.get("remarks") ?? "").trim();
  if (!remarks) {
    redirect(`/fama/applications/${id}?error=remarks`);
  }
  await getRepositories().rejectApplication(id, session, remarks);
  revalidatePath("/fama/applications");
  redirect(`/fama/applications/${id}`);
}

export async function createCompanyAction(formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  let companyId = "";
  try {
    const company = await getRepositories().createCompany(companyFieldsFromForm(formData), session);
    companyId = company.id;
  } catch (error) {
    redirect(`/fama/companies/new?error=${encodeURIComponent((error as Error).message)}`);
  }
  revalidatePath("/fama/companies");
  redirect(`/fama/companies/${companyId}`);
}

export async function updateManagedCompanyAction(companyId: string, formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  try {
    await getRepositories().updateManagedCompany(companyId, companyFieldsFromForm(formData), session);
    revalidatePath(`/fama/companies/${companyId}`);
    revalidatePath("/fama/companies");
  } catch (error) {
    redirect(`/fama/companies/${companyId}?error=${encodeURIComponent((error as Error).message)}`);
  }
}

export async function createAndActivateQrAction(companyId: string, formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  let applicationId = "";
  try {
    const result = await getRepositories().createAndActivateQr(
      companyId,
      applicationInputFromForm(companyId, formData),
      session,
    );
    applicationId = result.application.id;
  } catch (error) {
    redirect(`/fama/companies/${companyId}/qr/new?error=${encodeURIComponent((error as Error).message)}`);
  }
  revalidatePath(`/fama/companies/${companyId}`);
  revalidatePath("/fama/qr");
  revalidatePath("/fama");
  redirect(`/fama/companies/${companyId}/qr/${applicationId}`);
}

export async function updateManagedApplicationAction(companyId: string, applicationId: string, formData: FormData) {
  const session = await requireRole("FAMA_OFFICER");
  try {
    await getRepositories().updateManagedApplication(
      applicationId,
      applicationInputFromForm(companyId, formData),
      session,
    );
  } catch (error) {
    redirect(
      `/fama/companies/${companyId}/qr/${applicationId}?error=${encodeURIComponent((error as Error).message)}`,
    );
  }
  const qrs = await getRepositories().listQrCodes({ companyId });
  const qr = qrs.find((row) => row.applicationId === applicationId);
  revalidatePath(`/fama/companies/${companyId}/qr/${applicationId}`);
  revalidatePath(`/fama/companies/${companyId}`);
  if (qr) revalidatePath(`/trace/${qr.qrCode}`);
  redirect(`/fama/companies/${companyId}/qr/${applicationId}?saved=1`);
}

export async function addManagedCertificateAction(companyId: string, formData: FormData) {
  await requireRole("FAMA_OFFICER");
  let documentPath: string | null = null;
  try {
    documentPath = await saveUpload(formData.get("document") as File | null, "certificates", {
      allowPdf: true,
      required: true,
    });
  } catch (error) {
    redirect(`/fama/companies/${companyId}?error=${encodeURIComponent((error as Error).message)}`);
  }
  await getRepositories().addCertificate({
    companyId,
    type: String(formData.get("type") ?? "CoC") as CertificateType,
    certificateNo: String(formData.get("certificateNo") ?? ""),
    documentPath: documentPath ?? "/placeholders/certificate-coc.svg",
    issueDate: String(formData.get("issueDate") ?? new Date().toISOString().slice(0, 10)),
    expiryDate: String(formData.get("expiryDate") ?? "") || null,
    status: "ACTIVE",
  });
  revalidatePath(`/fama/companies/${companyId}`);
}

export async function deleteManagedCertificateAction(companyId: string, formData: FormData) {
  await requireRole("FAMA_OFFICER");
  await getRepositories().deleteCertificate(String(formData.get("id") ?? ""));
  revalidatePath(`/fama/companies/${companyId}`);
}
