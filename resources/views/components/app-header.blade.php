@props(['notificationCount' => 0, 'menuHref' => null])

<header class="sticky top-0 z-20 flex items-center gap-2 border-b border-border bg-white px-3 py-2 sm:px-4 sm:py-2.5">
    @if ($menuHref)
        <a href="{{ $menuHref }}" class="shrink-0 rounded-lg border border-border px-2 py-1 text-lg" aria-label="Menu">☰</a>
    @else
        <span class="w-8 shrink-0"></span>
    @endif
    <div class="flex min-w-0 flex-1 justify-center">
        <div class="flex min-w-0 max-w-full flex-col items-center leading-tight">
            <img src="{{ asset('logos/jejak-gpl.png') }}" alt="Sistem Jejak GPL" class="h-8 w-auto max-w-[42vw] object-contain sm:h-11">
            <p class="mt-0.5 hidden max-w-[220px] text-center text-[10px] leading-tight text-muted sm:block">Lembaga Pemasaran Pertanian Persekutuan</p>
        </div>
    </div>
    <span class="relative w-8 shrink-0 rounded-lg border border-border px-2 py-1 text-center text-sm" aria-label="Notifikasi">
        🔔
        @if ($notificationCount > 0)
            <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] text-white">{{ $notificationCount }}</span>
        @endif
    </span>
</header>
