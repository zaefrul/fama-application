"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

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
