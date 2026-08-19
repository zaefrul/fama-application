import { getRepositories } from "@/repositories";
import type { Certificate, Company, ExportApplication, ProduceType, QrCodeRecord } from "@/domain/types";

export async function hydrateApplication(application: ExportApplication) {
  const repos = getRepositories();
  const [company, produceTypes, qrCodes, certificates] = await Promise.all([
    repos.getCompany(application.companyId),
    repos.listProduceTypes(),
    repos.listQrCodes({ companyId: application.companyId }),
    repos.listCertificates(application.companyId),
  ]);
  const produce = produceTypes.find((row) => row.id === application.produceTypeId);
  const qr = qrCodes.find((row) => row.applicationId === application.id) ?? null;
  const coc = certificates.find((row) => row.id === application.cocCertificateId) ?? null;
  return { application, company, produce, qr, coc };
}

export function produceName(produceTypes: ProduceType[], id: string) {
  return produceTypes.find((row) => row.id === id)?.name ?? id;
}

export type HydratedApplication = {
  application: ExportApplication;
  company: Company | null;
  produce: ProduceType | undefined;
  qr: QrCodeRecord | null;
  coc: Certificate | null;
};
