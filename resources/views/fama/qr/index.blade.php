<x-layouts.fama title="Pengurusan QR">
    <div class="space-y-4">
        <x-page-title title="Pengurusan QR" />
        <ul class="space-y-2">
            @foreach ($qrs as $qr)
                <li>
                    @php $linked = $qr->application ?? $qr->rootApplication; @endphp
                    <a href="{{ $linked ? route('fama.applications.show', $linked) : route('fama.qr') }}">
                        <x-card class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $qr->qr_code }}</p>
                                <p class="text-xs text-muted">{{ $qr->holder?->name ?? $linked?->company?->name }}</p>
                                <p class="mt-1 text-xs text-muted">{{ number_format((int) $qr->accesses_count) }} imbasan</p>
                            </div>
                            <x-status-badge :qr="$qr->status" />
                        </x-card>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.fama>
