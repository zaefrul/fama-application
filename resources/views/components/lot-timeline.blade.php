@props([
    'nodes',
    'currentId' => null,
    'unit' => '',
    'nested' => false,
    'showLinks' => false,
    'childLinks' => false,
    'showBalance' => false,
    'currentLabel' => 'Kod ini',
    'soldLabel' => 'Dijual',
    'publicLabel' => 'Consumer',
    'showPath' => true,
    'pathLabel' => null,
])
@php
    if ($showPath) {
        if (filled($pathLabel)) {
            $paths = [$pathLabel];
        } elseif (collect($nodes)->count() === 1 && collect($nodes)->first()?->relationLoaded('children')) {
            $paths = collect($nodes)->first()->trackingPaths();
        } else {
            $paths = [collect($nodes)->map(fn ($node) => $node->trackingRole())->implode(' → ')];
        }
    }
@endphp
<ol {{ $attributes->merge(['class' => 'trace-checkpoints']) }}>
    @if ($showPath)
        @foreach ($paths as $path)
            <li class="mb-1 list-none text-sm font-semibold tracking-wide text-ink">{{ $path }}</li>
        @endforeach
    @endif
    @foreach ($nodes as $node)
        @php
            $current = (string) $node->id === (string) $currentId;
            $children = $nested && $node->relationLoaded('children') ? $node->children : collect();
        @endphp
        <li class="trace-checkpoint {{ $current ? 'is-current' : '' }} {{ $children->count() === 1 ? 'has-children' : '' }} {{ $children->count() > 1 ? 'has-fork' : '' }}">
            <span class="trace-checkpoint-mark" aria-hidden="true"></span>
            <div class="min-w-0">
                <p class="trace-checkpoint-kicker">
                    @if ($node->holder)
                        {{ $node->holder->partyLabel() }}
                    @elseif ($node->pendingHolder)
                        Menunggu penerimaan
                    @else
                        {{ $publicLabel }}
                    @endif
                    @if ($current)
                        · {{ $currentLabel }}
                    @endif
                </p>
                <p class="text-sm {{ $current ? 'font-semibold' : '' }} text-ink">
                    {{ $node->holder?->name ?? $node->pendingHolder?->name ?? $publicLabel }}
                </p>
                <p class="text-xs text-muted">
                    {{ $node->qr_code }}
                    · {{ number_format((int) $node->quantity) }} {{ $unit }}
                    @if ($showBalance)
                        · baki {{ number_format((int) $node->quantity_remaining) }}
                    @endif
                    @if ($node->disposition?->value === 'SOLD')
                        · {{ $soldLabel }}
                    @endif
                </p>
                @if ($showLinks && ! $current)
                    <div class="mt-1 flex gap-3 text-xs font-semibold">
                        <a class="text-brand" href="{{ route('exporter.qr.download', $node) }}">Muat turun</a>
                        <a class="text-brand" href="{{ url('/trace/'.$node->qr_code) }}">Jejak awam</a>
                    </div>
                @endif
            </div>
            @if ($children->count() > 1)
                <div class="trace-fork">
                    @foreach ($children as $child)
                        <div class="trace-fork-chain">
                            <x-lot-timeline
                                :nodes="collect([$child])"
                                :current-id="$currentId"
                                :unit="$unit"
                                :nested="true"
                                :show-balance="$showBalance"
                                :show-links="$childLinks"
                                :child-links="$childLinks"
                                :current-label="$currentLabel"
                                :sold-label="$soldLabel"
                                :public-label="$publicLabel"
                                :show-path="false"
                                class="is-inset"
                            />
                        </div>
                    @endforeach
                </div>
            @elseif ($children->isNotEmpty())
                <x-lot-timeline
                    :nodes="$children"
                    :current-id="$currentId"
                    :unit="$unit"
                    :nested="true"
                    :show-balance="$showBalance"
                    :show-links="$childLinks"
                    :child-links="$childLinks"
                    :current-label="$currentLabel"
                    :sold-label="$soldLabel"
                    :public-label="$publicLabel"
                    :show-path="false"
                    class="is-branch"
                />
            @endif
        </li>
    @endforeach
</ol>
