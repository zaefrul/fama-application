<x-layouts.exporter title="Permohonan Baharu">
    <x-page-title title="Permohonan Baharu" subtitle="QR tidak aktif akan dijana apabila draf disimpan." />
    <x-application-form
        :action="url('/exporter/applications')"
        :produce-types="$produceTypes"
        :certificates="$certificates"
    />
</x-layouts.exporter>
