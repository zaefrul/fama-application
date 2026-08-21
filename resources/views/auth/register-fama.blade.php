<x-layouts.auth title="Daftar FAMA">
    <x-card>
        <p class="mb-2 text-xs font-semibold text-muted">Pendaftaran Pegawai FAMA · Langkah <span id="step-label">1</span> / 3</p>
        <div class="mb-4 grid grid-cols-3 gap-1">
            @for ($i = 1; $i <= 3; $i++)
                <div id="bar-{{ $i }}" class="h-1.5 rounded-full {{ $i === 1 ? 'bg-brand' : 'bg-border' }}"></div>
            @endfor
        </div>
        <div id="step-1" class="space-y-4">
            <x-field label="Nombor Kad Pengenalan" hint="Carian mock iFAMA. Cuba 770101145533" required>
                <x-input id="identifier" />
            </x-field>
            <p id="lookup-error" class="text-sm text-danger">{{ $error === 'notfound' ? 'Tiada rekod dijumpai' : '' }}</p>
            <x-button type="button" class="w-full" onclick="lookupStaff()">Seterusnya</x-button>
        </div>
        <div id="step-2" class="hidden space-y-3">
            <h2 class="font-semibold">Maklumat Pegawai</h2>
            <x-field label="Nama Penuh"><x-input id="staff-name" readonly /></x-field>
            <x-field label="Emel"><x-input id="staff-email" readonly /></x-field>
            <x-field label="Jawatan"><x-input id="staff-position" readonly /></x-field>
            <x-button type="button" class="w-full" onclick="setStep(3)">Seterusnya</x-button>
        </div>
        <form id="register-form" action="{{ url('/auth/register/fama') }}" method="post" class="hidden space-y-3">
            @csrf
            <input type="hidden" name="identifier" id="identifier-hidden">
            <x-field label="Kata Laluan" required>
                <x-input name="password" type="password" required minlength="8" />
            </x-field>
            <x-field label="Sahkan Kata Laluan" required>
                <x-input name="confirmPassword" type="password" required minlength="8" />
            </x-field>
            @if ($error === 'validation')
                <x-error-text>Sila semak kata laluan.</x-error-text>
            @endif
            <x-button type="submit" class="w-full">Daftar</x-button>
        </form>
    </x-card>
    <script>
        function setStep(step) {
            for (let i = 1; i <= 3; i++) {
                document.getElementById('bar-' + i).className = 'h-1.5 rounded-full ' + (i <= step ? 'bg-brand' : 'bg-border');
                const el = document.getElementById('step-' + i);
                if (el) el.classList.toggle('hidden', i !== step);
            }
            document.getElementById('step-label').textContent = step;
            document.getElementById('register-form').classList.toggle('hidden', step !== 3);
        }
        async function lookupStaff() {
            const identifier = document.getElementById('identifier').value.trim();
            const error = document.getElementById('lookup-error');
            error.textContent = '';
            const response = await fetch('/api/integrations/ifama/staff/' + encodeURIComponent(identifier));
            if (!response.ok) { error.textContent = 'Tiada rekod dijumpai'; return; }
            const data = await response.json();
            document.getElementById('staff-name').value = data.fullName;
            document.getElementById('staff-email').value = data.email;
            document.getElementById('staff-position').value = data.position;
            document.getElementById('identifier-hidden').value = identifier;
            setStep(2);
        }
    </script>
</x-layouts.auth>
