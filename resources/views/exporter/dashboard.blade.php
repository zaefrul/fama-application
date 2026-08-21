<x-layouts.exporter title="Utama">
    <div class="space-y-4">
        <x-card class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm text-muted">Selamat datang,</p>
                <h1 class="flex min-w-0 items-center gap-2 text-base font-bold sm:text-lg">
                    <span class="truncate">{{ $company?->name ?? auth()->user()->name }}</span>
                    <span class="shrink-0 text-sm text-brand" aria-hidden>✓</span>
                </h1>
            </div>
            <a href="{{ route('exporter.company') }}" class="shrink-0 self-start rounded-full border border-border px-3 py-1.5 text-xs font-semibold text-brand">Kemas Kini Profil</a>
        </x-card>
        <div class="grid grid-cols-2 gap-3">
            <x-kpi-card :value="$stats['qrActive']" label="QR Aktif" tone="success" href="/exporter/qr" />
            <x-kpi-card :value="$stats['qrInactive']" label="QR Belum Aktif" tone="warning" href="/exporter/qr" />
        </div>
        <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <x-kpi-card :value="$stats['totalApplications']" label="Jumlah Permohonan" tone="warning" href="/exporter/applications" />
            <x-kpi-card :value="$stats['approved']" label="Permohonan Lulus" tone="success" href="/exporter/applications" />
            <x-kpi-card :value="$stats['rejected']" label="Permohonan Gagal" tone="danger" href="/exporter/applications" />
        </div>
        <x-card>
            <h2 class="mb-3 font-semibold">Tindakan Pantas</h2>
            <div class="grid grid-cols-2 gap-2 md:grid-cols-3">
                <a href="{{ route('exporter.produce') }}"><x-button type="button" class="w-full">+ Maklumat Buah</x-button></a>
                <a href="{{ route('exporter.certificates') }}"><x-button type="button" variant="secondary" class="w-full">+ Sijil</x-button></a>
                <a href="{{ route('exporter.qr') }}"><x-button type="button" variant="secondary" class="w-full">Cetak QR</x-button></a>
            </div>
        </x-card>
        <div class="grid gap-4 md:grid-cols-2">
            <x-card>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-semibold">Permohonan</h2>
                    <a href="{{ route('exporter.applications') }}" class="text-sm font-semibold text-brand">Lihat semua →</a>
                </div>
                <ul class="space-y-2">
                    @foreach ($applications->take(4) as $application)
                        <li>
                            <x-application-card
                                :href="route('exporter.applications.show', $application)"
                                :title="$application->application_no.' · '.($application->produceType?->name).' '.$application->variety"
                                :subtitle="'Dihantar pada '.($application->submitted_at?->locale('ms')->translatedFormat('d F Y') ?? '—')"
                                :status="$application->status"
                            />
                        </li>
                    @endforeach
                </ul>
            </x-card>
            @if ($featuredQr && $featuredApp)
                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold">Kod QR</h2>
                        <a href="{{ route('exporter.qr') }}" class="text-sm font-semibold text-brand">Lihat semua →</a>
                    </div>
                    <x-qr-preview :value="$publicUrl" :size="180" />
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-muted">ID Kod QR</dt><dd class="font-semibold">{{ $featuredQr->qr_code }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">Produk</dt><dd class="font-semibold">{{ $featuredApp->produceType?->name }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">Gred</dt><dd class="font-semibold">{{ $featuredApp->grade }}</dd></div>
                    </dl>
                </x-card>
            @endif
        </div>
        <x-card>
            <h2 class="mb-3 font-semibold">Galeri</h2>
            <div class="overflow-hidden rounded-2xl bg-surface-muted">
                @if ($gallery->first())
                    <img src="{{ $gallery->first()->file_path }}" alt="{{ $gallery->first()->description }}" class="h-44 w-full object-cover">
                @else
                    <div class="flex h-44 items-center justify-center text-sm text-muted">Tiada gambar</div>
                @endif
            </div>
        </x-card>
    </div>
</x-layouts.exporter>
