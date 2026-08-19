export function QrPreview({
  value,
  size = 220,
  caption = true,
}: {
  value: string;
  size?: number;
  caption?: boolean;
}) {
  const src = `/api/qr?data=${encodeURIComponent(value)}&size=${Math.max(size * 2, 360)}&v=2`;

  return (
    <div className="flex w-full flex-col items-center justify-center">
      <div className="rounded-3xl border border-border bg-white p-4 shadow-sm">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={src}
          alt={`QR ${value}`}
          width={size}
          height={size}
          className="mx-auto block aspect-square object-contain"
          style={{ width: size, height: size, maxWidth: "min(200px, 56vw)", maxHeight: "min(200px, 56vw)" }}
        />
      </div>
      {caption ? (
        <p className="mt-2 max-w-full break-all text-center text-xs font-semibold tracking-wide text-muted">
          {value}
        </p>
      ) : null}
    </div>
  );
}
