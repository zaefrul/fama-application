@props(['variant' => 'primary'])
@php
    $styles = [
        'primary' => 'bg-brand text-brand-fg hover:bg-brand-hover',
        'secondary' => 'bg-white text-ink border border-border hover:bg-surface-muted',
        'danger' => 'bg-white text-danger border border-danger/40 hover:bg-danger/5',
        'ghost' => 'bg-transparent text-brand hover:underline',
    ];
@endphp
<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition disabled:opacity-50 '.($styles[$variant] ?? $styles['primary'])]) }}>
    {{ $slot }}
</button>
