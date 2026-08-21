@props(['variant' => 'header', 'src' => null, 'alt' => null])
@php
    $presets = [
        'header' => [
            'src' => asset('logos/logo-fama.png'),
            'alt' => 'FAMA',
            'width' => 36,
            'height' => 36,
            'class' => 'brand-logo-header',
            'style' => 'display:block;width:36px;height:36px;max-width:36px;max-height:36px;object-fit:contain',
        ],
        'sidebar' => [
            'src' => asset('logos/jejak-gpl.png'),
            'alt' => 'Sistem Jejak GPL',
            'width' => 180,
            'height' => 48,
            'class' => 'brand-logo-sidebar',
            'style' => 'display:block;width:auto;height:48px;max-width:180px;max-height:48px;margin:0 auto;object-fit:contain',
        ],
        'auth' => [
            'src' => asset('logos/jejak-gpl.png'),
            'alt' => 'Sistem Jejak GPL',
            'width' => 280,
            'height' => 96,
            'class' => 'brand-logo-auth',
            'style' => 'display:block;width:auto;height:96px;max-width:min(280px,70vw);max-height:96px;margin:0 auto;object-fit:contain',
        ],
    ];
    $preset = $presets[$variant] ?? $presets['header'];
@endphp
<img
    src="{{ $src ?? $preset['src'] }}"
    alt="{{ $alt ?? $preset['alt'] }}"
    width="{{ $preset['width'] }}"
    height="{{ $preset['height'] }}"
    class="{{ $preset['class'] }}"
    style="{{ $preset['style'] }}"
>
