@php
    use App\Support\Nav;
    $path = '/'.ltrim(request()->path(), '/');
    $items = [
        ['href' => '/exporter', 'label' => 'Utama', 'icon' => '⌂'],
        ['href' => '/exporter/applications', 'label' => 'Permohonan', 'icon' => '☰'],
        ['href' => '/exporter/qr', 'label' => 'Kod QR', 'icon' => '▣'],
        ['href' => '/exporter/company/certificates', 'label' => 'Sijil', 'icon' => '▤'],
        ['href' => '/exporter/company', 'label' => 'Profil', 'icon' => '☺'],
    ];
@endphp
<x-layouts.app :title="$title ?? 'Sistem Jejak GPL'">
    <div class="min-h-dvh md:flex">
        <aside class="hidden w-64 shrink-0 bg-surface-dark p-4 text-white md:flex md:flex-col">
            <div class="mb-6 rounded-2xl bg-white px-3 py-2">
                <x-brand-logo variant="sidebar" />
            </div>
            <p class="mb-3 text-xs uppercase tracking-wide text-white/60">Menu Utama</p>
            <ul class="space-y-1">
                @foreach ($items as $item)
                    <li>
                        <a href="{{ $item['href'] }}" class="block rounded-xl px-3 py-2 text-sm {{ Nav::active($path, $item['href'], $items) ? 'bg-white/15 font-semibold' : 'text-white/80 hover:bg-white/10' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-auto">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="w-full rounded-xl px-3 py-2 text-left text-sm text-white/80 hover:bg-white/10">Log Keluar</button>
                </form>
            </div>
        </aside>
        <div class="flex min-h-dvh min-w-0 flex-1 flex-col">
            <x-app-header :notification-count="$notificationCount ?? 0" />
            <main class="mx-auto w-full min-w-0 max-w-5xl flex-1 px-3 pb-24 pt-3 sm:px-4 sm:pt-4 md:pb-8">
                {{ $slot }}
            </main>
            <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-border bg-white px-1 py-1.5 md:hidden">
                <ul class="grid grid-cols-5 text-center text-[10px] leading-tight">
                    @foreach ($items as $item)
                        <li class="min-w-0">
                            <a href="{{ $item['href'] }}" class="flex flex-col items-center gap-0.5 px-0.5 py-1 {{ Nav::active($path, $item['href'], $items) ? 'font-bold text-brand' : 'text-muted' }}">
                                <span class="text-base">{{ $item['icon'] }}</span>
                                <span class="max-w-full truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
</x-layouts.app>
