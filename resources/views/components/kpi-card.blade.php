@props(['value', 'label', 'tone' => 'neutral', 'href' => null])
@php
    $tones = [
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/15 text-warning',
        'danger' => 'bg-danger/10 text-danger',
        'info' => 'bg-info/10 text-info',
        'neutral' => 'bg-surface-muted text-ink',
    ];
@endphp
@php $content = '<x-card class="'.$tones[$tone].' px-2 py-3 text-center sm:p-4"><p class="text-2xl font-bold leading-none sm:text-3xl">'.e($value).'</p><p class="mt-1 text-[11px] font-medium leading-tight opacity-80 sm:text-xs">'.e($label).'</p></x-card>'; @endphp
@if ($href)
    <a href="{{ $href }}">
        <x-card class="{{ $tones[$tone] }} px-2 py-3 text-center sm:p-4">
            <p class="text-2xl font-bold leading-none sm:text-3xl">{{ $value }}</p>
            <p class="mt-1 text-[11px] font-medium leading-tight opacity-80 sm:text-xs">{{ $label }}</p>
        </x-card>
    </a>
@else
    <x-card class="{{ $tones[$tone] }} px-2 py-3 text-center sm:p-4">
        <p class="text-2xl font-bold leading-none sm:text-3xl">{{ $value }}</p>
        <p class="mt-1 text-[11px] font-medium leading-tight opacity-80 sm:text-xs">{{ $label }}</p>
    </x-card>
@endif
