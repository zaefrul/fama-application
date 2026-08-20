import { unstable_noStore as noStore } from "next/cache";
import { connection } from "next/server";
import Image from "next/image";
import Link from "next/link";
import { DocumentPreview } from "@/components/document-preview";
import { QrPreview } from "@/components/qr-preview";
import { StatusBadge } from "@/components/status-badge";
import { Card, DataRow } from "@/components/ui";
import { hydrateApplication } from "@/lib/lookups";
import { traceUrl } from "@/lib/app-url";
import { getRepositories } from "@/repositories";

export const dynamic = "force-dynamic";
export const revalidate = 0;

const copy = {
  bm: {
    inactiveTitle: "QR Belum Diaktifkan",
    inactiveBody: "QR ini telah dijana tetapi belum diaktifkan selepas kelulusan FAMA.",
    invalid: "Kod QR tidak sah.",
    more: "Lihat Maklumat Lanjut",
    product: "Maklumat Buah",
    export: "Maklumat Eksport",
    certs: "Sijil",
    nutrition: "Nutrisi",
  },
  en: {
    inactiveTitle: "QR Not Activated",
    inactiveBody: "This QR has been generated but is not active until FAMA approval.",
    invalid: "Invalid QR code.",
    more: "View more information",
    product: "Fruit information",
    export: "Export information",
    certs: "Certificates",
    nutrition: "Nutrition",
  },
  zh: {
    inactiveTitle: "QR 尚未激活",
    inactiveBody: "此二维码已生成，但尚未在 FAMA 批准后激活。",
    invalid: "无效的二维码。",
    more: "查看更多信息",
    product: "水果信息",
    export: "出口信息",
    certs: "证书",
    nutrition: "营养",
  },
};

export default async function TracePage({
  params,
  searchParams,
}: {
  params: Promise<{ qrCode: string }>;
  searchParams: Promise<{ lang?: string; t?: string }>;
}) {
  noStore();
  await connection();
  const { qrCode } = await params;
  const { lang: rawLang, t: cacheBust } = await searchParams;
  void cacheBust;
  const lang = rawLang === "en" || rawLang === "zh" ? rawLang : "bm";
  const t = copy[lang];
  const repos = getRepositories();
  const qr = await repos.getQrByCode(qrCode);

  return (
    <div className="relative mx-auto min-h-dvh max-w-lg overflow-hidden bg-[linear-gradient(180deg,#ffffff_0%,#f3f8f5_100%)] px-4 pb-16 pt-5">
      <header className="mb-5 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <Image
            src="/logos/jejak-gpl.png"
            alt="Sistem Jejak GPL"
            width={140}
            height={100}
            className="h-8 w-auto max-w-[46vw] object-contain sm:h-10"
          />
        </div>
        <div className="space-x-2 text-xs font-semibold">
          <Link href={`/trace/${qrCode}?lang=bm`} className={lang === "bm" ? "text-brand" : "text-muted"}>
            BM
          </Link>
          <Link href={`/trace/${qrCode}?lang=zh`} className={lang === "zh" ? "text-brand" : "text-muted"}>
            中文
          </Link>
          <Link href={`/trace/${qrCode}?lang=en`} className={lang === "en" ? "text-brand" : "text-muted"}>
            EN
          </Link>
        </div>
      </header>

      {!qr ? (
        <Card className="text-center">
          <p className="text-lg font-bold">{t.invalid}</p>
        </Card>
      ) : qr.status !== "ACTIVE" ? (
        <Card className="space-y-4 py-8 text-center">
          <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-warning text-4xl font-bold text-white">
            !
          </div>
          <h1 className="text-2xl font-bold">{t.inactiveTitle}</h1>
          <p className="text-sm text-muted">{t.inactiveBody}</p>
          <p className="rounded-xl bg-surface-muted px-3 py-2 text-sm font-semibold">QR ID: {qr.qrCode}</p>
        </Card>
      ) : (
        <ActiveTrace qrCode={qr.qrCode} lang={lang} t={t} />
      )}

      <div className="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-[radial-gradient(ellipse_at_bottom,_#0f6b4c22,_transparent_70%)]" />
    </div>
  );
}

async function ActiveTrace({
  qrCode,
  lang,
  t,
}: {
  qrCode: string;
  lang: "bm" | "en" | "zh";
  t: (typeof copy)["bm"];
}) {
  noStore();
  await connection();
  const repos = getRepositories();
  const qr = await repos.getQrByCode(qrCode);
  if (!qr) return null;
  const application = await repos.getApplication(qr.applicationId);
  if (!application) return null;
  const { company, produce } = await hydrateApplication(application);
  const certificates = await repos.listCertificates(application.companyId);
  const nutrition = await repos.getNutrition(application.produceTypeId);
  const publicUrl = await traceUrl(qr.qrCode);

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-bold leading-tight sm:text-3xl">{produce?.name}</h1>
        <div className="mt-2 flex gap-2">
          <StatusBadge label={`Gred ${application.grade}`} tone="success" />
          <StatusBadge label="Malaysia" tone="info" />
        </div>
      </div>
      <QrPreview value={publicUrl} size={180} />
      <Card className="px-5">
        <h2 className="mb-1 font-semibold">{t.product}</h2>
        <dl>
          <DataRow label="Exporter" value={company?.name} />
          <DataRow label={lang === "en" ? "Fruit type" : "Jenis Buah"} value={produce?.name} />
          <DataRow label="Gred" value={application.grade} />
          <DataRow label="Saiz" value={application.size} />
          <DataRow label="Berat" value={`${application.quantity} ${application.quantityUnit}`} />
          <DataRow label="Destinasi" value={application.destinationCountry} />
          <DataRow label="No. Sijil CoC" value={application.cocNumber} />
        </dl>
      </Card>
      <Card className="px-5">
        <h2 className="mb-1 font-semibold">{t.export}</h2>
        <dl>
          <DataRow label="Tarikh Eksport" value={application.exportDate} />
          <DataRow label="Alamat Pengeksport" value={company?.address} />
          <DataRow label="Nama Ladang" value={application.farmName} />
          <DataRow label="Pengimport" value={application.importerName} />
          <DataRow label="Alamat Pengimport" value={application.importerAddress} />
        </dl>
      </Card>
      <Card>
        <h2 className="mb-3 font-semibold">{t.certs}</h2>
        <div className="grid grid-cols-2 gap-2">
          {certificates.map((certificate) => (
            <a
              key={certificate.id}
              href={certificate.documentPath}
              target="_blank"
              rel="noreferrer"
              className="overflow-hidden rounded-xl border border-border bg-surface-muted p-2 text-xs"
            >
              <DocumentPreview src={certificate.documentPath} alt={certificate.type} className="mb-2 h-20 w-full object-cover" />
              <p className="font-semibold">SIJIL {certificate.type}</p>
            </a>
          ))}
        </div>
      </Card>
      {nutrition.length ? (
        <Card>
          <h2 className="mb-2 font-semibold">{t.nutrition}</h2>
          <table className="w-full text-left text-sm">
            <tbody>
              {nutrition.map((row) => (
                <tr key={row.name} className="border-t border-border">
                  <td className="py-1.5">{row.name}</td>
                  <td className="font-medium">{row.amount}</td>
                  <td className="text-muted">{row.dailyPercent}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </Card>
      ) : null}
    </div>
  );
}
