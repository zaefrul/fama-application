@props(['application' => null, 'qr' => null, 'label' => null, 'tone' => null])
@php
    $tones = [
        'success' => 'bg-success/10 text-success border-success/20',
        'warning' => 'bg-warning/15 text-warning border-warning/25',
        'danger' => 'bg-danger/10 text-danger border-danger/20',
        'info' => 'bg-info/10 text-info border-info/20',
        'neutral' => 'bg-neutral/10 text-neutral border-neutral/20',
    ];
    $resolvedLabel = $label ?? ($application?->label() ?? $qr?->label() ?? '');
    $resolvedTone = $tone ?? ($application?->tone() ?? $qr?->tone() ?? 'neutral');
@endphp
<span class="inline-flex max-w-[7.5rem] items-center truncate rounded-full border px-2 py-0.5 text-[11px] font-semibold sm:max-w-none sm:px-2.5 sm:text-xs {{ $tones[$resolvedTone] }}">
    {{ $resolvedLabel }}
</span>
