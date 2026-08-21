@props([
    'week' => 0,
    'lastWeek' => 0,
    'daily' => [],
    'top' => [],
])
@php
    $compareMax = max(1, (int) $week, (int) $lastWeek);
    $weekPct = (int) round(((int) $week / $compareMax) * 100);
    $lastPct = (int) round(((int) $lastWeek / $compareMax) * 100);
@endphp
<div class="space-y-5">
    <div>
        <h3 class="mb-2 text-sm font-semibold">Bandingan minggu</h3>
        <dl class="space-y-2">
            <div>
                <div class="mb-1 flex items-center justify-between text-xs">
                    <dt class="text-muted">Minggu ini</dt>
                    <dd class="font-semibold text-info">{{ number_format((int) $week) }}</dd>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-surface-muted">
                    <div class="h-full rounded-full bg-info" style="width: {{ $weekPct }}%"></div>
                </div>
            </div>
            <div>
                <div class="mb-1 flex items-center justify-between text-xs">
                    <dt class="text-muted">Minggu lepas</dt>
                    <dd class="font-semibold text-warning">{{ number_format((int) $lastWeek) }}</dd>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-surface-muted">
                    <div class="h-full rounded-full bg-warning" style="width: {{ $lastPct }}%"></div>
                </div>
            </div>
        </dl>
    </div>

    <div>
        <h3 class="mb-3 text-sm font-semibold">Imbasan harian · 7 hari</h3>
        <div class="flex h-36 items-end gap-1.5 sm:gap-2">
            @foreach ($daily as $index => $row)
                <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-1">
                    <p class="text-[10px] font-semibold text-ink">{{ $row['count'] }}</p>
                    <div class="flex h-24 w-full items-end justify-center">
                        <div class="w-full max-w-8 rounded-t-md" style="height: {{ max(6, $row['percent']) }}%; background: var(--chart-{{ ($index % 10) + 1 }})"></div>
                    </div>
                    <p class="text-[10px] font-medium text-muted">{{ $row['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @if (count($top))
        <div>
            <h3 class="mb-2 text-sm font-semibold">QR paling kerap diimbas · 7 hari</h3>
            <ul class="space-y-2">
                @foreach ($top as $index => $row)
                    <li>
                        <div class="mb-1 flex items-start justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $row['qrCode'] }}</p>
                                <p class="truncate text-muted">{{ $row['produce'] }} · {{ $row['company'] }}</p>
                            </div>
                            <p class="shrink-0 font-semibold" style="color: var(--chart-{{ ($index % 10) + 1 }})">{{ number_format((int) $row['count']) }}</p>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-surface-muted">
                            <div class="h-full rounded-full" style="width: {{ $row['percent'] }}%; background: var(--chart-{{ ($index % 10) + 1 }})"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
