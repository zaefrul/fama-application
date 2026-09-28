<x-layouts.exporter title="Permohonan Baharu">
    @if (! $kind)
        <x-page-title title="Permohonan Baharu" subtitle="Pilih jenis QR sebelum mengisi maklumat." />
        <x-product-kind-choice
            :produce-url="route('exporter.applications.create', ['kind' => 'produce'])"
            :livestock-url="route('exporter.applications.create', ['kind' => 'livestock'])"
        />
    @elseif ($kind->value === 'LIVESTOCK')
        <x-page-title title="Permohonan Ternakan" subtitle="QR tidak aktif akan dijana apabila draf disimpan." />
        <x-livestock-form
            :action="url('/exporter/applications')"
            :company-name="$companyName"
            :produce-types="$produceTypes"
            :choice-url="route('exporter.applications.create')"
        />
    @else
        <x-page-title title="Permohonan Baharu" subtitle="QR tidak aktif akan dijana apabila draf disimpan." />
        <x-application-form
            :action="url('/exporter/applications')"
            :company-name="$companyName"
            :produce-types="$produceTypes"
            :certificates="$certificates"
            :choice-url="route('exporter.applications.create')"
        />
    @endif
</x-layouts.exporter>
