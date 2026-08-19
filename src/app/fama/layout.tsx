import { AppHeader } from "@/components/brand";
import { SideNav } from "@/components/nav";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function FamaLayout({ children }: { children: React.ReactNode }) {
  const session = await requireRole("FAMA_OFFICER");
  const notifications = await getRepositories().listNotifications(session.id);
  const unread = notifications.filter((row) => !row.read).length;

  return (
    <div className="min-h-dvh md:flex">
      <SideNav actor="fama" />
      <div className="flex min-h-dvh min-w-0 flex-1 flex-col">
        <AppHeader notificationCount={unread} onMenuHref="/fama/menu" />
        <main className="mx-auto w-full min-w-0 max-w-6xl flex-1 px-3 pb-8 pt-3 sm:px-4 sm:pt-4">{children}</main>
      </div>
    </div>
  );
}
