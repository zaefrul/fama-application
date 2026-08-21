@php
    use App\Support\Nav;
    $path = '/'.ltrim(request()->path(), '/');
    $items = [
        ['href' => '/fama', 'label' => 'Utama', 'icon' => '⌂'],
        ['href' => '/fama/qr', 'label' => 'Pengurusan QR', 'icon' => '▣'],
        ['href' => '/fama/applications', 'label' => 'Kelulusan QR', 'icon' => '✓'],
        ['href' => '/fama/companies', 'label' => 'Maklumat Syarikat', 'icon' => '⌂'],
    ];
@endphp
<x-layouts.app :title="$title ?? 'Sistem Jejak GPL'">
    <div class="min-h-dvh md:flex">
        <aside class="hidden w-64 shrink-0 bg-surface-dark p-4 text-white md:flex md:flex-col">
            <div class="mb-6 rounded-2xl bg-white px-3 py-2">
                <img src="{{ asset('logos/jejak-gpl.png') }}" alt="Sistem Jejak GPL" class="mx-auto h-12 w-auto object-contain">
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
            <x-app-header :notification-count="$notificationCount ?? 0" menu-href="/fama/menu" />
            <main class="mx-auto w-full min-w-0 max-w-6xl flex-1 px-3 pb-8 pt-3 sm:px-4 sm:pt-4">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.app>
