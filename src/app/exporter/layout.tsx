import { AppHeader } from "@/components/brand";
import { ExporterBottomNav, SideNav } from "@/components/nav";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function ExporterLayout({ children }: { children: React.ReactNode }) {
  const session = await requireRole("EXPORTER");
  const notifications = await getRepositories().listNotifications(session.id);
  const unread = notifications.filter((row) => !row.read).length;

  return (
    <div className="min-h-dvh md:flex">
      <SideNav actor="exporter" />
      <div className="flex min-h-dvh min-w-0 flex-1 flex-col">
        <AppHeader notificationCount={unread} />
        <main className="mx-auto w-full min-w-0 max-w-5xl flex-1 px-3 pb-24 pt-3 sm:px-4 sm:pt-4 md:pb-8">{children}</main>
        <ExporterBottomNav />
      </div>
    </div>
  );
}
