@props(['label', 'value' => null])
<div class="flex items-start justify-between gap-3 border-b border-border/70 py-2.5 last:border-0">
    <dt class="max-w-[46%] shrink-0 text-sm leading-snug text-muted">{{ $label }}</dt>
    <dd class="min-w-0 break-words text-right text-sm font-semibold text-ink">{{ $value ?? '—' }}</dd>
</div>
