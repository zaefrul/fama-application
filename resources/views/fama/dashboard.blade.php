<x-layouts.fama title="Utama FAMA">
    <div class="space-y-4">
        <x-page-title title="Utama FAMA" subtitle="Pemantauan prototaip Jejak GPL" />

        <div class="grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-5">
            <x-kpi-card :value="$stats['activeCompanies']" label="Syarikat Aktif" tone="info" href="/fama/companies" />
            <x-kpi-card :value="$stats['exporters']" label="Pengeksport" tone="teal" href="/fama/companies" />
            <x-kpi-card :value="$stats['qrRequests']" label="Permohonan QR" tone="violet" href="/fama/applications" />
            <x-kpi-card :value="$stats['qrActive']" label="QR Aktif" tone="success" href="/fama/qr" />
            <x-kpi-card :value="$stats['qrInactive']" label="QR Belum Aktif" tone="warning" href="/fama/qr" />
            <x-kpi-card :value="$stats['approved']" label="Diluluskan" tone="success" href="/fama/applications?status=APPROVED" />
            <x-kpi-card :value="$stats['pending']" label="Menunggu Pengesahan" tone="warning" href="/fama/applications?status=UNDER_REVIEW" />
            <x-kpi-card :value="$stats['rejected']" label="Ditolak" tone="danger" href="/fama/applications?status=REJECTED" />
            <x-kpi-card :value="$stats['uniqueFruits']" label="Buah unik" tone="rose" href="/fama/companies" />
            <x-kpi-card :value="$stats['uniqueDestinations']" label="Destinasi" tone="neutral" href="/fama/applications" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <x-card>
                <h2 class="mb-1 font-semibold">10 buah paling kerap</h2>
                <p class="mb-3 text-xs text-muted">Disusun mengikut bilangan permohonan QR.</p>
                <x-rank-bars :items="$stats['topFruits']" empty="Tiada permohonan buah lagi." />
            </x-card>
            <x-card>
                <h2 class="mb-1 font-semibold">Destinasi eksport</h2>
                <p class="mb-3 text-xs text-muted">Negara destinasi daripada permohonan QR.</p>
                <x-rank-bars :items="$stats['topDestinations']" empty="Tiada destinasi direkodkan." />
            </x-card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <x-card>
                <h2 class="mb-1 font-semibold">Status permohonan</h2>
                <p class="mb-3 text-xs text-muted">Campuran draf, semakan, lulus dan tolak.</p>
                <div class="mb-3 flex h-3 overflow-hidden rounded-full bg-surface-muted">
                    @foreach ($stats['statusMix'] as $row)
                        @if ($row['count'] > 0)
                            <div
                                class="h-full {{ $row['tone'] === 'success' ? 'bg-success' : ($row['tone'] === 'danger' ? 'bg-danger' : ($row['tone'] === 'info' ? 'bg-info' : 'bg-neutral')) }}"
                                style="width: {{ $row['percent'] }}%"
                                title="{{ $row['label'] }}: {{ $row['count'] }}"
                            ></div>
                        @endif
                    @endforeach
                </div>
                <ul class="grid grid-cols-2 gap-2 text-xs">
                    @foreach ($stats['statusMix'] as $row)
                        <li class="flex items-center justify-between gap-2">
                            <span class="flex min-w-0 items-center gap-1.5">
                                <span class="h-2 w-2 shrink-0 rounded-full {{ $row['tone'] === 'success' ? 'bg-success' : ($row['tone'] === 'danger' ? 'bg-danger' : ($row['tone'] === 'info' ? 'bg-info' : 'bg-neutral')) }}"></span>
                                <span class="truncate text-muted">{{ $row['label'] }}</span>
                            </span>
                            <span class="font-semibold">{{ $row['count'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
            <x-card>
                <h2 class="mb-1 font-semibold">Syarikat mengikut negeri</h2>
                <p class="mb-3 text-xs text-muted">Termasuk syarikat aktif dan rekod FAMA.</p>
                <x-rank-bars :items="$stats['companiesByState']" empty="Tiada negeri direkodkan." />
                @if ($stats['famaCompanies'] > 0 || $stats['certificates'] > 0)
                    <p class="mt-3 text-xs text-muted">
                        @if ($stats['famaCompanies'] > 0)
                            Rekod FAMA: {{ $stats['famaCompanies'] }}
                        @endif
                        @if ($stats['famaCompanies'] > 0 && $stats['certificates'] > 0)
                            ·
                        @endif
                        @if ($stats['certificates'] > 0)
                            Sijil dimuat naik: {{ $stats['certificates'] }}
                        @endif
                    </p>
                @endif
            </x-card>
        </div>

        <x-card>
            <h2 class="mb-3 font-semibold">Pemantauan Harian Bilangan QR Yang Dijana</h2>
            <div class="flex h-36 items-end gap-1.5 sm:gap-2">
                @foreach ($stats['dailyQr'] as $row)
                    <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-1">
                        <p class="text-[10px] font-semibold text-ink">{{ $row['active'] + $row['inactive'] }}</p>
                        <div class="flex h-24 w-full items-end justify-center gap-0.5">
                            <div class="w-1/2 max-w-3 rounded-t-md bg-success/80" style="height: {{ max($row['active'] ? 8 : 0, $row['activePercent']) }}%"></div>
                            <div class="w-1/2 max-w-3 rounded-t-md bg-warning/80" style="height: {{ max($row['inactive'] ? 8 : 0, $row['inactivePercent']) }}%"></div>
                        </div>
                        <p class="text-[10px] font-medium text-muted">{{ $row['label'] }}</p>
                    </div>
                @endforeach
            </div>
            @if (array_sum(array_map(fn ($row) => $row['active'] + $row['inactive'], $stats['dailyQr'])) === 0)
                <p class="mt-2 text-xs text-muted">Tiada QR dijana dalam 7 hari lepas.</p>
            @else
                <p class="mt-2 text-xs text-muted">Hijau: QR Aktif · Kuning: QR Belum Aktif · 7 hari kalendar (waktu Malaysia)</p>
            @endif
        </x-card>

        <div class="grid grid-cols-2 gap-2 sm:gap-3">
            <x-kpi-card :value="$stats['accessSevenDays']" label="Imbasan QR · 7 hari" tone="info" href="/fama/qr" />
            <x-kpi-card :value="$stats['accessTotal']" label="Imbasan QR · keseluruhan" tone="neutral" href="/fama/qr" />
        </div>
        <x-card>
            <h2 class="mb-1 font-semibold">Imbasan Halaman Awam QR</h2>
            <p class="mb-4 text-xs text-muted">Setiap buka halaman awam untuk kod QR yang wujud dikira sebagai satu imbasan. Pengunjung tidak dinamakan.</p>
            <x-qr-access-chart
                :week="$stats['accessWeek']"
                :last-week="$stats['accessLastWeek']"
                :daily="$stats['dailyAccess']"
                :top="$stats['topQrAccess']"
            />
        </x-card>
    </div>
</x-layouts.fama>
