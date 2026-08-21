@props(['items' => []])
<p class="mb-2 text-xs text-muted">
    @foreach ($items as $index => $item)
        @if ($index > 0)<span class="mx-1">›</span>@endif
        <span @class(['font-semibold text-ink' => $index === count($items) - 1])>{{ $item }}</span>
    @endforeach
</p>
