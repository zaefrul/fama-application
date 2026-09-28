<dl class="px-4">
    <x-data-row :label="$t['company']" :value="$application?->company?->name" />
    <x-data-row :label="$t['fruitType']" :value="$application?->produceType?->name" />
    @if ($application?->isLivestock())
        <x-data-row :label="$t['breed']" :value="$application?->variety" />
        <x-data-row :label="$t['headCount']" :value="$application?->head_count ? $application->head_count.' '.$t['headUnit'] : null" />
        <x-data-row :label="$t['weight']" :value="$application?->quantity.' '.$application?->quantity_unit" />
        <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
        <x-data-row :label="$t['vetCertificate']" :value="$application?->vet_certificate_no" />
    @else
        <x-data-row label="Gred" :value="$application?->grade" />
        <x-data-row label="Saiz" :value="$application?->size" />
        <x-data-row label="Berat" :value="$application?->quantity.' '.$application?->quantity_unit" />
        <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
        <x-data-row label="No. Sijil CoC" :value="$application?->coc_number" />
    @endif
</dl>
