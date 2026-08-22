@php
    $copy = [
        'bm' => [
            'inactiveTitle' => 'QR Belum Diaktifkan',
            'inactiveBody' => 'QR ini telah dijana tetapi belum diaktifkan selepas kelulusan FAMA.',
            'invalid' => 'Kod QR tidak sah.',
            'product' => 'Maklumat Buah',
            'export' => 'Maklumat Eksport',
            'certs' => 'Sijil',
            'certsNote' => 'Contoh sijil yang biasa dipamer pengeksport. Ditanda CONTOH — bukan pengesahan rasmi rekod ini.',
            'sample' => 'CONTOH',
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
            'pamphletTitle' => 'Sijil Jejak Produk Eksport',
            'confidence' => 'Maklumat ini dikeluarkan oleh FAMA untuk pengesahan pembeli.',
            'officialNo' => 'No. Jejak',
            'originShort' => 'Hasil Malaysia',
            'stamp' => 'Cap pengesahan rasmi',
            'fruitType' => 'Jenis Buah',
            'agencies' => 'Agensi Kerajaan',
            'agenciesNote' => 'Logo rasmi yang dibekalkan untuk paparan portal. Bukan sijil produk.',
            'agencyMalaysia' => 'Kerajaan Malaysia',
            'agencyKpkm' => 'KPKM',
            'agencyFama' => 'FAMA',
            'agencyMaha' => 'MAHA 2026',
            'profileTitle' => 'Profil Produk Disahkan',
        ],
        'en' => [
            'inactiveTitle' => 'QR Not Activated',
            'inactiveBody' => 'This QR has been generated but is not active until FAMA approval.',
            'invalid' => 'Invalid QR code.',
            'product' => 'Fruit information',
            'export' => 'Export information',
            'certs' => 'Certificates',
            'certsNote' => 'Sample certificates exporters typically display. Marked CONTOH — not official verification of this record.',
            'sample' => 'SAMPLE',
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
            'pamphletTitle' => 'Export product traceability certificate',
            'confidence' => 'This information is issued by FAMA for buyer verification.',
            'officialNo' => 'Trace no.',
            'originShort' => 'Malaysian produce',
            'stamp' => 'Official verification mark',
            'fruitType' => 'Fruit type',
            'agencies' => 'Government agencies',
            'agenciesNote' => 'Official marks supplied for this portal. Not a product certificate.',
            'agencyMalaysia' => 'Government of Malaysia',
            'agencyKpkm' => 'MAFS',
            'agencyFama' => 'FAMA',
            'agencyMaha' => 'MAHA 2026',
            'profileTitle' => 'Verified product profile',
        ],
        'zh' => [
            'inactiveTitle' => 'QR 尚未激活',
            'inactiveBody' => '此二维码已生成，但尚未在 FAMA 批准后激活。',
            'invalid' => '无效的二维码。',
            'product' => '水果信息',
            'export' => '出口信息',
            'certs' => '证书',
            'certsNote' => '出口商常展示的证书样例。标有 CONTOH — 非本记录的正式核验。',
            'sample' => '样例',
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
            'pamphletTitle' => '出口产品溯源证书',
            'confidence' => '本信息由 FAMA 签发，供买方核实。',
            'officialNo' => '溯源编号',
            'originShort' => '马来西亚农产品',
            'stamp' => '官方核实印章',
            'fruitType' => '水果种类',
            'agencies' => '政府机构',
            'agenciesNote' => '本门户提供的官方标志。不是产品证书。',
            'agencyMalaysia' => '马来西亚政府',
            'agencyKpkm' => 'KPKM',
            'agencyFama' => 'FAMA',
            'agencyMaha' => 'MAHA 2026',
            'profileTitle' => '已核实产品档案',
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
    <div class="flex min-h-dvh flex-col bg-surface-dark">
        <x-gov-masthead />

        <header class="mx-auto w-full max-w-lg px-4 pt-5 lg:max-w-6xl">
            <div class="flex items-start justify-between gap-3 text-white">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="shrink-0 rounded-2xl bg-white px-2.5 py-2">
                        <img
                            src="{{ asset('logos/logo-fama.png') }}"
                            alt="FAMA"
                            width="56"
                            height="56"
                            class="trace-fama-logo"
                            style="width:56px;height:56px;max-width:56px;max-height:56px;object-fit:contain"
                        >
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
        </header>

        <main class="mx-auto w-full max-w-lg flex-1 px-4 pb-10 pt-4 lg:max-w-6xl">
            @if (! $qr)
                <x-card class="mx-auto max-w-lg text-center"><p class="text-lg font-bold">{{ $t['invalid'] }}</p></x-card>
            @elseif (! $active)
                <x-card class="mx-auto max-w-lg space-y-4 py-8 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-warning text-4xl font-bold text-white">!</div>
                    <h1 class="text-2xl font-bold">{{ $t['inactiveTitle'] }}</h1>
                    <p class="text-sm text-muted">{{ $t['inactiveBody'] }}</p>
                    <p class="rounded-xl bg-surface-muted px-3 py-2 text-sm font-semibold">QR ID: {{ $qr->qr_code }}</p>
                </x-card>
            @else
            <div class="lg:hidden">
                <article class="trace-pamphlet overflow-hidden rounded-sm">
                    <div class="bg-surface-dark px-5 py-4 text-center text-white">
                        <p class="text-[10px] font-semibold tracking-[0.22em] text-warning">{{ $t['verifiedKicker'] }}</p>
                        <h1 class="mt-1 text-lg font-bold tracking-wide">{{ $t['pamphletTitle'] }}</h1>
                        <p class="mt-1 text-xs text-white/70">Jejak Eksport Hasil Pertanian Malaysia</p>
                    </div>
                    <div class="trace-gold-rule"></div>

                    <div class="relative bg-surface-dark">
                        <img
                            src="{{ $heroImage }}"
                            alt="{{ $productTitle }}"
                            width="640"
                            height="200"
                            class="trace-produce-hero"
                            style="width:100%;height:200px;max-height:200px;object-fit:cover;display:block"
                        >
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-surface-dark via-surface-dark/70 to-transparent px-5 pb-4 pt-16">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-warning">{{ $t['productName'] }}</p>
                            <p class="text-2xl font-bold leading-tight text-white">{{ $productTitle }}</p>
                        </div>
                    </div>

                    <div class="bg-brand px-5 py-3 text-center text-white">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80">{{ $t['verifiedKicker'] }}</p>
                        <p class="text-lg font-bold leading-tight">{{ $t['verifiedTitle'] }}</p>
                        <p class="text-sm text-white/90">{{ $t['verifiedBody'] }}</p>
                    </div>
                    <div class="trace-gold-rule"></div>

                    <div class="px-5 py-4">
                        <p class="text-center text-sm leading-relaxed text-ink">{{ $t['confidence'] }}</p>
                        <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">{{ $t['officialNo'] }}</dt>
                                <dd class="mt-0.5 break-all text-[11px] font-bold text-brand">{{ $qr->qr_code }}</dd>
                            </div>
                            <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">Gred</dt>
                                <dd class="mt-0.5 text-sm font-bold text-brand">{{ $application?->grade }}</dd>
                            </div>
                            <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">{{ $t['origin'] }}</dt>
                                <dd class="mt-0.5 text-sm font-bold text-brand">{{ $t['malaysia'] }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mx-5 border-t border-warning/30"></div>

                    <dl class="px-5 py-3">
                        <x-data-row :label="$t['exporter']" :value="$application?->company?->name" />
                        <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
                        <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
                        <x-data-row :label="$t['scans']" :value="number_format((int) $accessCount)" />
                    </dl>

                    <div class="px-5 pb-5 pt-1 text-center">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $t['stamp'] }}</p>
                        <div class="mx-auto w-fit rounded-2xl border border-warning/40 bg-white px-4 py-3">
                            <x-qr-preview :value="$publicUrl" :size="120" :caption="false" />
                        </div>
                        <p class="mt-2 text-[11px] font-semibold tracking-wide text-brand">{{ $qr->qr_code }}</p>
                    </div>
                </article>

                <div class="mt-4 space-y-3">
                    <section class="trace-pamphlet overflow-hidden rounded-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['agencies'] }}</h2>
                        @include('trace.partials.agencies')
                    </section>

                    <section class="trace-pamphlet overflow-hidden rounded-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['product'] }}</h2>
                        @include('trace.partials.product-dl')
                    </section>

                    <section class="trace-pamphlet overflow-hidden rounded-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['export'] }}</h2>
                        @include('trace.partials.export-dl')
                    </section>

                    <section class="trace-pamphlet overflow-hidden rounded-sm">
                        <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['certs'] }}</h2>
                        @include('trace.partials.certificates', ['certCols' => 'grid-cols-2'])
                    </section>

                    @if (count($nutrition))
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['nutrition'] }}</h2>
                            @include('trace.partials.nutrition')
                        </section>
                    @endif

                    <p class="text-center text-[11px] text-white/60">{{ $t['scanBody'] }}</p>
                    <p class="text-center text-[10px] text-white/45">Foto produk: Wikimedia Commons · CC BY / CC BY-SA</p>
                    <p class="text-center text-[10px] text-white/45">Contoh sijil fitosanitasi: Wikimedia Commons · CC BY 2.0</p>
                </div>
            </div>

            <div class="trace-profile hidden lg:block">
                <article class="trace-pamphlet overflow-hidden rounded-sm">
                    <div class="grid grid-cols-12">
                        <div class="relative col-span-4 min-h-[360px] border-r-2 border-warning bg-surface-dark">
                            <img
                                src="{{ $heroImage }}"
                                alt="{{ $productTitle }}"
                                width="480"
                                height="360"
                                class="trace-produce-portrait"
                                style="width:100%;height:100%;max-height:none;object-fit:cover"
                            >
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-surface-dark via-surface-dark/80 to-transparent px-5 pb-4 pt-20">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-warning">{{ $t['originShort'] }}</p>
                                <p class="mt-1 text-lg font-bold leading-tight text-white">{{ $productTitle }}</p>
                                <p class="mt-1 text-xs text-white/80">{{ $t['verifiedBody'] }}</p>
                            </div>
                        </div>
                        <div class="col-span-8 flex min-w-0 flex-col">
                            <div class="bg-surface-dark px-6 py-4 text-white">
                                <p class="text-[10px] font-semibold tracking-[0.22em] text-warning">{{ $t['verifiedKicker'] }}</p>
                                <p class="mt-1 text-sm font-semibold text-white/80">{{ $t['pamphletTitle'] }}</p>
                                <h1 class="mt-1 text-2xl font-bold leading-tight">{{ $t['profileTitle'] }}</h1>
                            </div>
                            <div class="trace-gold-rule"></div>
                            <div class="flex flex-1 flex-col justify-center px-6 py-5">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $t['productName'] }}</p>
                                <p class="mt-1 text-3xl font-bold leading-tight text-ink">{{ $productTitle }}</p>
                                <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                                    <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                        <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">{{ $t['officialNo'] }}</dt>
                                        <dd class="mt-0.5 break-all text-[11px] font-bold text-brand">{{ $qr->qr_code }}</dd>
                                    </div>
                                    <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                        <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">Gred</dt>
                                        <dd class="mt-0.5 text-sm font-bold text-brand">{{ $application?->grade }}</dd>
                                    </div>
                                    <div class="rounded-xl border border-warning/40 bg-white/80 px-2 py-2.5">
                                        <dt class="text-[10px] font-semibold uppercase tracking-wide text-muted">{{ $t['origin'] }}</dt>
                                        <dd class="mt-0.5 text-sm font-bold text-brand">{{ $t['malaysia'] }}</dd>
                                    </div>
                                </dl>
                                <dl class="mt-2">
                                    <x-data-row :label="$t['exporter']" :value="$application?->company?->name" />
                                    <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
                                    <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
                                </dl>
                            </div>
                            <div class="bg-brand px-6 py-3 text-white">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80">{{ $t['verifiedKicker'] }}</p>
                                <p class="text-lg font-bold leading-tight">{{ $t['verifiedTitle'] }}</p>
                                <p class="text-sm text-white/90">{{ $t['verifiedBody'] }}</p>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="mt-6 grid grid-cols-12 items-start gap-6">
                    <div class="col-span-8 space-y-4">
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['product'] }}</h2>
                            @include('trace.partials.product-dl')
                        </section>
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['export'] }}</h2>
                            @include('trace.partials.export-dl')
                        </section>
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['certs'] }}</h2>
                            @include('trace.partials.certificates', ['certCols' => 'grid-cols-3'])
                        </section>
                        @if (count($nutrition))
                            <section class="trace-pamphlet overflow-hidden rounded-sm">
                                <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['nutrition'] }}</h2>
                                @include('trace.partials.nutrition')
                            </section>
                        @endif
                    </div>

                    <aside class="col-span-4 space-y-4 lg:sticky lg:top-4">
                        <section class="trace-pamphlet overflow-hidden rounded-sm px-5 py-5 text-center">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $t['stamp'] }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-ink">{{ $t['confidence'] }}</p>
                            <div class="mx-auto mt-4 w-fit rounded-2xl border border-warning/40 bg-white px-4 py-3">
                                <x-qr-preview :value="$publicUrl" :size="148" :caption="false" />
                            </div>
                            <p class="mt-2 text-[11px] font-semibold tracking-wide text-brand">{{ $qr->qr_code }}</p>
                            <p class="mt-3 text-xs text-muted">{{ $t['scans'] }}: {{ number_format((int) $accessCount) }}</p>
                        </section>
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['agencies'] }}</h2>
                            @include('trace.partials.agencies')
                        </section>
                    </aside>
                </div>

                <p class="mt-6 text-center text-[11px] text-white/60">{{ $t['scanBody'] }}</p>
                <p class="text-center text-[10px] text-white/45">Foto produk: Wikimedia Commons · CC BY / CC BY-SA</p>
                <p class="text-center text-[10px] text-white/45">Contoh sijil fitosanitasi: Wikimedia Commons · CC BY 2.0</p>
            </div>
            @endif
        </main>
        <x-gov-footer />
    </div>
</body>
</html>
