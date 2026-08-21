<x-layouts.fama title="Kelulusan QR">
    <div class="space-y-4">
        <x-page-title title="Kelulusan QR" subtitle="Senarai permohonan untuk semakan FAMA" />
        <ul class="space-y-2">
            @foreach ($applications as $application)
                <li>
                    <a href="{{ route('fama.applications.show', $application) }}">
                        <x-card class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $application->application_no }} · {{ $application->produceType?->name }}</p>
                                <p class="text-xs text-muted">{{ $application->company?->name }}</p>
                            </div>
                            <x-status-badge :application="$application->status" />
                        </x-card>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.fama>
