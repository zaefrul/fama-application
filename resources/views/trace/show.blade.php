@php
    $copy = [
        'bm' => [
            'inactiveTitle' => 'QR Belum Diaktifkan',
            'inactiveBody' => 'QR ini telah dijana tetapi belum diaktifkan selepas kelulusan FAMA.',
            'invalid' => 'Kod QR tidak sah.',
            'product' => 'Maklumat Buah',
            'export' => 'Maklumat Eksport',
            'certs' => 'Sijil',
            'nutrition' => 'Nutrisi',
            'verifiedKicker' => 'Pengesahan rasmi FAMA',
            'verifiedTitle' => 'Produk Disahkan Tulen',
            'verifiedBody' => 'Disahkan oleh FAMA',
            'productName' => 'Nama produk',
            'origin' => 'Negara asal',
            'exporter' => 'Pengeksport',
            'farm' => 'Ladang',
            'destination' => 'Destinasi',
            'scans' => 'Imbasan',
            'scanBody' => 'QR ini telah dibuka oleh pengunjung awam.',
            'malaysia' => 'Malaysia',
        ],
        'en' => [
            'inactiveTitle' => 'QR Not Activated',
            'inactiveBody' => 'This QR has been generated but is not active until FAMA approval.',
            'invalid' => 'Invalid QR code.',
            'product' => 'Fruit information',
            'export' => 'Export information',
            'certs' => 'Certificates',
            'nutrition' => 'Nutrition',
            'verifiedKicker' => 'Official FAMA verification',
            'verifiedTitle' => 'Authentic product verified',
            'verifiedBody' => 'Verified by FAMA',
            'productName' => 'Product name',
            'origin' => 'Country of origin',
            'exporter' => 'Exporter',
            'farm' => 'Farm',
            'destination' => 'Destination',
            'scans' => 'Scans',
            'scanBody' => 'This QR has been opened by public visitors.',
            'malaysia' => 'Malaysia',
        ],
        'zh' => [
            'inactiveTitle' => 'QR 尚未激活',
            'inactiveBody' => '此二维码已生成，但尚未在 FAMA 批准后激活。',
            'invalid' => '无效的二维码。',
            'product' => '水果信息',
            'export' => '出口信息',
            'certs' => '证书',
            'nutrition' => '营养',
            'verifiedKicker' => 'FAMA 官方核实',
            'verifiedTitle' => '产品已核实为正品',
            'verifiedBody' => '经 FAMA 核实',
            'productName' => '产品名称',
            'origin' => '原产国',
            'exporter' => '出口商',
            'farm' => '农场',
            'destination' => '目的地',
            'scans' => '扫码次数',
            'scanBody' => '此二维码已被公众打开。',
            'malaysia' => '马来西亚',
        ],
    ];
    $t = $copy[$lang];
    $productTitle = trim(($application?->produceType?->name ?? '').(($application?->variety) ? ' · '.$application->variety : ''));
    $langUrl = fn (string $code) => url('/trace/'.$qrCode).'?lang='.$code;
@endphp
<!DOCTYPE html>
<html lang="{{ $lang === 'en' ? 'en' : 'ms' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jejak GPL · {{ $qrCode }}</title>
    <x-assets />
