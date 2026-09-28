@props([
    'action',
    'application' => null,
    'produceTypes',
    'editable' => false,
    'primaryLabel' => 'Seterusnya',
    'hideSecondary' => false,
    'companyName' => null,
    'choiceUrl' => null,
])
@php
    $readOnly = $editable ? false : ($application ? $application->status->value !== 'DRAFT' : false);
    $displayCompany = $companyName ?? $application?->company?->name;
@endphp
<div class="space-y-4">
    <x-breadcrumb :items="['Senarai QR', 'Maklumat Ternakan']" />
    <x-progress-steps :current="2" :total="2" />
    <x-card class="px-5 py-5">
        <form action="{{ $action }}" method="post" enctype="multipart/form-data" class="grid gap-4">
            @csrf
            <input type="hidden" name="productKind" value="LIVESTOCK">
            @if ($choiceUrl)
                <a href="{{ $choiceUrl }}" class="text-sm font-semibold text-brand">Tukar jenis</a>
            @endif
            @if (session('error'))
                <x-error-text>{{ session('error') }}</x-error-text>
            @endif
            <section class="grid gap-3">
                <h2 class="text-sm font-bold text-brand">Maklumat Ternakan</h2>
                @if ($displayCompany)
                    <x-field label="Nama Syarikat">
                        <x-input :value="$displayCompany" readonly />
                    </x-field>
                @endif
                <x-field label="Jenis Ternakan" required hint="Taip untuk cari. Jika tiada dalam senarai, tekan + untuk tambah.">
                    <x-produce-type-field
                        :types="$produceTypes"
                        :selected="$application?->produce_type_id"
                        :disabled="$readOnly"
                        :required="! $readOnly"
                        placeholder="Cari jenis ternakan"
                        add-label="Tambah jenis ternakan"
                    />
                </x-field>
                <x-field label="Baka" required>
                    <x-input name="variety" :value="$application?->variety" :readonly="$readOnly" required />
                </x-field>
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Bilangan (ekor)" required>
                        <x-input name="headCount" type="number" min="1" :value="$application?->head_count" :readonly="$readOnly" required />
                    </x-field>
                    <x-field label="Berat (kg)" required>
                        <x-input name="quantity" type="number" min="1" :value="$application?->quantity" :readonly="$readOnly" required />
                    </x-field>
                </div>
                <x-field label="Destinasi" required>
                    <x-input name="destinationCountry" :value="$application?->destination_country" :readonly="$readOnly" required />
                </x-field>
                <x-field label="No. Sijil Veterinar" required>
                    <x-input name="vetCertificateNo" :value="$application?->vet_certificate_no" :readonly="$readOnly" required />
                </x-field>
                <x-field label="Gambar paparan QR" hint="JPG/PNG/WEBP, maksimum {{ \App\Services\UploadService::maxLabel() }}. Gambar ini dipaparkan pada halaman awam QR.">
                    @if ($application?->display_image_path)
                        <img
                            src="{{ $application->display_image_path }}"
                            alt="Gambar paparan QR"
                            width="160"
                            height="96"
                            class="mb-2 h-24 w-40 rounded-xl object-cover"
                        >
                    @endif
                    <x-input name="displayImage" type="file" accept="image/jpeg,image/png,image/webp" :disabled="$readOnly" />
                </x-field>
            </section>
            <section class="grid gap-3 border-t border-border pt-4">
                <h2 class="text-sm font-bold text-brand">Maklumat Eksport</h2>
                <x-field label="Tarikh Eksport">
                    <x-input name="exportDate" type="date" :value="$application?->export_date?->toDateString()" :readonly="$readOnly" />
                </x-field>
                <x-field label="Tarikh Sembelih">
                    <x-input name="slaughterDate" type="date" :value="$application?->slaughter_date?->toDateString()" :readonly="$readOnly" />
                </x-field>
                <x-field label="Nama Premis" required>
                    <x-input name="farmName" :value="$application?->farm_name" :readonly="$readOnly" required />
                </x-field>
                <x-field label="Rumah Sembelih" required>
                    <x-input name="abattoirName" :value="$application?->abattoir_name" :readonly="$readOnly" required />
                </x-field>
                <x-field label="Lokasi premis">
                    <x-input name="farmLocation" :value="$application?->farm_location" :readonly="$readOnly" />
                </x-field>
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Latitud">
                        <x-input name="farmLat" type="number" step="any" :value="$application?->farm_lat" :readonly="$readOnly" />
                    </x-field>
                    <x-field label="Longitud">
                        <x-input name="farmLng" type="number" step="any" :value="$application?->farm_lng" :readonly="$readOnly" />
                    </x-field>
                </div>
                @if ($application?->hasFarmCoordinates())
                    <x-farm-map
                        :lat="$application->farm_lat"
                        :lng="$application->farm_lng"
                        :interactive="! $readOnly"
                        map-title="Lokasi premis"
                        marker-hint="Klik peta untuk menanda lokasi premis. Medan latitud dan longitud dikemaskini secara automatik."
                    />
                @endif
                <x-field label="Pengimport" required>
                    <x-input name="importerName" :value="$application?->importer_name" :readonly="$readOnly" required />
                </x-field>
                <x-field label="Alamat Pengimport" required>
                    <x-textarea name="importerAddress" :readonly="$readOnly" required>{{ $application?->importer_address }}</x-textarea>
                </x-field>
            </section>
            @if (! $readOnly)
                <div class="sticky bottom-20 flex gap-2 bg-white/90 py-2 md:bottom-0">
                    @unless ($hideSecondary)
                        <x-button type="submit" variant="secondary" class="flex-1">Simpan</x-button>
                    @endunless
                    <x-button type="submit" class="flex-1">{{ $primaryLabel }}</x-button>
                </div>
            @else
                <p class="text-sm text-muted">Permohonan ini bukan draf dan tidak boleh dikemaskini.</p>
            @endif
        </form>
    </x-card>
</div>
