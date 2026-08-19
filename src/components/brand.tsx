import Image from "next/image";

export const APP_LOGO = "/logos/jejak-gpl.png";

export function BrandMark({ compact = false }: { compact?: boolean }) {
  return (
    <div className="flex min-w-0 max-w-full flex-col items-center leading-tight">
      <Image
        src={APP_LOGO}
        alt="Sistem Jejak GPL"
        width={220}
        height={156}
        className={`w-auto max-w-[42vw] object-contain sm:max-w-none ${compact ? "h-8" : "h-8 sm:h-11"}`}
        priority
      />
      {!compact ? (
        <p className="mt-0.5 hidden max-w-[220px] text-center text-[10px] leading-tight text-muted sm:block">
          Lembaga Pemasaran Pertanian Persekutuan
        </p>
      ) : null}
    </div>
  );
}

export function AppHeader({
  notificationCount = 0,
  onMenuHref,
}: {
  notificationCount?: number;
  onMenuHref?: string;
}) {
  return (
    <header className="sticky top-0 z-20 flex items-center gap-2 border-b border-border bg-white px-3 py-2 sm:px-4 sm:py-2.5">
      {onMenuHref ? (
        <a href={onMenuHref} className="shrink-0 rounded-lg border border-border px-2 py-1 text-lg" aria-label="Menu">
          ☰
        </a>
      ) : (
        <span className="w-8 shrink-0" />
      )}
      <div className="flex min-w-0 flex-1 justify-center">
        <BrandMark />
      </div>
      <span className="relative w-8 shrink-0 rounded-lg border border-border px-2 py-1 text-center text-sm" aria-label="Notifikasi">
        🔔
        {notificationCount > 0 ? (
          <span className="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] text-white">
            {notificationCount}
          </span>
        ) : null}
      </span>
    </header>
  );
}
