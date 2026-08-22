<dl class="px-4">
    <x-data-row label="Tarikh Eksport" :value="$application?->export_date?->toDateString()" />
    <x-data-row label="Alamat Pengeksport" :value="$application?->company?->address" />
    <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
    <x-data-row label="Pengimport" :value="$application?->importer_name" />
    <x-data-row label="Alamat Pengimport" :value="$application?->importer_address" />
</dl>
