<section class="trace-pamphlet overflow-hidden rounded-sm">
    <h2 class="bg-surface-dark px-4 py-2.5 text-sm font-bold tracking-wide text-white">{{ $t['chain'] }}</h2>
    <x-lot-timeline
        :nodes="$chain"
        :current-id="$qr->id"
        :unit="$application?->quantity_unit ?? ''"
        :current-label="$t['currentStop']"
        :sold-label="$t['sold']"
        :public-label="$t['soldToPublic']"
    />
</section>
