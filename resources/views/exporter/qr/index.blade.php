<x-layouts.exporter title="Senarai QR">
    <div class="space-y-4">
        <x-page-title title="Senarai QR" />
        <ul class="space-y-2">
            @foreach ($qrs as $qr)
                <li>
                    <a href="{{ route('exporter.qr.show', $qr) }}">
                        <x-card class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $qr->qr_code }}</p>
                                <p class="text-xs text-muted">{{ $qr->application?->produceType?->name ?? '—' }}</p>
                            </div>
                            <x-status-badge :qr="$qr->status" />
                        </x-card>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.exporter>
