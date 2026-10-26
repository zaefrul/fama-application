@props(['stats'])
@php
    $tones = [
        'success' => 'var(--pecahan-hold)',
        'warning' => 'var(--pecahan-wait)',
        'info' => 'var(--pecahan-done)',
        'sold' => 'var(--pecahan-sold)',
    ];
@endphp

<div class="pecahan-charts space-y-4">
    <div class="flex items-end justify-between gap-3">
        <div class="min-w-0">
            <h2 class="font-semibold">Jualan lot</h2>
            <p class="text-xs text-muted">Baki, aliran dan status lot syarikat ini.</p>
        </div>
        <a href="{{ route('exporter.lots') }}" class="shrink-0 text-sm font-semibold text-brand">Lihat semua →</a>
    </div>

    @if (! $stats['hasLots'])
        <x-card>
            <p class="text-sm text-muted">Tiada lot dipegang atau dihantar lagi. Jualan muncul selepas QR aktif dipecahkan.</p>
        </x-card>
    @else
        <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <x-kpi-card :value="$stats['waiting']" label="Menunggu penerimaan" tone="warning" href="/exporter/lots" />
            <x-kpi-card :value="$stats['holding']" label="Dipegang" tone="success" href="/exporter/lots" />
            <x-kpi-card :value="$stats['soldThisMonth']" label="Lot dijual bulan ini" tone="teal" href="/exporter/lots" />
        </div>

        <x-card>
            <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-semibold">Baki setiap lot</h3>
                    <p class="text-xs text-muted">Lot yang dipegang syarikat ini. Hijau baki, biru sudah keluar.</p>
                </div>
            </div>
            @if ($stats['balances'] === [])
                <p class="text-sm text-muted">Tiada lot dipegang.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($stats['balances'] as $row)
                        <li>
                            <a href="{{ route('exporter.lots.show', $row['id']) }}" class="block rounded-xl px-1 py-2.5 hover:bg-surface-muted">
                                <div class="mb-1 flex items-start justify-between gap-3 text-xs">
                                    <p class="min-w-0 truncate font-semibold">{{ $row['qrCode'] }}</p>
                                    <p class="shrink-0 font-semibold">{{ number_format($row['remaining']) }} / {{ number_format($row['quantity']) }} {{ $row['unit'] }}</p>
                                </div>
                                <p class="mb-1.5 truncate text-[11px] text-muted">{{ $row['produce'] }}</p>
                                <div class="pecahan-track flex h-2.5 overflow-hidden rounded-full">
                                    @if ($row['remaining'] > 0)
                                        <div class="h-full" style="flex: {{ (int) $row['remaining'] }} 1 0%; background: var(--pecahan-hold)" title="Baki {{ number_format($row['remaining']) }} {{ $row['unit'] }}"></div>
                                    @endif
                                    @if ($row['sent'] > 0)
                                        <div class="h-full" style="flex: {{ (int) $row['sent'] }} 1 0%; background: var(--pecahan-out)" title="Keluar {{ number_format($row['sent']) }} {{ $row['unit'] }}"></div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if ($stats['balanceMore'] > 0)
                    <p class="mt-2 text-xs text-muted">
                        <a href="{{ route('exporter.lots') }}" class="font-semibold text-brand">+ {{ $stats['balanceMore'] }} lot lagi</a>
                    </p>
                @endif
            @endif
        </x-card>

        <x-card>
            <h3 class="mb-1 font-semibold">Didapat lawan dilepaskan</h3>
            <p class="mb-3 text-xs text-muted">Garis hijau ialah kuantiti diterima daripada syarikat lain. Garis biru ialah kuantiti yang dihantar atau dijual. 8 minggu kalendar (waktu Malaysia).</p>
            <div class="space-y-4">
                @foreach ($stats['weeks'] as $seriesIndex => $series)
                    @if ((int) $series['receivedTotal'] + (int) $series['total'] === 0)
                        <p class="text-sm text-muted">Tiada kuantiti didapat atau dilepaskan dalam 8 minggu ini.</p>
                    @else
                        @php
                            $rows = $series['rows'];
                            $count = count($rows);
                            $peak = 1;
                            foreach ($rows as $row) {
                                $peak = max($peak, (int) $row['received'], (int) $row['split'] + (int) $row['sold']);
                            }
                            $width = 640;
                            $height = 176;
                            $left = 22;
                            $right = 22;
                            $top = 12;
                            $bottom = 28;
                            $plotW = $width - $left - $right;
                            $plotH = $height - $top - $bottom;
                            $step = $count > 1 ? $plotW / ($count - 1) : 0;
                            $marks = [];
                            $inLine = [];
                            $outLine = [];
                            foreach ($rows as $index => $row) {
                                $x = round($left + ($step * $index), 1);
                                $received = (int) $row['received'];
                                $released = (int) $row['split'] + (int) $row['sold'];
                                $inY = round($top + (1 - ($received / $peak)) * $plotH, 1);
                                $outY = round($top + (1 - ($released / $peak)) * $plotH, 1);
                                $inLine[] = $x.','.$inY;
                                $outLine[] = $x.','.$outY;
                                $marks[] = compact('x', 'inY', 'outY', 'received', 'released') + ['label' => $row['label']];
                            }
                        $baseY = round($top + $plotH, 1);
                        $firstX = $marks[0]['x'];
                        $lastX = $marks[count($marks) - 1]['x'];
                        $inArea = $firstX.','.$baseY.' '.implode(' ', $inLine).' '.$lastX.','.$baseY;
                        $outArea = $firstX.','.$baseY.' '.implode(' ', $outLine).' '.$lastX.','.$baseY;
                        $chartKey = $seriesIndex;
                    @endphp
                        @if (count($stats['weeks']) > 1 && $series['unit'])
                            <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $series['unit'] }}</p>
                        @endif
                        <p class="mb-2 text-xs">
                            <span class="font-semibold" style="color: var(--pecahan-hold-ink)">Didapat {{ number_format((int) $series['receivedTotal']) }} {{ $series['unit'] }}</span>
                            <span class="text-muted"> · </span>
                            <span class="font-semibold" style="color: var(--pecahan-out-ink)">Dilepaskan {{ number_format((int) $series['splitTotal'] + (int) $series['soldTotal']) }} {{ $series['unit'] }}</span>
                        </p>
                        <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-40 w-full" role="img" aria-label="Didapat lawan dilepaskan, {{ $series['unit'] }}">
                            <defs>
                                <linearGradient id="pecahan-out-fill-{{ $chartKey }}" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="var(--pecahan-out)" stop-opacity="0.42" />
                                    <stop offset="100%" stop-color="var(--pecahan-out)" stop-opacity="0.04" />
                                </linearGradient>
                                <linearGradient id="pecahan-in-fill-{{ $chartKey }}" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="var(--pecahan-hold)" stop-opacity="0.45" />
                                    <stop offset="100%" stop-color="var(--pecahan-hold)" stop-opacity="0.04" />
                                </linearGradient>
                            </defs>
                            @foreach ([0, 0.5, 1] as $tick)
                                @php $gy = round($top + (1 - $tick) * $plotH, 1); @endphp
                                <line x1="{{ $left }}" y1="{{ $gy }}" x2="{{ $width - $right }}" y2="{{ $gy }}" stroke="var(--border)" stroke-width="1" />
                            @endforeach
                            <polygon points="{{ $outArea }}" fill="url(#pecahan-out-fill-{{ $chartKey }})" />
                            <polygon points="{{ $inArea }}" fill="url(#pecahan-in-fill-{{ $chartKey }})" />
                            <polyline points="{{ implode(' ', $outLine) }}" fill="none" stroke="var(--pecahan-out-ink)" stroke-width="2.75" stroke-linejoin="round" stroke-linecap="round" />
                            <polyline points="{{ implode(' ', $inLine) }}" fill="none" stroke="var(--pecahan-hold-ink)" stroke-width="2.75" stroke-linejoin="round" stroke-linecap="round" />
                            @foreach ($marks as $mark)
                                @if ($mark['released'] > 0)
                                    <circle cx="{{ $mark['x'] }}" cy="{{ $mark['outY'] }}" r="5" fill="#fff" />
                                    <circle cx="{{ $mark['x'] }}" cy="{{ $mark['outY'] }}" r="3.25" fill="var(--pecahan-out)" stroke="var(--pecahan-out-ink)" stroke-width="1.5">
                                        <title>{{ $mark['label'] }} · Dilepaskan {{ number_format($mark['released']) }}</title>
                                    </circle>
                                @endif
                                @if ($mark['received'] > 0)
                                    <circle cx="{{ $mark['x'] }}" cy="{{ $mark['inY'] }}" r="5" fill="#fff" />
                                    <circle cx="{{ $mark['x'] }}" cy="{{ $mark['inY'] }}" r="3.25" fill="var(--pecahan-hold)" stroke="var(--pecahan-hold-ink)" stroke-width="1.5">
                                        <title>{{ $mark['label'] }} · Didapat {{ number_format($mark['received']) }}</title>
                                    </circle>
                                @endif
                                <text x="{{ $mark['x'] }}" y="{{ $height - 8 }}" text-anchor="middle" fill="var(--text-secondary)" font-size="11">{{ $mark['label'] }}</text>
                            @endforeach
                        </svg>
                    @endif
                @endforeach
            </div>
        </x-card>

        <div class="grid gap-4 md:grid-cols-2">
            <x-card class="h-full">
                <h3 class="mb-1 font-semibold">Status jualan</h3>
                <p class="mb-3 text-xs text-muted">Dikira satu per lot, bukan kuantiti.</p>
                <div class="pecahan-track mb-3 flex h-3.5 overflow-hidden rounded-full">
                    @foreach ($stats['statusMix'] as $row)
                        @if ($row['count'] > 0)
                            <div class="h-full" style="flex: {{ (int) $row['count'] }} 1 0%; background: {{ $tones[$row['tone']] ?? 'var(--status-neutral)' }}" title="{{ $row['label'] }}: {{ $row['count'] }}"></div>
                        @endif
                    @endforeach
                </div>
                <ul class="grid grid-cols-2 gap-2 text-xs">
                    @foreach ($stats['statusMix'] as $row)
                        <li class="flex items-center justify-between gap-2">
                            <span class="flex min-w-0 items-center gap-1.5">
                                <span class="h-2 w-2 shrink-0 rounded-full" style="background: {{ $tones[$row['tone']] ?? 'var(--status-neutral)' }}"></span>
                                <span class="truncate text-muted">{{ $row['label'] }}</span>
                            </span>
                            <span class="font-semibold">{{ $row['count'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
            <x-card class="h-full">
                <h3 class="mb-1 font-semibold">Ke mana kuantiti pergi</h3>
                <p class="mb-3 text-xs text-muted">Jualan yang dihantar syarikat ini, mengikut peranan penerima.</p>
                @if ($stats['destinations'] === [])
                    <p class="text-sm text-muted">Tiada jualan dihantar lagi.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($stats['destinations'] as $group)
                            @if (count($stats['destinations']) > 1)
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $group['unit'] }}</p>
                            @endif
                            <x-rank-bars :items="$group['items']" empty="Tiada jualan dihantar lagi." ink-counts />
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <x-card class="h-full">
                <h3 class="mb-1 font-semibold">Kuantiti mengikut produk</h3>
                <p class="mb-3 text-xs text-muted">Hijau baki di tangan, biru sudah keluar. Unit tidak dicampur.</p>
                @if ($stats['products'] === [])
                    <p class="text-sm text-muted">Tiada lot dipegang.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($stats['products'] as $row)
                            <li>
                                <div class="mb-1 flex items-start justify-between gap-3 text-xs">
                                    <p class="min-w-0 truncate font-semibold">{{ $row['label'] }}</p>
                                    <p class="shrink-0 font-semibold">{{ number_format($row['total']) }} {{ $row['unit'] }}</p>
                                </div>
                                <div class="pecahan-track h-2.5 overflow-hidden rounded-full" style="width: {{ max(8, (int) $row['percent']) }}%">
                                    <div class="flex h-full">
                                        @if ($row['remaining'] > 0)
                                            <div class="h-full" style="flex: {{ (int) $row['remaining'] }} 1 0%; background: var(--pecahan-hold)"></div>
                                        @endif
                                        @if ($row['sent'] > 0)
                                            <div class="h-full" style="flex: {{ (int) $row['sent'] }} 1 0%; background: var(--pecahan-out)"></div>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-1 text-[10px] text-muted">{{ number_format($row['remaining']) }} baki · {{ number_format($row['sent']) }} keluar</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
            <x-card class="h-full">
                <h3 class="mb-1 font-semibold">Jualan mengikut minggu</h3>
                <p class="mb-3 text-xs text-muted">8 minggu kalendar, Isnin hingga Ahad (waktu Malaysia).</p>
                <div class="space-y-4">
                    @foreach ($stats['weeks'] as $series)
                        @if ($series['total'] === 0)
                            <p class="text-sm text-muted">Tiada jualan dalam 8 minggu ini.</p>
                        @else
                            @if (count($stats['weeks']) > 1 && $series['unit'])
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $series['unit'] }}</p>
                            @endif
                            <div class="flex h-36 items-end gap-1 sm:gap-2">
                                @foreach ($series['rows'] as $row)
                                    <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-1">
                                        <p class="text-[10px] font-semibold text-ink">{{ $row['split'] + $row['sold'] > 0 ? number_format($row['split'] + $row['sold']) : '' }}</p>
                                        <div class="flex h-24 w-full items-end justify-center">
                                            @if ($row['split'] + $row['sold'] > 0)
                                                <div class="pecahan-col flex w-full max-w-6 flex-col overflow-hidden rounded-t-md" style="height: {{ max(12, (int) $row['splitPercent'] + (int) $row['soldPercent']) }}%">
                                                    @if ($row['sold'] > 0)
                                                        <div class="pecahan-sold-fill w-full" style="flex: {{ (int) $row['sold'] }} 1 0%" title="Dijual {{ number_format($row['sold']) }}"></div>
                                                    @endif
                                                    @if ($row['split'] > 0)
                                                        <div class="pecahan-split w-full" style="flex: {{ (int) $row['split'] }} 1 0%" title="Dipecahkan {{ number_format($row['split']) }}"></div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-medium text-muted">{{ $row['label'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-muted">Biru: dipecahkan {{ number_format($series['splitTotal']) }} {{ $series['unit'] }} · Kuning: dijual {{ number_format($series['soldTotal']) }} {{ $series['unit'] }}</p>
                        @endif
                    @endforeach
                </div>
            </x-card>
        </div>
    @endif
</div>
