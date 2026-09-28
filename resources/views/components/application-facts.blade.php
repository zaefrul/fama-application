@props(['application'])
<dl>
    <x-data-row label="Nama Syarikat" :value="$application->company?->name" />
    <x-data-row label="Alamat" :value="$application->company?->address" />
    @if ($application->isLivestock())
        <x-data-row label="Jenis ternakan" :value="$application->produceType?->name" />
        <x-data-row label="Baka" :value="$application->variety" />
        <x-data-row label="Bilangan" :value="$application->head_count ? $application->head_count.' ekor' : null" />
        <x-data-row label="Berat" :value="$application->quantity.' '.$application->quantity_unit" />
        <x-data-row label="Destinasi" :value="$application->destination_country" />
        @if ($application->export_date)
            <x-data-row label="Tarikh eksport" :value="$application->export_date->toDateString()" />
        @endif
        @if ($application->slaughter_date)
            <x-data-row label="Tarikh sembelih" :value="$application->slaughter_date->toDateString()" />
        @endif
        <x-data-row label="Nama premis" :value="$application->farm_name" />
        <x-data-row label="Rumah sembelih" :value="$application->abattoir_name" />
        <x-data-row label="Lokasi premis" :value="$application->farm_location" />
        <x-data-row label="No. sijil veterinar" :value="$application->vet_certificate_no" />
    @else
        <x-data-row label="Jenis keluaran" :value="$application->produceType?->name" />
        <x-data-row label="Varieti" :value="$application->variety" />
        <x-data-row label="Gred" :value="$application->grade" />
        <x-data-row label="Saiz" :value="$application->size" />
        <x-data-row label="Kuantiti" :value="$application->quantity.' '.$application->quantity_unit" />
        <x-data-row label="Destinasi" :value="$application->destination_country" />
        @if ($application->export_date)
            <x-data-row label="Tarikh eksport" :value="$application->export_date->toDateString()" />
        @endif
        <x-data-row label="Ladang" :value="$application->farm_name" />
        <x-data-row label="No. Lot" :value="$application->lot_no" />
        <x-data-row label="Lokasi ladang" :value="$application->farm_location" />
        <x-data-row label="No. Sijil CoC" :value="$application->coc_number" />
    @endif
    <x-data-row label="Pengimport" :value="$application->importer_name" />
    <x-data-row label="Alamat pengimport" :value="$application->importer_address" />
</dl>
