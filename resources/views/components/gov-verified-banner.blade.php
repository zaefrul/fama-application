@props([
    'title' => 'Disahkan oleh FAMA',
    'body' => 'Maklumat jejak ini telah disahkan oleh Lembaga Pemasaran Pertanian Persekutuan.',
])

<div {{ $attributes->class('flex items-start gap-3 rounded-2xl border border-success/25 bg-success/10 px-4 py-3') }}>
    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-success text-white">
        <x-icon name="shield" class="h-4 w-4" />
    </span>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-success">{{ $title }}</p>
        <p class="mt-0.5 text-xs leading-snug text-muted">{{ $body }}</p>
    </div>
</div>
