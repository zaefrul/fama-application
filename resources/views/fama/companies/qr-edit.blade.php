<x-layouts.fama title="Kemaskini QR">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <x-page-title title="Kemaskini QR" :subtitle="$company->name.' · '.($qr?->qr_code ?? $application->application_no)" />
            @if ($qr)<x-status-badge :qr="$qr->status" />@endif
        </div>
        @if ($qr)
            <x-qr-preview :value="$publicUrl" />
            <x-card class="space-y-3">
                <form action="{{ route('qr.download', $qr) }}" method="get" class="grid gap-3 sm:grid-cols-2">
                    <x-field label="Saiz QR">
                        <x-select name="size">
                            <option value="3">3 cm</option>
                            <option value="5" selected>5 cm</option>
                            <option value="8">Custom (8 cm)</option>
                        </x-select>
                    </x-field>
                    <x-field label="Format Muat Turun">
                        <x-select name="format">
                            <option value="png">PNG</option>
                            <option value="pdf">PDF</option>
                        </x-select>
                    </x-field>
                    <div class="sm:col-span-2">
                        <x-button type="submit" class="w-full">Muat Turun QR</x-button>
                    </div>
                </form>
            </x-card>
        @endif
        <x-error-text>{{ $error }}</x-error-text>
        @if ($saved)
            <p class="text-sm text-success">Maklumat awam disimpan. Identiti QR tidak berubah.</p>
        @endif
        <x-application-form
            :action="url('/fama/companies/'.$company->id.'/qr/'.$application->id)"
            :application="$application"
            :company-name="$companyName ?? $company->name"
            :produce-types="$produceTypes"
            :certificates="$certificates"
            :editable="true"
            :hide-secondary="true"
            primary-label="Simpan"
        />
    </div>
</x-layouts.fama>
