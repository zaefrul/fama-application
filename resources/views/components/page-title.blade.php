@props(['title', 'subtitle' => null])
<header class="mb-3 min-w-0 sm:mb-4">
    <h1 class="text-lg font-bold leading-tight text-ink sm:text-xl">{{ $title }}</h1>
    @if ($subtitle)<p class="mt-1 text-sm leading-snug text-muted">{{ $subtitle }}</p>@endif
</header>
