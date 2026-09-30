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
    'publicLabel' => 'Pengguna',
])
<ol {{ $attributes->merge(['class' => 'trace-checkpoints']) }}>
    @foreach ($nodes as $node)
        @php
            $current = (string) $node->id === (string) $currentId;
            $children = $nested && $node->relationLoaded('children') ? $node->children : collect();
        @endphp
        <li class="trace-checkpoint {{ $current ? 'is-current' : '' }} {{ $children->isNotEmpty() ? 'has-children' : '' }}">
            <span class="trace-checkpoint-mark" aria-hidden="true"></span>
            <div class="min-w-0">
                <p class="trace-checkpoint-kicker">
                    {{ $node->holder?->partyLabel() ?? $publicLabel }}
                    @if ($current)
                        · {{ $currentLabel }}
                    @endif
                </p>
                <p class="text-sm {{ $current ? 'font-semibold' : '' }} text-ink">
                    {{ $node->holder?->name ?? $publicLabel }}
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
            @if ($children->isNotEmpty())
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
                    class="is-branch"
                />
            @endif
        </li>
    @endforeach
</ol>
