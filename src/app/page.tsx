import { redirect } from "next/navigation";
import { getSession } from "@/lib/session";

export default async function HomePage() {
  const session = await getSession();
  if (session?.role === "FAMA_OFFICER") redirect("/fama");
  if (session?.role === "EXPORTER") redirect("/exporter");
  redirect("/auth/login");
}
