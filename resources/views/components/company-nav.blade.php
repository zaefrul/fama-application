@php
    use App\Support\Nav;
    $path = '/'.ltrim(request()->path(), '/');
    $items = [
        ['href' => '/exporter/company', 'label' => 'Syarikat'],
        ['href' => '/exporter/company/produce', 'label' => 'Keluaran'],
        ['href' => '/exporter/company/certificates', 'label' => 'Sijil'],
        ['href' => '/exporter/company/gallery', 'label' => 'Galeri'],
    ];
@endphp
<nav class="mb-4">
    <ul class="grid grid-cols-4 gap-1 rounded-2xl bg-surface-muted p-1">
        @foreach ($items as $item)
            <li class="min-w-0">
                <a href="{{ $item['href'] }}" class="block truncate rounded-xl px-1 py-2 text-center text-[11px] sm:px-3 sm:text-sm {{ Nav::active($path, $item['href'], $items) ? 'bg-white font-semibold text-brand shadow-sm' : 'text-muted' }}">
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
