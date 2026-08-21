@props(['src', 'alt' => '', 'class' => 'h-28 w-full object-cover'])
@if (\App\Services\UploadService::isPdf($src))
    <a href="{{ $src }}" target="_blank" rel="noreferrer" class="flex h-28 items-center justify-center rounded-xl bg-surface-muted text-sm font-semibold text-brand">
        PDF · Lihat sijil
    </a>
@else
    <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $class }}">
@endif
