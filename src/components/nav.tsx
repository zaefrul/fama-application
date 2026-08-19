"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

const exporterItems = [
  { href: "/exporter", label: "Utama", icon: "⌂" },
  { href: "/exporter/applications", label: "Permohonan", icon: "☰" },
  { href: "/exporter/qr", label: "Kod QR", icon: "▣" },
  { href: "/exporter/company/certificates", label: "Sijil", icon: "▤" },
  { href: "/exporter/company", label: "Profil", icon: "☺" },
];

const famaItems = [
  { href: "/fama", label: "Utama", icon: "⌂" },
  { href: "/fama/qr", label: "Pengurusan QR", icon: "▣" },
  { href: "/fama/applications", label: "Kelulusan QR", icon: "✓" },
  { href: "/fama/companies", label: "Maklumat Syarikat", icon: "⌂" },
];

const companyItems = [
  { href: "/exporter/company", label: "Syarikat" },
  { href: "/exporter/company/produce", label: "Keluaran" },
  { href: "/exporter/company/certificates", label: "Sijil" },
  { href: "/exporter/company/gallery", label: "Galeri" },
];

function isActive(pathname: string | null, href: string, items: { href: string }[]) {
  const path = pathname ?? "";
  if (href === "/exporter" || href === "/fama") return path === href;
  const matches = items.filter((item) => path === item.href || path.startsWith(`${item.href}/`));
  const best = [...matches].sort((a, b) => b.href.length - a.href.length)[0];
  return best?.href === href;
}

export function ExporterBottomNav() {
  const pathname = usePathname();
  return (
    <nav className="fixed inset-x-0 bottom-0 z-20 border-t border-border bg-white px-1 py-1.5 md:hidden">
      <ul className="grid grid-cols-5 text-center text-[10px] leading-tight">
        {exporterItems.map((item) => (
          <li key={item.href} className="min-w-0">
            <Link
              href={item.href}
              className={`flex flex-col items-center gap-0.5 px-0.5 py-1 ${
                isActive(pathname, item.href, exporterItems) ? "font-bold text-brand" : "text-muted"
              }`}
            >
              <span className="text-base">{item.icon}</span>
              <span className="max-w-full truncate">{item.label}</span>
            </Link>
          </li>
        ))}
      </ul>
    </nav>
  );
}

export function SideNav({ actor }: { actor: "exporter" | "fama" }) {
  const pathname = usePathname();
  const items = actor === "exporter" ? exporterItems : famaItems;
  const logout = (
    <form action="/api/auth/logout" method="post">
      <button className="w-full rounded-xl px-3 py-2 text-left text-sm text-white/80 hover:bg-white/10">
        Log Keluar
      </button>
    </form>
  );

  return (
    <aside className="hidden w-64 shrink-0 bg-surface-dark p-4 text-white md:flex md:flex-col">
      <div className="mb-6 rounded-2xl bg-white px-3 py-2">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img src="/logos/jejak-gpl.png" alt="Sistem Jejak GPL" className="mx-auto h-12 w-auto object-contain" />
      </div>
      <p className="mb-3 text-xs uppercase tracking-wide text-white/60">Menu Utama</p>
      <ul className="space-y-1">
        {items.map((item) => (
          <li key={item.href}>
            <Link
              href={item.href}
              className={`block rounded-xl px-3 py-2 text-sm ${
                isActive(pathname, item.href, items) ? "bg-white/15 font-semibold" : "text-white/80 hover:bg-white/10"
              }`}
            >
              {item.label}
            </Link>
          </li>
        ))}
      </ul>
      <div className="mt-auto">{logout}</div>
    </aside>
  );
}

export function CompanyNav() {
  const pathname = usePathname();
  return (
    <nav className="mb-4">
      <ul className="grid grid-cols-4 gap-1 rounded-2xl bg-surface-muted p-1">
        {companyItems.map((item) => (
          <li key={item.href} className="min-w-0">
            <Link
              href={item.href}
              className={`block truncate rounded-xl px-1 py-2 text-center text-[11px] sm:px-3 sm:text-sm ${
                isActive(pathname, item.href, companyItems) ? "bg-white font-semibold text-brand shadow-sm" : "text-muted"
              }`}
            >
              {item.label}
            </Link>
          </li>
        ))}
      </ul>
    </nav>
  );
}

export function FamaMobileMenu() {
  return (
    <div className="space-y-2 p-4 md:hidden">
      {famaItems.map((item) => (
        <Link key={item.href} href={item.href} className="block rounded-xl bg-white px-3 py-3 text-sm shadow-sm">
          {item.label}
        </Link>
      ))}
      <form action="/api/auth/logout" method="post">
        <button className="w-full rounded-xl bg-white px-3 py-3 text-left text-sm text-danger shadow-sm">
          Log Keluar
        </button>
      </form>
    </div>
  );
}
