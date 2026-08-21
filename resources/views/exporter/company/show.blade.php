<x-layouts.exporter title="Maklumat Syarikat">
    @if (! $company)
        <p>Syarikat tidak dijumpai.</p>
    @else
        <x-page-title title="Maklumat Syarikat" subtitle="Medan dari DagangNet adalah baca sahaja." />
        <x-company-nav />
        <x-card>
            <form action="{{ route('exporter.company') }}" method="post" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2">
                @csrf
                <x-field label="No. Pendaftaran"><x-input readonly :value="$company->registration_no" /></x-field>
                <x-field label="Nama Syarikat"><x-input readonly :value="$company->name" /></x-field>
                <x-field label="Alamat" required><x-input name="address" :value="$company->address" /></x-field>
                <x-field label="Negeri"><x-input name="state" :value="$company->state" /></x-field>
                <x-field label="Daerah"><x-input name="district" :value="$company->district" /></x-field>
                <x-field label="Poskod"><x-input name="postcode" :value="$company->postcode" /></x-field>
                <x-field label="No. Telefon"><x-input name="phone" :value="$company->phone" /></x-field>
                <x-field label="Emel"><x-input name="email" type="email" :value="$company->email" /></x-field>
                <x-field label="Laman Web"><x-input name="website" :value="$company->website" /></x-field>
                <x-field label="Logo Syarikat"><x-input name="logo" type="file" accept="image/jpeg,image/png,image/webp" /></x-field>
                @if ($company->logo_path)
                    <div>
                        <p class="mb-1 text-sm font-medium">Logo semasa</p>
                        <img src="{{ $company->logo_path }}" alt="{{ $company->name }}" class="h-16 w-16 rounded-xl object-contain bg-surface-muted p-1">
                    </div>
                @endif
                <div class="md:col-span-2"><x-error-text>{{ $error }}</x-error-text></div>
                <div class="md:col-span-2 flex gap-2">
                    <x-button type="submit">Simpan</x-button>
                    <a href="{{ route('exporter.produce') }}" class="inline-flex items-center text-sm font-semibold text-brand">Seterusnya</a>
                </div>
            </form>
        </x-card>
    @endif
</x-layouts.exporter>
