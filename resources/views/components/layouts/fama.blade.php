@php
    use App\Support\Nav;
    $path = '/'.ltrim(request()->path(), '/');
    $items = [
        ['href' => '/fama', 'label' => 'Utama', 'icon' => 'home'],
        ['href' => '/fama/qr', 'label' => 'Pengurusan QR', 'icon' => 'qr'],
        ['href' => '/fama/applications', 'label' => 'Kelulusan QR', 'icon' => 'check'],
        ['href' => '/fama/companies', 'label' => 'Maklumat Syarikat', 'icon' => 'building'],
    ];
@endphp
<x-layouts.app :title="$title ?? 'Sistem Jejak GPL'">
    <div class="flex min-h-dvh flex-col">
        <x-gov-masthead />
        <div class="flex min-h-0 flex-1">
            <aside class="hidden w-64 shrink-0 bg-surface-dark p-4 text-white md:flex md:flex-col">
                <div class="mb-6 rounded-2xl bg-white px-3 py-3">
                    <x-brand-logo variant="sidebar" />
                </div>
                <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/55">Menu Utama</p>
                <ul class="space-y-1">
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm {{ Nav::active($path, $item['href'], $items) ? 'bg-white/15 font-semibold text-white' : 'text-white/80 hover:bg-white/10' }}">
                                <x-icon :name="$item['icon']" class="h-4 w-4 opacity-90" />
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-auto pt-4">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-sm text-white/80 hover:bg-white/10">
                            <x-icon name="logout" class="h-4 w-4" />
                            Log Keluar
                        </button>
                    </form>
                </div>
            </aside>
            <div class="flex min-w-0 flex-1 flex-col">
                <x-app-header :notification-count="$notificationCount ?? 0" menu-href="/fama/menu" />
                <main class="mx-auto w-full min-w-0 max-w-6xl flex-1 px-3 py-3 sm:px-4 sm:py-4">
                    {{ $slot }}
                </main>
                <x-gov-footer />
            </div>
        </div>
    </div>
</x-layouts.app>
