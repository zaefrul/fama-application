@php
    $canDecide = in_array($application->status->value, ['UNDER_REVIEW', 'SUBMITTED'], true);
@endphp
<x-layouts.fama title="Ringkasan">
    <div class="space-y-4">
        <x-breadcrumb :items="['Senarai QR', 'Maklumat Eksport', 'Ringkasan']" />
        <div class="flex items-start justify-between gap-3">
            <x-page-title title="Ringkasan" :subtitle="$application->application_no" />
            <x-status-badge :application="$application->status" />
        </div>
        @if ($application->qrCode)
            <x-qr-preview :value="$publicUrl" />
        @endif
        <x-card class="px-5">
            <dl>
                <x-data-row label="Tarikh eksport" :value="$application->export_date?->toDateString()" />
                <x-data-row label="Jenis keluaran" :value="$application->produceType?->name" />
                <x-data-row label="Gred" :value="$application->grade" />
                <x-data-row label="Saiz" :value="$application->size" />
                <x-data-row label="Pengeksport" :value="$application->company?->name" />
                <x-data-row label="Alamat pengeksport" :value="$application->company?->address" />
                <x-data-row label="Nama ladang" :value="$application->farm_name" />
                <x-data-row label="Pengimport" :value="$application->importer_name" />
                <x-data-row label="Alamat pengimport" :value="$application->importer_address" />
                <x-data-row label="No. Sijil CoC" :value="$application->coc_number" />
            </dl>
        </x-card>
        @if ($canDecide)
            <x-card class="space-y-4 px-5 py-5">
                <x-field label="Catatan" required>
                    <x-textarea name="remarks" form="reject-form" required placeholder="Wajib jika menolak" />
                </x-field>
                @if ($error === 'remarks')
                    <x-error-text>Catatan penolakan diperlukan.</x-error-text>
                @endif
                <div class="grid grid-cols-2 gap-2">
                    <form id="reject-form" action="{{ route('fama.applications.reject', $application) }}" method="post">
                        @csrf
                        <x-button type="submit" variant="danger" class="w-full">Tolak</x-button>
                    </form>
                    <form action="{{ route('fama.applications.approve', $application) }}" method="post">
                        @csrf
                        <input type="hidden" name="remarks" value="Diluluskan">
                        <x-button type="submit" class="w-full">Sahkan</x-button>
                    </form>
                </div>
            </x-card>
        @else
            <x-card>
                <h2 class="mb-2 font-semibold">Keputusan</h2>
                @foreach ($application->approvals as $approval)
                    <p class="text-sm">{{ $approval->decision === 'APPROVED' ? 'Diluluskan' : 'Ditolak' }} · {{ $approval->remarks }}</p>
                @endforeach
            </x-card>
        @endif
    </div>
</x-layouts.fama>
