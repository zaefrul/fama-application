<x-layouts.fama title="Utama FAMA">
    <div class="space-y-4">
        <x-page-title title="Utama FAMA" subtitle="Pemantauan prototaip Jejak GPL" />
        <div class="grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-5">
            <x-kpi-card :value="$stats['activeCompanies']" label="Syarikat Aktif" tone="info" href="/fama/companies" />
            <x-kpi-card :value="$stats['qrActive']" label="QR Aktif" tone="neutral" href="/fama/qr" />
            <x-kpi-card :value="$stats['approved']" label="Diluluskan" tone="success" href="/fama/applications?status=APPROVED" />
            <x-kpi-card :value="$stats['pending']" label="Menunggu Pengesahan" tone="warning" href="/fama/applications?status=UNDER_REVIEW" />
            <x-kpi-card :value="$stats['rejected']" label="Ditolak" tone="danger" href="/fama/applications?status=REJECTED" />
        </div>
        <x-card>
            <h2 class="mb-3 font-semibold">Pemantauan Harian Bilangan QR Yang Dijana</h2>
            <div class="grid grid-cols-7 gap-1 text-center text-[10px] sm:gap-2 sm:text-xs">
                @foreach ($stats['dailyQr'] as $row)
                    <div class="min-w-0 rounded-lg bg-surface-muted p-1.5 sm:rounded-xl sm:p-2">
                        <p class="font-semibold">{{ substr($row['day'], 0, 3) }}</p>
                        <p class="text-success">{{ $row['active'] }}</p>
                        <p class="text-warning">{{ $row['inactive'] }}</p>
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-muted">Hijau: QR Aktif · Kuning: QR Belum Aktif</p>
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
