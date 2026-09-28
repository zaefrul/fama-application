<x-layouts.fama title="Cipta QR">
    <div class="space-y-4">
        @if (! $kind)
            <x-page-title title="Cipta QR" :subtitle="$company->name.' · Pilih jenis QR dahulu.'" />
            <x-error-text>{{ $error }}</x-error-text>
            <x-product-kind-choice
                :produce-url="route('fama.companies.qr.create', ['id' => $company->id, 'kind' => 'produce'])"
                :livestock-url="route('fama.companies.qr.create', ['id' => $company->id, 'kind' => 'livestock'])"
            />
        @elseif ($kind->value === 'LIVESTOCK')
            <x-page-title title="Cipta QR Ternakan" :subtitle="$company->name.' · QR akan diaktifkan serta-merta.'" />
            <x-error-text>{{ $error }}</x-error-text>
            <x-livestock-form
                :action="url('/fama/companies/'.$company->id.'/qr')"
                :company-name="$companyName ?? $company->name"
                :produce-types="$produceTypes"
                :editable="true"
                :hide-secondary="true"
                primary-label="Cipta dan Aktifkan QR"
                :choice-url="route('fama.companies.qr.create', ['id' => $company->id])"
            />
        @else
            <x-page-title title="Cipta QR" :subtitle="$company->name.' · QR akan diaktifkan serta-merta.'" />
            <x-error-text>{{ $error }}</x-error-text>
            <x-application-form
                :action="url('/fama/companies/'.$company->id.'/qr')"
                :company-name="$companyName ?? $company->name"
                :produce-types="$produceTypes"
                :certificates="$certificates"
                :editable="true"
                :hide-secondary="true"
                primary-label="Cipta dan Aktifkan QR"
                :choice-url="route('fama.companies.qr.create', ['id' => $company->id])"
            />
        @endif
    </div>
</x-layouts.fama>
