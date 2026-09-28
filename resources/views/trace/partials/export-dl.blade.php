<dl class="px-4">
    @if ($application?->export_date)
        <x-data-row label="Tarikh Eksport" :value="$application->export_date->toDateString()" />
    @endif
    @if ($application?->isLivestock() && $application->slaughter_date)
        <x-data-row :label="$t['slaughterDate']" :value="$application->slaughter_date->toDateString()" />
    @endif
    <x-data-row :label="$t['exporterAddress']" :value="$application?->company?->address" />
    <x-data-row :label="$t['farm']" :value="$application?->farm_name" />
    @if ($application?->isLivestock())
        <x-data-row :label="$t['abattoir']" :value="$application?->abattoir_name" />
    @elseif ($application?->lot_no)
        <x-data-row :label="$t['lot']" :value="$application->lot_no" />
    @endif
    @if ($application?->farm_location)
        <x-data-row :label="$t['farmLocation']" :value="$application->farm_location" />
    @endif
</dl>
@if ($application?->hasFarmCoordinates())
    <div class="px-4 pb-4">
        <x-farm-map
            :lat="$application->farm_lat"
            :lng="$application->farm_lng"
            :map-title="$application->isLivestock() ? $t['farmLocation'] : 'Lokasi ladang'"
        />
    </div>
@endif
