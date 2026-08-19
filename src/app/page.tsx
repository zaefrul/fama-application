import { redirect } from "next/navigation";
import { getSession } from "@/lib/session";

function isNextRedirect(error: unknown) {
  return (
    typeof error === "object" &&
    error !== null &&
    "digest" in error &&
    String((error as { digest?: string }).digest).startsWith("NEXT_REDIRECT")
  );
}

export default async function HomePage() {
  try {
    const session = await getSession();
    if (session?.role === "FAMA_OFFICER") redirect("/fama");
    if (session?.role === "EXPORTER") redirect("/exporter");
  } catch (error) {
    if (isNextRedirect(error)) throw error;
    const message = error instanceof Error ? error.message : String(error);
    if (message.includes("Dynamic server usage") || message.includes("couldn't be rendered statically")) {
      throw error;
    }
    return (
      <main className="mx-auto max-w-2xl p-6">
        <h1 className="mb-3 text-lg font-bold">Sistem Jejak GPL — ralat pelayan</h1>
        <pre className="overflow-auto rounded-lg bg-zinc-100 p-4 text-sm">{message}</pre>
        <p className="mt-3 text-sm text-muted">
          In Plesk Node.js, set AUTH_SECRET, AUTH_URL=https://jejakgpl.metadatasystem.my,
          DATABASE_URL, and DATA_SOURCE=prisma, then Restart App.
        </p>
      </main>
    );
  }
  redirect("/auth/login");
}
