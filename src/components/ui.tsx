import type { ButtonHTMLAttributes, InputHTMLAttributes, ReactNode, SelectHTMLAttributes, TextareaHTMLAttributes } from "react";

export function Button({
  variant = "primary",
  className = "",
  ...props
}: ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: "primary" | "secondary" | "danger" | "ghost";
}) {
  const styles = {
    primary: "bg-brand text-brand-fg hover:bg-brand-hover",
    secondary: "bg-white text-ink border border-border hover:bg-surface-muted",
    danger: "bg-white text-danger border border-danger/40 hover:bg-danger/5",
    ghost: "bg-transparent text-brand hover:underline",
  };
  return (
    <button
      className={`inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition disabled:opacity-50 ${styles[variant]} ${className}`}
      {...props}
    />
  );
}

export function Card({
  children,
  className = "",
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <section className={`min-w-0 rounded-2xl border border-border bg-white p-3 shadow-sm sm:p-4 ${className}`}>
      {children}
    </section>
  );
}

export function KpiCard({
  value,
  label,
  tone = "neutral",
  href,
}: {
  value: number | string;
  label: string;
  tone?: "success" | "warning" | "danger" | "info" | "neutral";
  href?: string;
}) {
  const tones = {
    success: "bg-success/10 text-success",
    warning: "bg-warning/15 text-warning",
    danger: "bg-danger/10 text-danger",
    info: "bg-info/10 text-info",
    neutral: "bg-surface-muted text-ink",
  };
  const content = (
    <Card className={`${tones[tone]} px-2 py-3 text-center sm:p-4`}>
      <p className="text-2xl font-bold leading-none sm:text-3xl">{value}</p>
      <p className="mt-1 text-[11px] font-medium leading-tight opacity-80 sm:text-xs">{label}</p>
    </Card>
  );
  return href ? <a href={href}>{content}</a> : content;
}

export function Field({
  label,
  hint,
  required,
  children,
}: {
  label: string;
  hint?: string;
  required?: boolean;
  children: ReactNode;
}) {
  return (
    <label className="block space-y-1.5">
      <span className="text-sm font-medium text-ink">
        {label}
        {required ? <span className="text-danger"> *</span> : null}
      </span>
      {children}
      {hint ? <span className="text-xs text-muted">{hint}</span> : null}
    </label>
  );
}

const inputClass =
  "w-full min-w-0 max-w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-brand";

export function Input({ className = "", ...props }: InputHTMLAttributes<HTMLInputElement>) {
  return (
    <input
      className={`${inputClass} ${props.readOnly ? "bg-surface-muted text-muted" : ""} ${className}`}
      {...props}
    />
  );
}

export function Select({ className = "", ...props }: SelectHTMLAttributes<HTMLSelectElement>) {
  return <select className={`${inputClass} ${className}`} {...props} />;
}

export function Textarea({ className = "", ...props }: TextareaHTMLAttributes<HTMLTextAreaElement>) {
  return <textarea className={`${inputClass} min-h-24 ${className}`} {...props} />;
}

export function PageTitle({
  title,
  subtitle,
}: {
  title: string;
  subtitle?: string;
}) {
  return (
    <header className="mb-3 min-w-0 sm:mb-4">
      <h1 className="text-lg font-bold leading-tight text-ink sm:text-xl">{title}</h1>
      {subtitle ? <p className="mt-1 text-sm leading-snug text-muted">{subtitle}</p> : null}
    </header>
  );
}

export function ErrorText({ children }: { children?: ReactNode }) {
  if (!children) return null;
  return <p className="text-sm text-danger">{children}</p>;
}

export function DataRow({ label, value }: { label: string; value?: ReactNode }) {
  return (
    <div className="flex items-start justify-between gap-3 border-b border-border/70 py-2.5 last:border-0">
      <dt className="max-w-[46%] shrink-0 text-sm leading-snug text-muted">{label}</dt>
      <dd className="min-w-0 break-words text-right text-sm font-semibold text-ink">{value ?? "—"}</dd>
    </div>
  );
}

export function ProgressSteps({ current, total }: { current: number; total: number }) {
  return (
    <div className="mb-4 grid gap-1" style={{ gridTemplateColumns: `repeat(${total}, minmax(0, 1fr))` }}>
      {Array.from({ length: total }, (_, index) => (
        <div
          key={index}
          className={`h-1.5 rounded-full ${index < current ? "bg-brand" : "bg-border"}`}
        />
      ))}
    </div>
  );
}

export function Breadcrumb({ items }: { items: string[] }) {
  return (
    <p className="mb-2 text-xs text-muted">
      {items.map((item, index) => (
        <span key={item}>
          {index > 0 ? <span className="mx-1">›</span> : null}
          <span className={index === items.length - 1 ? "font-semibold text-ink" : ""}>{item}</span>
        </span>
      ))}
    </p>
  );
}
