@props([
    'lat' => null,
    'lng' => null,
    'interactive' => false,
    'latName' => 'farmLat',
    'lngName' => 'farmLng',
    'locationName' => 'farmLocation',
    'mapTitle' => 'Lokasi ladang',
    'markerHint' => 'Cari alamat atau klik peta. Latitud dan longitud diisi secara automatik.',
])

@php
    $hasPoint = $lat !== null && $lat !== '' && $lng !== null && $lng !== '';
    $mapLat = $hasPoint ? (float) $lat : null;
    $mapLng = $hasPoint ? (float) $lng : null;
    $delta = 0.04;
    $embed = $hasPoint
        ? 'https://www.openstreetmap.org/export/embed.html?bbox='.rawurlencode(($mapLng - $delta).','.($mapLat - $delta).','.($mapLng + $delta).','.($mapLat + $delta)).'&layer=mapnik&marker='.rawurlencode($mapLat.','.$mapLng)
        : '';
@endphp

@if ($interactive)
<div
    {{ $attributes->class('space-y-2') }}
    data-farm-picker
    data-lat="{{ $mapLat }}"
    data-lng="{{ $mapLng }}"
    data-lat-input="{{ $latName }}"
    data-lng-input="{{ $lngName }}"
    data-location-input="{{ $locationName }}"
>
    <div class="farm-map h-64 w-full overflow-hidden rounded-xl border border-border" data-farm-canvas></div>
    <p class="text-xs text-muted">{{ $markerHint }}</p>
</div>
@once
    <style>
        .farm-search{position:absolute;z-index:40;left:0;right:0;top:calc(100% + 4px);max-height:12rem;overflow:auto;border:1px solid var(--border);border-radius:0.75rem;background:#fff;box-shadow:0 8px 20px rgba(20,36,29,.08)}
        .farm-search button{display:block;width:100%;padding:.5rem .75rem;text-align:left;font-size:.75rem;line-height:1.35}
        .farm-search button:hover{background:var(--surface-muted)}
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof L === 'undefined') return;
            document.querySelectorAll('[data-farm-picker]').forEach(function (root) {
                if (root.dataset.bound === '1') return;
                root.dataset.bound = '1';
                var canvas = root.querySelector('[data-farm-canvas]');
                var form = root.closest('form');
                if (!canvas || !form) return;
                var latInput = form.querySelector('[name="' + root.dataset.latInput + '"]');
                var lngInput = form.querySelector('[name="' + root.dataset.lngInput + '"]');
                var locationInput = form.querySelector('[name="' + root.dataset.locationInput + '"]');
                var lat = parseFloat(root.dataset.lat);
                var lng = parseFloat(root.dataset.lng);
                var hasPoint = !Number.isNaN(lat) && !Number.isNaN(lng);
                var map = L.map(canvas).setView(hasPoint ? [lat, lng] : [4.2105, 101.9758], hasPoint ? 14 : 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                var marker = null;
                var searchList = null;
                var searchTimer = null;
                var searchSeq = 0;

                function writeCoords(nextLat, nextLng) {
                    if (latInput) latInput.value = Number(nextLat).toFixed(7);
                    if (lngInput) lngInput.value = Number(nextLng).toFixed(7);
                }

                function place(nextLat, nextLng, address) {
                    var point = L.latLng(nextLat, nextLng);
                    if (!marker) {
                        marker = L.marker(point, { draggable: true }).addTo(map);
                        marker.on('dragend', function () {
                            var dropped = marker.getLatLng();
                            writeCoords(dropped.lat, dropped.lng);
                            reverse(dropped.lat, dropped.lng);
                        });
                    } else {
                        marker.setLatLng(point);
                    }
                    map.setView(point, Math.max(map.getZoom(), 14));
                    writeCoords(nextLat, nextLng);
                    if (address && locationInput) locationInput.value = address;
                }

                function reverse(nextLat, nextLng) {
                    fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(nextLat) + '&lon=' + encodeURIComponent(nextLng))
                        .then(function (response) { return response.ok ? response.json() : null; })
                        .then(function (data) {
                            if (data && data.display_name && locationInput) locationInput.value = data.display_name;
                        })
                        .catch(function () {});
                }

                function hideSearch() {
                    if (searchList) searchList.hidden = true;
                }

                if (hasPoint) place(lat, lng, null);

                map.on('click', function (event) {
                    place(event.latlng.lat, event.latlng.lng, null);
                    reverse(event.latlng.lat, event.latlng.lng);
                    hideSearch();
                });

                if (locationInput) {
                    var field = locationInput.parentElement;
                    if (field) field.style.position = 'relative';
                    searchList = document.createElement('div');
                    searchList.className = 'farm-search';
                    searchList.hidden = true;
                    locationInput.insertAdjacentElement('afterend', searchList);
                    locationInput.addEventListener('input', function () {
                        window.clearTimeout(searchTimer);
                        var query = locationInput.value.trim();
                        if (query.length < 3) {
                            hideSearch();
                            return;
                        }
                        searchTimer = window.setTimeout(function () {
                            var seq = ++searchSeq;
                            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=my&q=' + encodeURIComponent(query))
                                .then(function (response) { return response.ok ? response.json() : []; })
                                .then(function (rows) {
                                    if (seq !== searchSeq) return;
                                    searchList.replaceChildren();
                                    (rows || []).forEach(function (row) {
                                        var button = document.createElement('button');
                                        button.type = 'button';
                                        button.textContent = row.display_name;
                                        button.addEventListener('click', function () {
                                            place(parseFloat(row.lat), parseFloat(row.lon), row.display_name);
                                            hideSearch();
                                        });
                                        searchList.appendChild(button);
                                    });
                                    searchList.hidden = searchList.childElementCount === 0;
                                })
                                .catch(function () { hideSearch(); });
                        }, 400);
                    });
                    locationInput.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') event.preventDefault();
                    });
                    document.addEventListener('click', function (event) {
                        if (!field.contains(event.target)) hideSearch();
                    });
                }
            });
        });
    </script>
@endonce
@elseif ($hasPoint)
<div {{ $attributes->class('space-y-2') }}>
    <iframe
        title="{{ $mapTitle }}"
        class="farm-map h-48 w-full rounded-xl border border-border"
        src="{{ $embed }}"
    ></iframe>
    <p class="text-center text-[11px] text-muted">
        <a href="https://www.openstreetmap.org/?mlat={{ $mapLat }}&mlon={{ $mapLng }}#map=14/{{ $mapLat }}/{{ $mapLng }}" class="text-brand underline-offset-2 hover:underline" rel="noopener noreferrer" target="_blank">OpenStreetMap</a>
    </p>
</div>
@endif
