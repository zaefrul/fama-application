@php
    $t = \App\Support\TraceCopy::shared($lang);
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
        <x-gov-masthead variant="public" />

        <header class="mx-auto w-full max-w-lg px-4 pt-5 lg:max-w-6xl">
            <div class="flex items-start justify-between gap-3 text-white">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <div class="shrink-0 rounded-2xl bg-white px-2 py-1.5">
                        <img
                            src="{{ asset('logos/logo-jata-negara.png') }}"
                            alt="{{ $t['agencyMalaysia'] }}"
                            width="44"
                            height="44"
                            class="trace-header-mark"
                            style="width:44px;height:44px;max-width:44px;max-height:44px;object-fit:contain"
                        >
                    </div>
                    <div class="shrink-0 rounded-2xl bg-white px-2 py-1.5">
                        <img
                            src="{{ asset('logos/logo-fama.png') }}"
                            alt="FAMA"
                            width="44"
                            height="44"
                            class="trace-header-mark"
                            style="width:44px;height:44px;max-width:44px;max-height:44px;object-fit:contain"
                        >
                    </div>
                    <div class="shrink-0 rounded-2xl bg-white px-2 py-1.5">
                        <img
                            src="{{ asset('logos/fama-jejak-gpl-logo-hd-1kpx.png') }}"
                            alt="Jejak GPL"
                            width="44"
                            height="44"
                            class="trace-header-mark"
                            style="width:44px;height:44px;max-width:44px;max-height:44px;object-fit:contain"
                        >
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
                        <h1 class="text-lg font-bold tracking-wide">{{ $t['pamphletTitle'] }}</h1>
                        <p class="mt-1 text-xs text-white/70">{{ $t['profileTitle'] }}</p>
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
                        <x-data-row :label="$t['company']" :value="$application?->company?->name" />
                        <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
                        @if ($application?->lot_no)
                            <x-data-row :label="$t['lot']" :value="$application->lot_no" />
                        @endif
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

                    @if ($certificates->isNotEmpty())
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['certs'] }}</h2>
                            @include('trace.partials.certificates', ['certCols' => 'grid-cols-2'])
                        </section>
                    @endif

                    @if (count($nutrition))
                        <section class="trace-pamphlet overflow-hidden rounded-sm">
                            <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['nutrition'] }}</h2>
                            @include('trace.partials.nutrition')
                        </section>
                    @endif

                    <p class="text-center text-[11px] text-white/60">{{ $t['scanBody'] }}</p>
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
                            </div>
                        </div>
                        <div class="col-span-8 flex min-w-0 flex-col">
                            <div class="bg-surface-dark px-6 py-4 text-white">
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
                                    <x-data-row :label="$t['company']" :value="$application?->company?->name" />
                                    <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
                                    @if ($application?->lot_no)
                                        <x-data-row :label="$t['lot']" :value="$application->lot_no" />
                                    @endif
                                    <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
                                </dl>
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
                        @if ($certificates->isNotEmpty())
                            <section class="trace-pamphlet overflow-hidden rounded-sm">
                                <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['certs'] }}</h2>
                                @include('trace.partials.certificates', ['certCols' => 'grid-cols-3'])
                            </section>
                        @endif
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
            </div>
            @endif
        </main>
        <x-gov-footer />
    </div>
</body>
</html>
