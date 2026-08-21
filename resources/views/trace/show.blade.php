@php
    $copy = [
        'bm' => ['inactiveTitle' => 'QR Belum Diaktifkan', 'inactiveBody' => 'QR ini telah dijana tetapi belum diaktifkan selepas kelulusan FAMA.', 'invalid' => 'Kod QR tidak sah.', 'product' => 'Maklumat Buah', 'export' => 'Maklumat Eksport', 'certs' => 'Sijil', 'nutrition' => 'Nutrisi'],
        'en' => ['inactiveTitle' => 'QR Not Activated', 'inactiveBody' => 'This QR has been generated but is not active until FAMA approval.', 'invalid' => 'Invalid QR code.', 'product' => 'Fruit information', 'export' => 'Export information', 'certs' => 'Certificates', 'nutrition' => 'Nutrition'],
        'zh' => ['inactiveTitle' => 'QR 尚未激活', 'inactiveBody' => '此二维码已生成，但尚未在 FAMA 批准后激活。', 'invalid' => '无效的二维码。', 'product' => '水果信息', 'export' => '出口信息', 'certs' => '证书', 'nutrition' => '营养'],
    ];
    $t = $copy[$lang];
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
    <div class="relative mx-auto min-h-dvh max-w-lg overflow-hidden bg-[linear-gradient(180deg,#ffffff_0%,#f3f8f5_100%)] px-4 pb-16 pt-5">
        <header class="mb-5 flex items-center justify-between">
            <img src="{{ asset('logos/jejak-gpl.png') }}" alt="Sistem Jejak GPL" class="h-8 w-auto max-w-[46vw] object-contain sm:h-10">
            <div class="space-x-2 text-xs font-semibold">
                <a href="{{ url('/trace/'.$qrCode) }}?lang=bm" class="{{ $lang === 'bm' ? 'text-brand' : 'text-muted' }}">BM</a>
                <a href="{{ url('/trace/'.$qrCode) }}?lang=zh" class="{{ $lang === 'zh' ? 'text-brand' : 'text-muted' }}">中文</a>
                <a href="{{ url('/trace/'.$qrCode) }}?lang=en" class="{{ $lang === 'en' ? 'text-brand' : 'text-muted' }}">EN</a>
            </div>
        </header>
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
                <div>
                    <h1 class="text-2xl font-bold leading-tight sm:text-3xl">{{ $application?->produceType?->name }}</h1>
                    <div class="mt-2 flex gap-2">
                        <x-status-badge :label="'Gred '.$application->grade" tone="success" />
                        <x-status-badge label="Malaysia" tone="info" />
                    </div>
                </div>
                <x-qr-preview :value="$publicUrl" :size="180" />
                <x-card class="px-5">
                    <h2 class="mb-1 font-semibold">{{ $t['product'] }}</h2>
                    <dl>
                        <x-data-row label="Exporter" :value="$application?->company?->name" />
                        <x-data-row :label="$lang === 'en' ? 'Fruit type' : 'Jenis Buah'" :value="$application?->produceType?->name" />
                        <x-data-row label="Gred" :value="$application?->grade" />
                        <x-data-row label="Saiz" :value="$application?->size" />
                        <x-data-row label="Berat" :value="$application?->quantity.' '.$application?->quantity_unit" />
                        <x-data-row label="Destinasi" :value="$application?->destination_country" />
                        <x-data-row label="No. Sijil CoC" :value="$application?->coc_number" />
                    </dl>
                </x-card>
                <x-card class="px-5">
                    <h2 class="mb-1 font-semibold">{{ $t['export'] }}</h2>
                    <dl>
                        <x-data-row label="Tarikh Eksport" :value="$application?->export_date?->toDateString()" />
                        <x-data-row label="Alamat Pengeksport" :value="$application?->company?->address" />
                        <x-data-row label="Nama Ladang" :value="$application?->farm_name" />
                        <x-data-row label="Pengimport" :value="$application?->importer_name" />
                        <x-data-row label="Alamat Pengimport" :value="$application?->importer_address" />
                    </dl>
                </x-card>
                <x-card>
                    <h2 class="mb-3 font-semibold">{{ $t['certs'] }}</h2>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($certificates as $certificate)
                            <a href="{{ $certificate->document_path }}" target="_blank" rel="noreferrer" class="overflow-hidden rounded-xl border border-border bg-surface-muted p-2 text-xs">
                                <x-document-preview :src="$certificate->document_path" :alt="$certificate->type" class="mb-2 h-20 w-full object-cover" />
                                <p class="font-semibold">SIJIL {{ $certificate->type }}</p>
                            </a>
                        @endforeach
                    </div>
                </x-card>
                @if (count($nutrition))
                    <x-card>
                        <h2 class="mb-2 font-semibold">{{ $t['nutrition'] }}</h2>
                        <table class="w-full text-left text-sm">
                            <tbody>
                                @foreach ($nutrition as $row)
                                    <tr class="border-t border-border">
                                        <td class="py-1.5">{{ $row['name'] }}</td>
                                        <td class="font-medium">{{ $row['amount'] }}</td>
                                        <td class="text-muted">{{ $row['dailyPercent'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-card>
                @endif
            </div>
        @endif
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-[radial-gradient(ellipse_at_bottom,_#0f6b4c22,_transparent_70%)]"></div>
    </div>
</body>
</html>
