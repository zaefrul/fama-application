import { auth } from "@/auth";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import type { Role, SessionUser } from "@/domain/types";
import { getRepositories } from "@/repositories";

const COOKIE = "jejak_session";

export async function getSession(): Promise<SessionUser | null> {
  const nextAuth = await auth();
  if (nextAuth?.user?.id && nextAuth.user.role) {
    return {
      id: nextAuth.user.id,
      name: nextAuth.user.name ?? "",
      email: nextAuth.user.email ?? "",
      role: nextAuth.user.role,
      companyId: nextAuth.user.companyId,
    };
  }

  const store = await cookies();
  const userId = store.get(COOKIE)?.value;
  if (!userId) return null;
  const user = await getRepositories().findUserById(userId);
  if (!user) return null;
  return {
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    companyId: user.companyId,
  };
}

export async function requireSession(): Promise<SessionUser> {
  const session = await getSession();
  if (!session) redirect("/auth/login");
  return session;
}

export async function requireRole(role: Role): Promise<SessionUser> {
  const session = await requireSession();
  if (session.role !== role) {
    redirect(session.role === "FAMA_OFFICER" ? "/fama" : "/exporter");
  }
  return session;
}

export async function setSessionCookie(userId: string) {
  const store = await cookies();
  store.set(COOKIE, userId, {
    httpOnly: true,
    sameSite: "lax",
    path: "/",
  });
}

export async function clearSessionCookie() {
  const store = await cookies();
  store.delete(COOKIE);
}
