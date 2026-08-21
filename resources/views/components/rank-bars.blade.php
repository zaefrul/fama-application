@props([
    'items' => [],
    'empty' => 'Tiada data',
])
@if (! count($items))
    <p class="text-sm text-muted">{{ $empty }}</p>
@else
    <ol class="space-y-2.5">
        @foreach ($items as $row)
            <li>
                <div class="mb-1 flex items-start justify-between gap-3 text-xs">
                    <p class="min-w-0 truncate font-semibold">{{ $row['label'] }}</p>
                    <p class="shrink-0 font-semibold" style="color: var(--chart-{{ $row['color'] }})">{{ number_format((int) $row['count']) }}</p>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-surface-muted">
                    <div class="h-full rounded-full" style="width: {{ max(6, (int) $row['percent']) }}%; background: var(--chart-{{ $row['color'] }})"></div>
                </div>
            </li>
        @endforeach
    </ol>
@endif
