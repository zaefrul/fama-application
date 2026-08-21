@props(['href', 'title', 'subtitle', 'status'])
<a href="{{ $href }}" class="flex min-w-0 items-center gap-2 rounded-2xl border border-[#c5d4ea] bg-[#eef5fb] px-2.5 py-2.5 sm:gap-3 sm:px-3 sm:py-3">
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand text-white sm:h-10 sm:w-10">+</span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold">{{ $title }}</p>
        <p class="truncate text-xs text-muted">{{ $subtitle }}</p>
    </div>
    <span class="shrink-0"><x-status-badge :application="$status" /></span>
    <span class="hidden text-muted sm:inline">›</span>
</a>
