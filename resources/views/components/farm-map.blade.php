@props([
    'lat' => null,
    'lng' => null,
    'interactive' => false,
    'latName' => 'farmLat',
    'lngName' => 'farmLng',
])

@php
    $hasPoint = $lat !== null && $lat !== '' && $lng !== null && $lng !== '';
    $mapLat = $hasPoint ? (float) $lat : null;
    $mapLng = $hasPoint ? (float) $lng : null;
    $delta = 0.04;
    $bbox = $hasPoint ? (($mapLng - $delta).','.($mapLat - $delta).','.($mapLng + $delta).','.($mapLat + $delta)) : '';
    $embed = $hasPoint
        ? 'https://www.openstreetmap.org/export/embed.html?bbox='.rawurlencode($bbox).'&layer=mapnik&marker='.rawurlencode($mapLat.','.$mapLng)
        : '';
    $mapId = 'farm-map-'.substr(md5($latName.$lngName.(string) $lat.(string) $lng), 0, 8);
@endphp

@if ($hasPoint)
<div {{ $attributes->class('space-y-2') }}>
    @if ($interactive)
        <div id="{{ $mapId }}" class="farm-map h-48 w-full overflow-hidden rounded-xl border border-border" data-interactive="1" data-lat="{{ $mapLat }}" data-lng="{{ $mapLng }}" data-lat-input="{{ $latName }}" data-lng-input="{{ $lngName }}"></div>
        <p class="text-xs text-muted">Klik peta untuk menanda lokasi ladang. Medan latitud dan longitud dikemaskini secara automatik.</p>
        @once
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('[data-interactive="1"]').forEach(function (el) {
                        if (el.dataset.bound === '1' || typeof L === 'undefined') return;
                        el.dataset.bound = '1';
                        var latInput = document.querySelector('[name="' + el.dataset.latInput + '"]');
                        var lngInput = document.querySelector('[name="' + el.dataset.lngInput + '"]');
                        var lat = parseFloat(el.dataset.lat);
                        var lng = parseFloat(el.dataset.lng);
                        var map = L.map(el).setView([lat, lng], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(map);
                        var marker = L.marker([lat, lng]).addTo(map);
                        map.on('click', function (e) {
                            marker.setLatLng(e.latlng);
                            if (latInput) latInput.value = e.latlng.lat.toFixed(7);
                            if (lngInput) lngInput.value = e.latlng.lng.toFixed(7);
                        });
                    });
                });
            </script>
        @endonce
    @else
        <iframe
            title="Lokasi ladang"
            class="farm-map h-48 w-full rounded-xl border border-border"
            src="{{ $embed }}"
        ></iframe>
        <p class="text-center text-[11px] text-muted">
            <a href="https://www.openstreetmap.org/?mlat={{ $mapLat }}&mlon={{ $mapLng }}#map=14/{{ $mapLat }}/{{ $mapLng }}" class="text-brand underline-offset-2 hover:underline" rel="noopener noreferrer" target="_blank">OpenStreetMap</a>
        </p>
    @endif
</div>
@endif
