<x-layouts.exporter title="Senarai QR">
    <div class="space-y-6">
        <x-page-title title="Senarai QR" />

        <section class="space-y-2">
            <h2 class="text-sm font-semibold">QR permohonan sendiri</h2>
            @if ($ownQrs->isEmpty())
                <x-card><p class="text-sm text-muted">Tiada QR daripada permohonan syarikat ini.</p></x-card>
            @else
                <ul class="space-y-2">
                    @foreach ($ownQrs as $qr)
                        <li>
                            <a href="{{ route('exporter.qr.show', $qr) }}">
                                <x-card class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold">{{ $qr->qr_code }}</p>
                                        <p class="text-xs text-muted">{{ $qr->application?->produceType?->name ?? '—' }}</p>
                                    </div>
                                    <x-status-badge :qr="$qr->status" />
                                </x-card>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-semibold">QR jualan</h2>
            @if ($receivedQrs->isEmpty())
                <x-card><p class="text-sm text-muted">Tiada QR yang dipecahkan kepada syarikat ini.</p></x-card>
            @else
                <ul class="space-y-2">
                    @foreach ($receivedQrs as $qr)
                        <li>
                            <x-card class="flex items-center justify-between gap-3">
                                <a href="{{ route('exporter.qr.show', $qr) }}" class="min-w-0 flex-1">
                                    <p class="font-semibold">{{ $qr->qr_code }}</p>
                                    <p class="text-xs text-muted">{{ $qr->rootApplication?->produceType?->name ?? $qr->application?->produceType?->name ?? '—' }}</p>
                                    @if ($qr->holder_company_id === null && $qr->pending_holder_company_id)
                                        <p class="text-xs text-muted">Menunggu penerimaan</p>
                                    @endif
                                </a>
                                <div class="flex shrink-0 items-center gap-2">
                                    <x-status-badge :qr="$qr->status" />
                                    @if ($qr->holder_company_id === null && $qr->pending_holder_company_id)
                                        <form method="post" action="{{ route('exporter.lots.accept', $qr) }}">
                                            @csrf
                                            <x-button type="submit">Diterima</x-button>
                                        </form>
                                    @endif
                                </div>
                            </x-card>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.exporter>
