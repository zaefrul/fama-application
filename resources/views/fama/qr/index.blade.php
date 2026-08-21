<x-layouts.fama title="Pengurusan QR">
    <div class="space-y-4">
        <x-page-title title="Pengurusan QR" />
        <ul class="space-y-2">
            @foreach ($qrs as $qr)
                <li>
                    <a href="{{ $qr->application ? route('fama.applications.show', $qr->application) : route('fama.qr') }}">
                        <x-card class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $qr->qr_code }}</p>
                                <p class="text-xs text-muted">{{ $qr->application?->company?->name }}</p>
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
