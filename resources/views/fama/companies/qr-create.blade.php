<x-layouts.fama title="Cipta QR">
    <div class="space-y-4">
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
        />
    </div>
</x-layouts.fama>
