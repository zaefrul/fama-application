"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";
import { saveUpload } from "@/lib/upload";
import type { CertificateType, GalleryCategory } from "@/domain/types";

async function companyId() {
  const session = await requireRole("EXPORTER");
  if (!session.companyId) throw new Error("Akaun pengeksport tiada syarikat");
  return { session, companyId: session.companyId };
}

export async function updateCompanyAction(formData: FormData) {
  const { companyId: id } = await companyId();
  let logoPath: string | null = null;
  try {
    logoPath = await saveUpload(formData.get("logo") as File | null, "logos");
  } catch (error) {
    redirect(`/exporter/company?error=${encodeURIComponent((error as Error).message)}`);
  }
  await getRepositories().updateCompany(id, {
    address: String(formData.get("address") ?? ""),
    state: String(formData.get("state") ?? ""),
    district: String(formData.get("district") ?? ""),
    postcode: String(formData.get("postcode") ?? ""),
    phone: String(formData.get("phone") ?? ""),
    email: String(formData.get("email") ?? ""),
    website: String(formData.get("website") ?? ""),
    ...(logoPath ? { logoPath } : {}),
  });
  revalidatePath("/exporter/company");
}

export async function addProduceAction(formData: FormData) {
  const { companyId: id } = await companyId();
  await getRepositories().addCompanyProduce(id, String(formData.get("produceTypeId") ?? ""));
  revalidatePath("/exporter/company/produce");
}

export async function removeProduceAction(formData: FormData) {
  await requireRole("EXPORTER");
  await getRepositories().removeCompanyProduce(String(formData.get("id") ?? ""));
  revalidatePath("/exporter/company/produce");
}

export async function addCertificateAction(formData: FormData) {
  const { companyId: id } = await companyId();
  let documentPath: string | null = null;
  try {
    documentPath = await saveUpload(formData.get("document") as File | null, "certificates", {
      allowPdf: true,
      required: true,
    });
  } catch (error) {
    redirect(`/exporter/company/certificates?error=${encodeURIComponent((error as Error).message)}`);
  }
  await getRepositories().addCertificate({
    companyId: id,
    type: String(formData.get("type") ?? "CoC") as CertificateType,
    certificateNo: String(formData.get("certificateNo") ?? ""),
    documentPath: documentPath ?? "/placeholders/certificate-coc.svg",
    issueDate: String(formData.get("issueDate") ?? new Date().toISOString().slice(0, 10)),
    expiryDate: String(formData.get("expiryDate") ?? "") || null,
    status: "ACTIVE",
  });
  revalidatePath("/exporter/company/certificates");
  revalidatePath("/exporter/company");
}

export async function deleteCertificateAction(formData: FormData) {
  await requireRole("EXPORTER");
  await getRepositories().deleteCertificate(String(formData.get("id") ?? ""));
  revalidatePath("/exporter/company/certificates");
}

export async function addGalleryAction(formData: FormData) {
  const { session, companyId: id } = await companyId();
  let filePath: string | null = null;
  try {
    filePath = await saveUpload(formData.get("image") as File | null, "gallery", { required: true });
  } catch (error) {
    redirect(`/exporter/company/gallery?error=${encodeURIComponent((error as Error).message)}`);
  }
  await getRepositories().addGalleryItem({
    companyId: id,
    category: String(formData.get("category") ?? "BUAH") as GalleryCategory,
    description: String(formData.get("description") ?? ""),
    filePath: filePath ?? "/placeholders/gallery-buah.svg",
    uploadedBy: session.id,
    uploadedAt: new Date().toISOString(),
  });
  revalidatePath("/exporter/company/gallery");
  revalidatePath("/exporter");
}

export async function deleteGalleryAction(formData: FormData) {
  await requireRole("EXPORTER");
  await getRepositories().deleteGalleryItem(String(formData.get("id") ?? ""));
  revalidatePath("/exporter/company/gallery");
}

export async function createApplicationAction(formData: FormData) {
  const { session, companyId: id } = await companyId();
  const repos = getRepositories();
  const application = await repos.createApplication({
    companyId: id,
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
  });
  await repos.generateQr(application.id, session);
  revalidatePath("/exporter/applications");
  redirect(`/exporter/applications/${application.id}`);
}

export async function updateApplicationAction(id: string, formData: FormData) {
  const { session } = await companyId();
  const repos = getRepositories();
  await repos.updateApplication(id, {
    produceTypeId: String(formData.get("produceTypeId") ?? ""),
    variety: String(formData.get("variety") ?? ""),
    grade: String(formData.get("grade") ?? ""),
    size: String(formData.get("size") ?? ""),
    quantity: Number(formData.get("quantity") ?? 0),
    destinationCountry: String(formData.get("destinationCountry") ?? ""),
    cocCertificateId: String(formData.get("cocCertificateId") ?? "") || null,
    cocNumber: String(formData.get("cocNumber") ?? ""),
    exportDate: String(formData.get("exportDate") ?? ""),
    farmName: String(formData.get("farmName") ?? ""),
    importerName: String(formData.get("importerName") ?? ""),
    importerAddress: String(formData.get("importerAddress") ?? ""),
  });
  await repos.generateQr(id, session);
  revalidatePath(`/exporter/applications/${id}`);
  redirect(`/exporter/applications/${id}`);
}

export async function submitApplicationAction(id: string) {
  const { session } = await companyId();
  await getRepositories().submitApplication(id, session);
  revalidatePath("/exporter/applications");
  redirect(`/exporter/applications/${id}`);
}

export async function generateQrAction(id: string) {
  const { session } = await companyId();
  await getRepositories().generateQr(id, session);
  revalidatePath("/exporter/qr");
}
