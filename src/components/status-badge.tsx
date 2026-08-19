import {
  APPLICATION_STATUS_LABEL,
  QR_STATUS_LABEL,
  applicationStatusTone,
  qrStatusTone,
  type ApplicationStatus,
  type QrStatus,
  type StatusTone,
} from "@/domain/status";

const tones: Record<StatusTone, string> = {
  success: "bg-success/10 text-success border-success/20",
  warning: "bg-warning/15 text-warning border-warning/25",
  danger: "bg-danger/10 text-danger border-danger/20",
  info: "bg-info/10 text-info border-info/20",
  neutral: "bg-neutral/10 text-neutral border-neutral/20",
};

export function StatusBadge({
  application,
  qr,
  label,
  tone,
}: {
  application?: ApplicationStatus;
  qr?: QrStatus;
  label?: string;
  tone?: StatusTone;
}) {
  const resolvedLabel =
    label ??
    (application ? APPLICATION_STATUS_LABEL[application] : qr ? QR_STATUS_LABEL[qr] : "");
  const resolvedTone =
    tone ??
    (application ? applicationStatusTone(application) : qr ? qrStatusTone(qr) : "neutral");

  return (
    <span
      className={`inline-flex max-w-[7.5rem] items-center truncate rounded-full border px-2 py-0.5 text-[11px] font-semibold sm:max-w-none sm:px-2.5 sm:text-xs ${tones[resolvedTone]}`}
    >
      {resolvedLabel}
    </span>
  );
}
