import { Card, KpiCard, PageTitle } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function FamaDashboardPage() {
  await requireRole("FAMA_OFFICER");
  const stats = await getRepositories().dashboardFama();

  return (
    <div className="space-y-4">
      <PageTitle title="Utama FAMA" subtitle="Pemantauan prototaip Jejak GPL" />
      <div className="grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-5">
        <KpiCard value={stats.activeCompanies} label="Syarikat Aktif" tone="info" href="/fama/companies" />
        <KpiCard value={stats.qrActive} label="QR Aktif" tone="neutral" href="/fama/qr" />
        <KpiCard value={stats.approved} label="Diluluskan" tone="success" href="/fama/applications?status=APPROVED" />
        <KpiCard value={stats.pending} label="Menunggu Pengesahan" tone="warning" href="/fama/applications?status=UNDER_REVIEW" />
        <KpiCard value={stats.rejected} label="Ditolak" tone="danger" href="/fama/applications?status=REJECTED" />
      </div>
      <Card>
        <h2 className="mb-3 font-semibold">Pemantauan Harian Bilangan QR Yang Dijana</h2>
        <div className="grid grid-cols-7 gap-1 text-center text-[10px] sm:gap-2 sm:text-xs">
          {stats.dailyQr.map((row) => (
            <div key={row.day} className="min-w-0 rounded-lg bg-surface-muted p-1.5 sm:rounded-xl sm:p-2">
              <p className="font-semibold">{row.day.slice(0, 3)}</p>
              <p className="text-success">{row.active}</p>
              <p className="text-warning">{row.inactive}</p>
            </div>
          ))}
        </div>
        <p className="mt-2 text-xs text-muted">Hijau: QR Aktif · Kuning: QR Belum Aktif</p>
      </Card>
    </div>
  );
}
