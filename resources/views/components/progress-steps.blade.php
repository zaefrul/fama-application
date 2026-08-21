@props(['current' => 1, 'total' => 2])
<div class="mb-4 grid gap-1" style="grid-template-columns: repeat({{ $total }}, minmax(0, 1fr))">
    @for ($i = 0; $i < $total; $i++)
        <div class="h-1.5 rounded-full {{ $i < $current ? 'bg-brand' : 'bg-border' }}"></div>
    @endfor
</div>
