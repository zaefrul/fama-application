<x-layouts.exporter title="{{ $application->application_no }}">
    <div class="space-y-4">
        <x-breadcrumb :items="['Senarai QR', 'Maklumat Eksport', 'Ringkasan']" />
        <div class="flex items-start justify-between gap-3">
            <x-page-title :title="$application->application_no" :subtitle="$application->produceType ? $application->produceType->name.' '.$application->variety : ''" />
            <x-status-badge :application="$application->status" />
        </div>
        @if ($application->status->value === 'DRAFT' && $application->isLivestock())
            <x-livestock-form
                :action="url('/exporter/applications/'.$application->id)"
                :application="$application"
                :company-name="$companyName ?? $application->company?->name"
                :produce-types="$produceTypes"
            />
        @elseif ($application->status->value === 'DRAFT')
            <x-application-form
                :action="url('/exporter/applications/'.$application->id)"
                :application="$application"
                :company-name="$companyName ?? $application->company?->name"
                :produce-types="$produceTypes"
                :certificates="$certificates"
            />
        @else
            <x-card class="px-5">
                <x-application-facts :application="$application" />
            </x-card>
        @endif
        @if ($application->qrCode)
            <x-card class="space-y-4 px-5 py-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Kod QR</h2>
                    <x-status-badge :qr="$application->qrCode->status" />
                </div>
                <x-qr-preview :value="$publicUrl" />
                @if ($application->status->value === 'DRAFT')
                    <form action="{{ route('exporter.applications.submit', $application) }}" method="post">
                        @csrf
                        <x-button type="submit" class="w-full">Hantar</x-button>
                    </form>
                @endif
            </x-card>
        @else
            <form action="{{ url('/exporter/applications/'.$application->id.'/qr') }}" method="post">
                @csrf
                <x-button type="submit">Jana QR</x-button>
            </form>
        @endif
    </div>
</x-layouts.exporter>
