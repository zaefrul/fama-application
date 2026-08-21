<x-layouts.fama title="Menu Utama">
    <x-page-title title="Menu Utama" subtitle="Navigasi pegawai FAMA" />
    <div class="space-y-2 md:hidden">
        <a href="{{ route('fama.dashboard') }}" class="flex items-center gap-3 rounded-xl border border-border bg-white px-3 py-3 text-sm shadow-sm">
            <x-icon name="home" class="h-5 w-5 text-brand" /> Utama
        </a>
        <a href="{{ route('fama.qr') }}" class="flex items-center gap-3 rounded-xl border border-border bg-white px-3 py-3 text-sm shadow-sm">
            <x-icon name="qr" class="h-5 w-5 text-brand" /> Pengurusan QR
        </a>
        <a href="{{ route('fama.applications') }}" class="flex items-center gap-3 rounded-xl border border-border bg-white px-3 py-3 text-sm shadow-sm">
            <x-icon name="check" class="h-5 w-5 text-brand" /> Kelulusan QR
        </a>
        <a href="{{ route('fama.companies') }}" class="flex items-center gap-3 rounded-xl border border-border bg-white px-3 py-3 text-sm shadow-sm">
            <x-icon name="building" class="h-5 w-5 text-brand" /> Maklumat Syarikat
        </a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="flex w-full items-center gap-3 rounded-xl border border-danger/20 bg-white px-3 py-3 text-left text-sm text-danger shadow-sm">
                <x-icon name="logout" class="h-5 w-5" /> Log Keluar
            </button>
        </form>
    </div>
    <p class="hidden text-sm text-muted md:block">Gunakan menu sisi di kiri untuk navigasi.</p>
</x-layouts.fama>
