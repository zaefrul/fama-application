<x-layouts.exporter title="Pecahan lot">
    <div class="space-y-4">
        <x-page-title title="Pecahan lot" subtitle="Lot yang dipegang syarikat aktif" />
        @if (session('status'))
            <x-card><p class="text-sm font-semibold text-brand">{{ session('status') }}</p></x-card>
        @endif
        @if (session('error'))
            <x-error-text>{{ session('error') }}</x-error-text>
        @endif

        <section class="space-y-2">
            <h2 class="text-sm font-semibold">Menunggu penerimaan</h2>
            <p class="text-xs text-muted">Pecahan yang dihantar kepada syarikat ini. Tekan Diterima untuk menerima lot dan mengaktifkan QR.</p>
            @if ($incoming->isEmpty())
                <x-card>
                    <p class="text-sm text-muted">Tiada pecahan menunggu penerimaan.</p>
                </x-card>
            @else
                <ul class="space-y-2">
                    @foreach ($incoming as $lot)
                        <li>
                            <x-card class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold">{{ $lot->qr_code }}</p>
                                    <p class="text-xs text-muted">{{ $lot->rootApplication?->produceType?->name ?? '—' }}</p>
                                    <p class="mt-1 text-xs text-muted">
                                        {{ number_format((int) $lot->quantity) }} {{ $lot->rootApplication?->quantity_unit }}
                                    </p>
                                    @if ($lot->parent?->holder)
                                        <p class="text-xs text-muted">Dari {{ $lot->parent->holder->partyLabel() }} · {{ $lot->parent->holder->name }}</p>
                                    @endif
                                </div>
                                <form method="post" action="{{ route('exporter.lots.accept', $lot) }}" class="shrink-0">
                                    @csrf
                                    <x-button type="submit">Diterima</x-button>
                                </form>
                            </x-card>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($lots->isEmpty())
            <x-card>
                <p class="text-sm text-muted">Tiada lot dipegang oleh syarikat ini.</p>
            </x-card>
        @else
            <ul class="space-y-2">
                @foreach ($lots as $lot)
                    <li>
                        <a href="{{ route('exporter.lots.show', $lot) }}">
                            <x-card class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold">{{ $lot->qr_code }}</p>
                                    <p class="text-xs text-muted">{{ $lot->rootApplication?->produceType?->name ?? '—' }}</p>
                                    <p class="mt-1 text-xs text-muted">
                                        Baki {{ number_format((int) $lot->quantity_remaining) }} / {{ number_format((int) $lot->quantity) }}
                                        {{ $lot->rootApplication?->quantity_unit }}
                                    </p>
                                    @if ($lot->parent?->holder)
                                        <p class="text-xs text-muted">Dari {{ $lot->parent->holder->partyLabel() }} · {{ $lot->parent->holder->name }}</p>
                                    @else
                                        <p class="text-xs text-muted">Lot asal</p>
                                    @endif
                                </div>
                                <div class="shrink-0 text-right">
                                    <x-status-badge :qr="$lot->status" />
                                    <p class="mt-1 text-xs font-semibold text-muted">{{ $lot->disposition?->label() }}</p>
                                </div>
                            </x-card>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.exporter>
