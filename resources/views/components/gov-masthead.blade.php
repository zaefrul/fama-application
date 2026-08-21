@props(['variant' => 'app'])

<div {{ $attributes }}>
    <div class="flex h-1 w-full" role="presentation" aria-hidden="true">
        <span class="flex-1 bg-flag-red"></span>
        <span class="flex-1 bg-white"></span>
        <span class="flex-1 bg-flag-blue"></span>
        <span class="flex-1 bg-flag-yellow"></span>
    </div>
    <div class="bg-surface-dark text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-3 py-1.5 sm:px-4">
            <p class="text-[11px] font-semibold tracking-[0.12em] sm:text-xs">LAMAN RASMI FAMA</p>
            <p class="truncate text-[10px] text-white/70 sm:text-[11px]">
                <span class="sm:hidden">KPKM</span>
                <span class="hidden sm:inline">Kementerian Pertanian dan Keterjaminan Makanan</span>
            </p>
        </div>
    </div>
    @if ($variant === 'app')
        <div class="h-0.5 bg-warning/80" aria-hidden="true"></div>
    @endif
</div>
