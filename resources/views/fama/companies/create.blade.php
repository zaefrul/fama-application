<x-layouts.fama title="Daftar Vendor">
    <div class="space-y-4">
        <x-breadcrumb :items="['Senarai Syarikat', 'Daftar Vendor']" />
        <x-page-title title="Daftar Vendor" subtitle="Rekod syarikat diurus oleh FAMA. Tiada akaun pengeksport dicipta." />
        <x-card>
            <x-fama-company-form :action="url('/fama/companies')" :error="$error" submit-label="Simpan Vendor" />
        </x-card>
    </div>
</x-layouts.fama>
