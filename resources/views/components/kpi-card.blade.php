@props(['value', 'label', 'tone' => 'neutral', 'href' => null])
@php
    $accents = [
        'success' => 'border-l-success text-success',
        'warning' => 'border-l-warning text-warning',
        'danger' => 'border-l-danger text-danger',
        'info' => 'border-l-info text-info',
        'neutral' => 'border-l-neutral text-ink',
    ];
    $accent = $accents[$tone] ?? $accents['neutral'];
@endphp
@if ($href)
    <a href="{{ $href }}" class="block min-w-0">
        <x-card class="border-l-4 {{ $accent }} bg-white px-3 py-3 sm:px-4 sm:py-4">
            <p class="text-2xl font-bold leading-none sm:text-3xl">{{ $value }}</p>
            <p class="mt-1.5 text-[11px] font-medium leading-tight text-muted sm:text-xs">{{ $label }}</p>
        </x-card>
    </a>
@else
    <x-card class="border-l-4 {{ $accent }} bg-white px-3 py-3 sm:px-4 sm:py-4">
        <p class="text-2xl font-bold leading-none sm:text-3xl">{{ $value }}</p>
        <p class="mt-1.5 text-[11px] font-medium leading-tight text-muted sm:text-xs">{{ $label }}</p>
    </x-card>
@endif
