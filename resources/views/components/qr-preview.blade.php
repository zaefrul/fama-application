@props(['value', 'size' => 220, 'caption' => true])
<div class="flex w-full flex-col items-center justify-center">
    <div class="rounded-3xl border border-border bg-white p-4 shadow-sm">
        <img
            src="{{ url('/api/qr') }}?data={{ urlencode($value) }}&size={{ max($size * 2, 360) }}&v=2"
            alt="QR {{ $value }}"
            width="{{ $size }}"
            height="{{ $size }}"
            class="mx-auto block aspect-square object-contain"
            style="width: {{ $size }}px; height: {{ $size }}px; max-width: min(200px, 56vw); max-height: min(200px, 56vw)"
        >
    </div>
    @if ($caption)
        <p class="mt-2 max-w-full break-all text-center text-xs font-semibold tracking-wide text-muted">{{ $value }}</p>
    @endif
</div>
