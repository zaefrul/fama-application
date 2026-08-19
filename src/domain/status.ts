export const APPLICATION_STATUSES = [
  "DRAFT",
  "SUBMITTED",
  "UNDER_REVIEW",
  "APPROVED",
  "REJECTED",
] as const;

export type ApplicationStatus = (typeof APPLICATION_STATUSES)[number];

export const QR_STATUSES = [
  "NOT_GENERATED",
  "GENERATED_INACTIVE",
  "ACTIVE",
] as const;

export type QrStatus = (typeof QR_STATUSES)[number];

export const APPLICATION_STATUS_LABEL: Record<ApplicationStatus, string> = {
  DRAFT: "Draf",
  SUBMITTED: "Dihantar",
  UNDER_REVIEW: "Dalam Semakan",
  APPROVED: "Diluluskan",
  REJECTED: "Ditolak",
};

export const QR_STATUS_LABEL: Record<QrStatus, string> = {
  NOT_GENERATED: "Belum Dijana",
  GENERATED_INACTIVE: "Belum Aktif",
  ACTIVE: "Aktif",
};

export type StatusTone = "success" | "warning" | "danger" | "info" | "neutral";

export function applicationStatusTone(status: ApplicationStatus): StatusTone {
  switch (status) {
    case "APPROVED":
      return "success";
    case "REJECTED":
      return "danger";
    case "UNDER_REVIEW":
    case "SUBMITTED":
      return "info";
    case "DRAFT":
      return "neutral";
  }
}

export function qrStatusTone(status: QrStatus): StatusTone {
  switch (status) {
    case "ACTIVE":
      return "success";
    case "GENERATED_INACTIVE":
      return "warning";
    case "NOT_GENERATED":
      return "neutral";
  }
}