</head>
<body class="min-h-dvh antialiased">
    <div class="flex min-h-dvh flex-col bg-brand/10">
        <x-gov-masthead />

        <header class="bg-surface-dark text-white">
            <div class="mx-auto flex max-w-lg items-start justify-between gap-3 px-4 py-5">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="shrink-0 rounded-2xl bg-white px-2.5 py-2 shadow-sm">
                        <img src="{{ asset('logos/logo-fama.png') }}" alt="FAMA" class="h-14 w-auto object-contain sm:h-16">
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold tracking-[0.18em] text-warning">LAMAN RASMI FAMA</p>
                        <p class="mt-0.5 text-lg font-bold leading-tight">Sistem Jejak GPL</p>
                        <p class="text-xs leading-snug text-white/75">Lembaga Pemasaran Pertanian Persekutuan</p>
                    </div>
                </div>
                <div class="shrink-0 space-x-2 pt-1 text-[11px] font-semibold">
                    <a href="{{ $langUrl('bm') }}" class="{{ $lang === 'bm' ? 'text-warning' : 'text-white/65' }}">BM</a>
                    <a href="{{ $langUrl('zh') }}" class="{{ $lang === 'zh' ? 'text-warning' : 'text-white/65' }}">中文</a>
                    <a href="{{ $langUrl('en') }}" class="{{ $lang === 'en' ? 'text-warning' : 'text-white/65' }}">EN</a>
                </div>
            </div>
            <p class="bg-black/25 px-4 py-2 text-center text-[11px] font-semibold tracking-[0.16em] text-white">JEJAK EKSPORT HASIL PERTANIAN</p>
            <div class="h-1 bg-warning" aria-hidden="true"></div>
        </header>

        @if ($qr && $active)
            <div class="bg-brand text-white shadow-sm">
                <div class="mx-auto flex max-w-lg items-center gap-3 px-4 py-3.5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-brand">
                        <x-icon name="shield" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-white/80">{{ $t['verifiedKicker'] }}</p>
                        <p class="text-base font-bold leading-tight">{{ $t['verifiedTitle'] }}</p>
                        <p class="text-xs text-white/90">{{ $t['verifiedBody'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        <main class="mx-auto w-full max-w-lg flex-1 px-4 pb-10 pt-5">
            @if (! $qr)
                <x-card class="text-center"><p class="text-lg font-bold">{{ $t['invalid'] }}</p></x-card>
            @elseif (! $active)
                <x-card class="space-y-4 py-8 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-warning text-4xl font-bold text-white">!</div>
                    <h1 class="text-2xl font-bold">{{ $t['inactiveTitle'] }}</h1>
                    <p class="text-sm text-muted">{{ $t['inactiveBody'] }}</p>
                    <p class="rounded-xl bg-surface-muted px-3 py-2 text-sm font-semibold">QR ID: {{ $qr->qr_code }}</p>
                </x-card>
            @else
                <div class="space-y-4">
                    <x-card class="overflow-hidden p-0">
                        <div class="flex items-center gap-4 px-4 py-4">
                            <img src="{{ $heroImage }}" alt="{{ $productTitle }}" class="h-24 w-24 shrink-0 rounded-full border-4 border-brand/20 object-cover">
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-brand">{{ $t['productName'] }}</p>
                                <h1 class="text-xl font-bold leading-tight text-ink">{{ $productTitle }}</h1>
                                <p class="mt-1 text-sm text-muted">{{ $t['origin'] }}: <span class="font-semibold text-ink">{{ $t['malaysia'] }}</span></p>
                                <p class="text-sm text-muted">{{ $t['exporter'] }}: <span class="font-semibold text-ink">{{ $application?->company?->name }}</span></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-px bg-brand/15">
                            <div class="bg-white px-4 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-brand">{{ $t['farm'] }}</p>
                                <p class="mt-0.5 text-sm font-semibold leading-snug">{{ $application?->farm_name ?: '—' }}</p>
                            </div>
                            <div class="bg-white px-4 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-brand">{{ $t['destination'] }}</p>
                                <p class="mt-0.5 text-sm font-semibold leading-snug">{{ $application?->destination_country ?: '—' }}</p>
                            </div>
                        </div>
                    </x-card>

                    <div class="flex items-center justify-between gap-3">
                        <x-status-badge :label="'Gred '.$application->grade" tone="success" />
                        <p class="text-xs font-semibold text-brand">{{ number_format((int) $accessCount) }} {{ $t['scans'] }}</p>
                    </div>

                    <x-qr-preview :value="$publicUrl" :size="160" />

                    <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['product'] }}</h2>
                        <dl class="px-4">
                            <x-data-row :label="$t['exporter']" :value="$application?->company?->name" />
                            <x-data-row :label="$lang === 'en' ? 'Fruit type' : ($lang === 'zh' ? '水果种类' : 'Jenis Buah')" :value="$application?->produceType?->name" />
                            <x-data-row label="Gred" :value="$application?->grade" />
                            <x-data-row label="Saiz" :value="$application?->size" />
                            <x-data-row label="Berat" :value="$application?->quantity.' '.$application?->quantity_unit" />
                            <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
                            <x-data-row label="No. Sijil CoC" :value="$application?->coc_number" />
                        </dl>
                    </section>

                    <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['export'] }}</h2>
                        <dl class="px-4">
                            <x-data-row label="Tarikh Eksport" :value="$application?->export_date?->toDateString()" />
                            <x-data-row label="Alamat Pengeksport" :value="$application?->company?->address" />
                            <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
                            <x-data-row label="Pengimport" :value="$application?->importer_name" />
                            <x-data-row label="Alamat Pengimport" :value="$application?->importer_address" />
                        </dl>
                    </section>

                    <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['certs'] }}</h2>
                        <div class="grid grid-cols-2 gap-2 p-3">
                            @foreach ($certificates as $certificate)
                                <a href="{{ $certificate->document_path }}" target="_blank" rel="noreferrer" class="overflow-hidden rounded-xl border border-border bg-surface-muted p-2 text-xs">
                                    <x-document-preview :src="$certificate->document_path" :alt="$certificate->type" class="mb-2 h-20 w-full object-cover" />
                                    <p class="font-semibold">SIJIL {{ $certificate->type }}</p>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    @if (count($nutrition))
                        <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['nutrition'] }}</h2>
                            <table class="w-full text-left text-sm">
                                <tbody>
                                    @foreach ($nutrition as $row)
                                        <tr class="border-t border-border">
                                            <td class="px-4 py-1.5">{{ $row['name'] }}</td>
                                            <td class="font-medium">{{ $row['amount'] }}</td>
                                            <td class="pr-4 text-muted">{{ $row['dailyPercent'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </section>
                    @endif

                    <p class="text-center text-[11px] text-muted">{{ $t['scanBody'] }}</p>
                    <p class="text-center text-[10px] text-muted">Foto produk: Wikimedia Commons · CC BY / CC BY-SA</p>
                </div>
            @endif
        </main>
        <x-gov-footer />
    </div>
</body>
</html>
