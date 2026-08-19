import type { ApplicationStatus, QrStatus } from "./status";

const APPLICATION_TRANSITIONS: Record<ApplicationStatus, ApplicationStatus[]> = {
  DRAFT: ["SUBMITTED"],
  SUBMITTED: ["UNDER_REVIEW"],
  UNDER_REVIEW: ["APPROVED", "REJECTED"],
  APPROVED: [],
  REJECTED: [],
};

const QR_TRANSITIONS: Record<QrStatus, QrStatus[]> = {
  NOT_GENERATED: ["GENERATED_INACTIVE"],
  GENERATED_INACTIVE: ["ACTIVE"],
  ACTIVE: [],
};

export function canTransitionApplication(
  from: ApplicationStatus,
  to: ApplicationStatus,
): boolean {
  return APPLICATION_TRANSITIONS[from].includes(to);
}

export function canTransitionQr(from: QrStatus, to: QrStatus): boolean {
  return QR_TRANSITIONS[from].includes(to);
}

export function assertApplicationTransition(
  from: ApplicationStatus,
  to: ApplicationStatus,
) {
  if (!canTransitionApplication(from, to)) {
    throw new Error(`Peralihan status permohonan tidak sah: ${from} → ${to}`);
  }
}

export function assertQrTransition(from: QrStatus, to: QrStatus) {
  if (!canTransitionQr(from, to)) {
    throw new Error(`Peralihan status QR tidak sah: ${from} → ${to}`);
  }
}
