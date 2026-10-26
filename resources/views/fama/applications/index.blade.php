<x-layouts.fama title="Kelulusan QR">
    <div class="space-y-4">
        <x-page-title title="Kelulusan QR" subtitle="Senarai permohonan untuk semakan FAMA" />
        @foreach (\App\Domain\Market::cases() as $market)
            @php
                $group = $applications->filter(fn ($application) => ($application->market ?? \App\Domain\Market::Export) === $market);
            @endphp
            <section class="space-y-2">
                <h2 class="text-sm font-bold text-brand">{{ $market->label() }}</h2>
                @if ($group->isEmpty())
                    <p class="text-sm text-muted">Tiada permohonan.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($group as $application)
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
                @endif
            </section>
        @endforeach
    </div>
</x-layouts.fama>
