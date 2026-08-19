import { isPdfPath } from "@/lib/upload";

export function DocumentPreview({
  src,
  alt,
  className = "h-28 w-full object-cover",
}: {
  src: string;
  alt: string;
  className?: string;
}) {
  if (isPdfPath(src)) {
    return (
      <a
        href={src}
        target="_blank"
        rel="noreferrer"
        className="flex h-28 items-center justify-center rounded-xl bg-surface-muted text-sm font-semibold text-brand"
      >
        PDF · Lihat sijil
      </a>
    );
  }

  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img src={src} alt={alt} className={className} />
  );
}
