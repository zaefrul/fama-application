import Link from "next/link";
import { ApplicationCard } from "@/components/application-card";
import { QrPreview } from "@/components/qr-preview";
import { Button, Card, KpiCard } from "@/components/ui";
import { produceName } from "@/lib/lookups";
import { requireRole } from "@/lib/session";
import { traceUrl } from "@/lib/app-url";
import { getRepositories } from "@/repositories";

export default async function ExporterDashboardPage() {
  const session = await requireRole("EXPORTER");
  const repos = getRepositories();
  const company = session.companyId ? await repos.getCompany(session.companyId) : null;
  const stats = session.companyId
    ? await repos.dashboardExporter(session.companyId)
    : { qrActive: 0, qrInactive: 0, totalApplications: 0, approved: 0, rejected: 0 };
  const applications = session.companyId ? await repos.listApplications({ companyId: session.companyId }) : [];
  const qrs = session.companyId ? await repos.listQrCodes({ companyId: session.companyId }) : [];
  const produceTypes = await repos.listProduceTypes();
  const gallery = session.companyId ? await repos.listGallery(session.companyId) : [];
  const featuredQr = qrs.find((qr) => qr.status === "ACTIVE") ?? qrs[0] ?? null;
  const featuredApp = featuredQr
    ? applications.find((application) => application.id === featuredQr.applicationId)
    : null;
  const publicUrl = featuredQr ? await traceUrl(featuredQr.qrCode) : "";

  return (
    <div className="space-y-4">
      <Card className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div className="min-w-0">
          <p className="text-sm text-muted">Selamat datang,</p>
          <h1 className="flex min-w-0 items-center gap-2 text-base font-bold sm:text-lg">
            <span className="truncate">{company?.name ?? session.name}</span>
            <span className="shrink-0 text-sm text-brand" aria-hidden>
              ✓
            </span>
          </h1>
        </div>
        <Link
          href="/exporter/company"
          className="shrink-0 self-start rounded-full border border-border px-3 py-1.5 text-xs font-semibold text-brand"
        >
          Kemas Kini Profil
        </Link>
      </Card>

      <div className="grid grid-cols-2 gap-3">
        <KpiCard value={stats.qrActive} label="QR Aktif" tone="success" href="/exporter/qr" />
        <KpiCard value={stats.qrInactive} label="QR Belum Aktif" tone="warning" href="/exporter/qr" />
      </div>
      <div className="grid grid-cols-3 gap-2 sm:gap-3">
        <KpiCard value={stats.totalApplications} label="Jumlah Permohonan" tone="warning" href="/exporter/applications" />
        <KpiCard value={stats.approved} label="Permohonan Lulus" tone="success" href="/exporter/applications" />
        <KpiCard value={stats.rejected} label="Permohonan Gagal" tone="danger" href="/exporter/applications" />
      </div>

      <Card>
        <h2 className="mb-3 font-semibold">Tindakan Pantas</h2>
        <div className="grid grid-cols-2 gap-2 md:grid-cols-3">
          <Link href="/exporter/company/produce">
            <Button className="w-full">+ Maklumat Buah</Button>
          </Link>
          <Link href="/exporter/company/certificates">
            <Button variant="secondary" className="w-full">
              + Sijil
            </Button>
          </Link>
          <Link href="/exporter/qr">
            <Button variant="secondary" className="w-full">
              Cetak QR
            </Button>
          </Link>
        </div>
      </Card>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-semibold">Permohonan</h2>
            <Link href="/exporter/applications" className="text-sm font-semibold text-brand">
              Lihat semua →
            </Link>
          </div>
          <ul className="space-y-2">
            {applications.slice(0, 4).map((application) => (
              <li key={application.id}>
                <ApplicationCard
                  href={`/exporter/applications/${application.id}`}
                  title={`${application.applicationNo} · ${produceName(produceTypes, application.produceTypeId)} ${application.variety}`}
                  subtitle={`Dihantar pada ${application.submittedAt ? new Date(application.submittedAt).toLocaleDateString("ms-MY", { day: "2-digit", month: "long", year: "numeric" }) : "—"}`}
                  status={application.status}
                />
              </li>
            ))}
          </ul>
        </Card>

        {featuredQr && featuredApp ? (
          <Card className="space-y-3">
            <div className="flex items-center justify-between">
              <h2 className="font-semibold">Kod QR</h2>
              <Link href="/exporter/qr" className="text-sm font-semibold text-brand">
                Lihat semua →
              </Link>
            </div>
            <QrPreview value={publicUrl} size={180} />
            <dl className="space-y-1 text-sm">
              <div className="flex justify-between gap-3">
                <dt className="text-muted">ID Kod QR</dt>
                <dd className="font-semibold">{featuredQr.qrCode}</dd>
              </div>
              <div className="flex justify-between gap-3">
                <dt className="text-muted">Produk</dt>
                <dd className="font-semibold">{produceName(produceTypes, featuredApp.produceTypeId)}</dd>
              </div>
              <div className="flex justify-between gap-3">
                <dt className="text-muted">Gred</dt>
                <dd className="font-semibold">{featuredApp.grade}</dd>
              </div>
            </dl>
          </Card>
        ) : null}
      </div>

      <Card>
        <h2 className="mb-3 font-semibold">Galeri</h2>
        <div className="overflow-hidden rounded-2xl bg-surface-muted">
          {gallery[0] ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={gallery[0].filePath} alt={gallery[0].description} className="h-44 w-full object-cover" />
          ) : (
            <div className="flex h-44 items-center justify-center text-sm text-muted">Tiada gambar</div>
          )}
        </div>
      </Card>
    </div>
  );
}
