<x-layouts.fama title="Menu Utama">
    <x-page-title title="Menu Utama" />
    <div class="space-y-2 p-4 md:hidden">
        <a href="{{ route('fama.dashboard') }}" class="block rounded-xl bg-white px-3 py-3 text-sm shadow-sm">Utama</a>
        <a href="{{ route('fama.qr') }}" class="block rounded-xl bg-white px-3 py-3 text-sm shadow-sm">Pengurusan QR</a>
        <a href="{{ route('fama.applications') }}" class="block rounded-xl bg-white px-3 py-3 text-sm shadow-sm">Kelulusan QR</a>
        <a href="{{ route('fama.companies') }}" class="block rounded-xl bg-white px-3 py-3 text-sm shadow-sm">Maklumat Syarikat</a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="w-full rounded-xl bg-white px-3 py-3 text-left text-sm text-danger shadow-sm">Log Keluar</button>
        </form>
    </div>
</x-layouts.fama>
