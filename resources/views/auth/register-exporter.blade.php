<x-layouts.auth title="Daftar Usahawan">
    <x-card>
        <p class="mb-2 text-xs font-semibold text-muted">Pendaftaran Usahawan · Langkah <span id="step-label">1</span> / 4</p>
        <div class="mb-4 grid grid-cols-4 gap-1">
            @for ($i = 1; $i <= 4; $i++)
                <div id="bar-{{ $i }}" class="h-1.5 rounded-full {{ $i === 1 ? 'bg-brand' : 'bg-border' }}"></div>
            @endfor
        </div>
        <div id="step-1" class="space-y-4">
            <x-field label="Nombor Akaun" hint="Carian mock DagangNet. Cuba H0B00001" required>
                <x-input id="identifier" />
            </x-field>
            <p id="lookup-error" class="text-sm text-danger">{{ $error === 'notfound' ? 'Tiada rekod dijumpai' : '' }}</p>
            <x-button type="button" class="w-full" onclick="lookupCompany()">Seterusnya</x-button>
        </div>
        <div id="step-2" class="hidden space-y-3">
            <h2 class="font-semibold">Maklumat Syarikat</h2>
            <x-field label="Nama Syarikat"><x-input id="company-name" readonly /></x-field>
            <x-field label="Emel syarikat"><x-input id="company-email" readonly /></x-field>
            <x-field label="Status"><x-input id="company-status" readonly /></x-field>
            <x-button type="button" class="w-full" onclick="setStep(3)">Seterusnya</x-button>
        </div>
        <form id="register-form" action="{{ url('/auth/register/exporter') }}" method="post" class="hidden space-y-3">
            @csrf
            <input type="hidden" name="identifier" id="identifier-hidden">
            <div id="step-3" class="space-y-3">
                <h2 class="font-semibold">Maklumat Pengguna</h2>
                <x-field label="No Kad Pengenalan Pengguna" required>
                    <x-input name="identityReference" required value="660113021111" />
                </x-field>
                <x-field label="Nama Pengguna" required>
                    <x-input name="name" required value="Ali bin Abu" />
                </x-field>
                <x-button type="button" class="w-full" onclick="setStep(4)">Seterusnya</x-button>
            </div>
            <div id="step-4" class="hidden space-y-3">
                <h2 class="font-semibold">Kata Laluan</h2>
                <x-field label="Kata Laluan" required>
                    <x-input name="password" type="password" required minlength="8" />
                </x-field>
                <x-field label="Sahkan Kata Laluan" required>
                    <x-input name="confirmPassword" type="password" required minlength="8" />
                </x-field>
                @if ($error === 'validation')
                    <x-error-text>Sila semak kata laluan dan maklumat pengguna.</x-error-text>
                @endif
                <x-button type="submit" class="w-full">Daftar</x-button>
            </div>
        </form>
    </x-card>
    <script>
        function setStep(step) {
            for (let i = 1; i <= 4; i++) {
                document.getElementById('bar-' + i).className = 'h-1.5 rounded-full ' + (i <= step ? 'bg-brand' : 'bg-border');
                const el = document.getElementById('step-' + i);
                if (el) el.classList.toggle('hidden', i !== step);
            }
            document.getElementById('step-label').textContent = step;
            document.getElementById('register-form').classList.toggle('hidden', step < 3);
        }
        async function lookupCompany() {
            const identifier = document.getElementById('identifier').value.trim();
            const error = document.getElementById('lookup-error');
            error.textContent = '';
            const response = await fetch('/api/integrations/dagangnet/company/' + encodeURIComponent(identifier));
            if (!response.ok) { error.textContent = 'Tiada rekod dijumpai'; return; }
            const data = await response.json();
            document.getElementById('company-name').value = data.name;
            document.getElementById('company-email').value = data.email;
            document.getElementById('company-status').value = data.status;
            document.getElementById('identifier-hidden').value = identifier;
            setStep(2);
        }
    </script>
</x-layouts.auth>
