@props(['notificationCount' => 0, 'menuHref' => null])
@php
    use App\Domain\Role;
    $user = auth()->user();
    $roleLabel = $user?->role === Role::FamaOfficer ? 'Pegawai FAMA' : 'Usahawan';
@endphp

<header class="sticky top-0 z-20 border-b border-border bg-white">
    <div class="flex items-center gap-2.5 px-3 py-2 sm:gap-3 sm:px-4 sm:py-2.5">
        @if ($menuHref)
            <a href="{{ $menuHref }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-border text-ink md:hidden" aria-label="Menu">
                <x-icon name="menu" class="h-5 w-5" />
            </a>
        @endif
        <x-brand-logo variant="header" />
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold leading-tight text-ink sm:text-base">Sistem Jejak GPL</p>
            <p class="truncate text-[11px] leading-tight text-muted sm:text-xs">Jejak Eksport Hasil Pertanian</p>
        </div>
        @if ($user)
            <div class="hidden min-w-0 text-right md:block">
                <p class="truncate text-xs font-semibold text-ink">{{ $user->name }}</p>
                <p class="text-[11px] text-muted">{{ $roleLabel }}</p>
            </div>
        @endif
        <span class="relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-border text-ink" aria-label="Notifikasi">
            <x-icon name="bell" class="h-5 w-5" />
            @if ($notificationCount > 0)
                <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-semibold text-white">{{ $notificationCount }}</span>
            @endif
        </span>
    </div>
</header>
