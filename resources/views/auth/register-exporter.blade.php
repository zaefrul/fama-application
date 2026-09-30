<x-layouts.auth title="Daftar Usahawan">
    <x-card>
        <p class="mb-2 text-xs font-semibold text-muted">Pendaftaran Usahawan · Langkah <span id="step-label">1</span> / 4</p>
        <div class="mb-4 grid grid-cols-4 gap-1">
            @for ($i = 1; $i <= 4; $i++)
                <div id="bar-{{ $i }}" class="h-1.5 rounded-full {{ $i === 1 ? 'bg-brand' : 'bg-border' }}"></div>
            @endfor
        </div>
        <div id="step-1" class="space-y-4">
            <x-field label="Nombor Akaun" hint="Boleh tambah lebih daripada satu. H0B00003 dan H0B00004 masih kosong. H0B00001 sudah ada akaun demo." required>
                <x-input id="identifier" />
            </x-field>
            <ul id="company-list" class="space-y-2"></ul>
            <p id="lookup-error" class="text-sm text-danger">{{ $errorMessage }}</p>
            <div class="grid grid-cols-2 gap-2">
                <x-button type="button" variant="secondary" class="w-full" onclick="addCompany()">Tambah</x-button>
                <x-button type="button" class="w-full" onclick="nextFromLookup()">Seterusnya</x-button>
            </div>
        </div>
        <div id="step-2" class="hidden space-y-3">
            <h2 class="font-semibold">Maklumat Syarikat</h2>
            <ul id="company-summary" class="space-y-2"></ul>
            <x-button type="button" class="w-full" onclick="setStep(3)">Seterusnya</x-button>
        </div>
        <form id="register-form" action="{{ url('/auth/register/exporter') }}" method="post" class="hidden space-y-3">
            @csrf
            <div id="identifier-fields"></div>
            <div id="step-3" class="space-y-3">
                <h2 class="font-semibold">Maklumat Pengguna</h2>
                <x-field label="No Kad Pengenalan Pengguna" required>
                    <x-input name="identityReference" required value="660113021111" />
                </x-field>
                <x-field label="Nama Pengguna" required>
                    <x-input name="name" required value="Ali bin Abu" />
                </x-field>
                <x-field label="Peranan dalam rantaian" hint="Lapisan ini disimpan pada syarikat yang didaftarkan." required>
                    <x-select name="party_type" id="party-type" required>
                        @foreach ($partyTypes as $type)
                            <option value="{{ $type->value }}" @selected($type->value === 'EXPORTER')>{{ $type->label() }}</option>
                        @endforeach
                    </x-select>
                </x-field>
                <div id="party-label-field" class="hidden">
                    <x-field label="Nama lapisan" hint="Contoh: Pengumpul atau Pemborong." required>
                        <x-input name="party_label" id="party-label" />
                    </x-field>
                </div>
                <x-button type="button" class="w-full" onclick="setStep(4)">Seterusnya</x-button>
            </div>
            <div id="step-4" class="hidden space-y-3">
                <h2 class="font-semibold">Kata Laluan</h2>
                <p class="text-sm text-muted">Emel log masuk ialah emel syarikat pertama: <span id="login-email" class="font-semibold text-ink"></span></p>
                <x-field label="Kata Laluan" required>
                    <x-input name="password" type="password" required minlength="8" />
                </x-field>
                <x-field label="Sahkan Kata Laluan" required>
                    <x-input name="confirmPassword" type="password" required minlength="8" />
                </x-field>
                <p id="password-error" class="text-sm text-danger"></p>
                <x-button type="submit" class="w-full">Daftar</x-button>
            </div>
        </form>
    </x-card>
    <script>
        const companies = [];

        document.getElementById('identifier').addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                addCompany();
            }
        });

        document.getElementById('register-form').addEventListener('submit', (event) => {
            const password = event.target.password.value;
            const confirm = event.target.confirmPassword.value;
            const error = document.getElementById('password-error');
            const party = document.getElementById('party-type').value;
            const label = document.getElementById('party-label').value.trim();
            error.textContent = '';
            if (companies.length === 0 || password.length < 8 || password !== confirm) {
                event.preventDefault();
                error.textContent = 'Sila semak kata laluan dan maklumat pengguna.';
                return;
            }
            if (party === 'OTHER' && label === '') {
                event.preventDefault();
                error.textContent = 'Nama lapisan diperlukan untuk Lain-lain.';
            }
        });

        document.getElementById('party-type').addEventListener('change', togglePartyLabel);
        togglePartyLabel();

        function togglePartyLabel() {
            const other = document.getElementById('party-type').value === 'OTHER';
            document.getElementById('party-label-field').classList.toggle('hidden', !other);
            document.getElementById('party-label').required = other;
        }

        function setStep(step) {
            for (let i = 1; i <= 4; i++) {
                document.getElementById('bar-' + i).className = 'h-1.5 rounded-full ' + (i <= step ? 'bg-brand' : 'bg-border');
                const el = document.getElementById('step-' + i);
                if (el) el.classList.toggle('hidden', i !== step);
            }
            document.getElementById('step-label').textContent = step;
            document.getElementById('register-form').classList.toggle('hidden', step < 3);
        }

        function renderCompanies() {
            const list = document.getElementById('company-list');
            const summary = document.getElementById('company-summary');
            const fields = document.getElementById('identifier-fields');
            list.replaceChildren();
            summary.replaceChildren();
            fields.replaceChildren();
            companies.forEach((company, index) => {
                list.append(companyRow(company, true, index));
                summary.append(companyRow(company, false, index));
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'identifiers[]';
                input.value = company.identifier;
                fields.append(input);
            });
            const email = document.getElementById('login-email');
            if (email) email.textContent = companies[0]?.email ?? '';
        }

        function companyRow(company, removable, index) {
            const item = document.createElement('li');
            item.className = 'rounded-xl border border-border bg-surface-muted px-3 py-2 text-sm';
            const title = document.createElement('p');
            title.className = 'font-semibold';
            title.textContent = company.name;
            const meta = document.createElement('p');
            meta.className = 'text-muted';
            meta.textContent = company.identifier + ' · ' + company.email + ' · ' + company.status;
            item.append(title, meta);
            if (removable) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'mt-1 text-xs font-semibold text-danger';
                button.textContent = 'Buang';
                button.addEventListener('click', () => {
                    companies.splice(index, 1);
                    renderCompanies();
                });
                item.append(button);
            }
            return item;
        }

        async function addCompany() {
            const input = document.getElementById('identifier');
            const identifier = input.value.trim();
            const error = document.getElementById('lookup-error');
            error.textContent = '';
            if (!identifier) {
                error.textContent = 'Masukkan nombor akaun.';
                return false;
            }
            if (companies.some((company) => company.identifier.toLowerCase() === identifier.toLowerCase())) {
                error.textContent = 'Nombor akaun ini sudah ditambah.';
                return false;
            }
            const response = await fetch('/api/integrations/dagangnet/company/' + encodeURIComponent(identifier));
            if (!response.ok) {
                error.textContent = 'Tiada rekod dijumpai';
                return false;
            }
            const data = await response.json();
            companies.push({
                identifier: data.identifier,
                name: data.name,
                email: data.email,
                status: data.status,
            });
            input.value = '';
            renderCompanies();
            return true;
        }

        async function nextFromLookup() {
            const identifier = document.getElementById('identifier').value.trim();
            if (identifier) {
                const added = await addCompany();
                if (!added) return;
            }
            if (companies.length === 0) {
                document.getElementById('lookup-error').textContent = 'Tambah sekurang-kurangnya satu nombor akaun.';
                return;
            }
            renderCompanies();
            setStep(2);
        }
    </script>
</x-layouts.auth>
