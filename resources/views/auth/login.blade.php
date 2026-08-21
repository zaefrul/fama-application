<x-layouts.auth title="Log Masuk">
    <x-card class="px-5 py-6">
        <h1 class="mb-5 text-center text-lg font-bold">Log Masuk</h1>
        <form action="{{ url('/auth/login') }}" method="post" class="space-y-5">
            @csrf
            <fieldset>
                <legend class="mb-2 text-sm font-medium">Log masuk sebagai</legend>
                <div class="grid grid-cols-2 rounded-2xl bg-surface-muted p-1">
                    <label class="relative cursor-pointer text-center text-sm font-semibold">
                        <input type="radio" name="role" value="FAMA_OFFICER" @checked($defaultRole === 'FAMA_OFFICER') class="peer absolute inset-0 z-10 cursor-pointer opacity-0">
                        <span class="pointer-events-none block rounded-xl px-3 py-2 text-muted peer-checked:bg-white peer-checked:text-brand peer-checked:shadow-sm">FAMA</span>
                    </label>
                    <label class="relative cursor-pointer text-center text-sm font-semibold">
                        <input type="radio" name="role" value="EXPORTER" @checked($defaultRole !== 'FAMA_OFFICER') class="peer absolute inset-0 z-10 cursor-pointer opacity-0">
                        <span class="pointer-events-none block rounded-xl px-3 py-2 text-muted peer-checked:bg-white peer-checked:text-brand peer-checked:shadow-sm">PENGEKSPORT</span>
                    </label>
                </div>
            </fieldset>
            <x-field label="Emel" required>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">✉</span>
                    <x-input name="email" type="email" required placeholder="nama@contoh.com" class="pl-9" />
                </div>
            </x-field>
            <x-field label="Kata Laluan" required>
                <div class="relative">
                    <x-input id="password" name="password" type="password" required class="pr-12" />
                    <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-muted" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'; this.textContent=i.type==='password'?'Lihat':'Hide';">Lihat</button>
                </div>
            </x-field>
            <x-error-text>{{ $error }}</x-error-text>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <p class="text-sm">
                    <a href="{{ route('register.exporter') }}" class="font-semibold text-brand">Daftar</a>
                    <span class="text-muted"> | </span>
                    <a href="{{ route('password.forgot') }}" class="font-semibold text-brand">Lupa kata laluan</a>
                </p>
                <x-button type="submit" class="w-full sm:w-auto sm:min-w-28">Log Masuk</x-button>
            </div>
            <p class="text-center text-xs text-muted">
                Pegawai FAMA?
                <a href="{{ route('register.fama') }}" class="font-semibold text-brand">Daftar FAMA</a>
            </p>
        </form>
    </x-card>
</x-layouts.auth>
