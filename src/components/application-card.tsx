import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import type { ApplicationStatus } from "@/domain/status";

export function ApplicationCard({
  href,
  title,
  subtitle,
  status,
}: {
  href: string;
  title: string;
  subtitle: string;
  status: ApplicationStatus;
}) {
  return (
    <Link
      href={href}
      className="flex min-w-0 items-center gap-2 rounded-2xl border border-[#c5d4ea] bg-[#eef5fb] px-2.5 py-2.5 sm:gap-3 sm:px-3 sm:py-3"
    >
      <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand text-white sm:h-10 sm:w-10">
        +
      </span>
      <div className="min-w-0 flex-1">
        <p className="truncate text-sm font-semibold">{title}</p>
        <p className="truncate text-xs text-muted">{subtitle}</p>
      </div>
      <span className="shrink-0">
        <StatusBadge application={status} />
      </span>
      <span className="hidden text-muted sm:inline">›</span>
    </Link>
  );
}
