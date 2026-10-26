<x-layouts.exporter title="Permohonan">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <x-page-title title="Permohonan" />
            <a href="{{ route('exporter.applications.create') }}" class="shrink-0 pt-0.5"><x-button type="button">+ Tambah</x-button></a>
        </div>
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
                                <x-application-card
                                    :href="route('exporter.applications.show', $application)"
                                    :title="$application->application_no.' · '.($application->produceType?->name).' '.$application->variety"
                                    :subtitle="'Dihantar pada '.($application->submitted_at?->locale('ms')->translatedFormat('d F Y') ?? '—')"
                                    :status="$application->status"
                                />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
</x-layouts.exporter>
