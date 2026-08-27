<dl class="px-4">
    <x-data-row :label="$t['company']" :value="$application?->company?->name" />
    <x-data-row :label="$t['fruitType']" :value="$application?->produceType?->name" />
    <x-data-row label="Gred" :value="$application?->grade" />
    <x-data-row label="Saiz" :value="$application?->size" />
    <x-data-row label="Berat" :value="$application?->quantity.' '.$application?->quantity_unit" />
    <x-data-row :label="$t['destination']" :value="$application?->destination_country" />
    <x-data-row label="No. Sijil CoC" :value="$application?->coc_number" />
</dl>
