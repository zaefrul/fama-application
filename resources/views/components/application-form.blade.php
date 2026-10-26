@props([
    'action',
    'application' => null,
    'produceTypes',
    'certificates',
    'editable' => false,
    'primaryLabel' => 'Seterusnya',
    'hideSecondary' => false,
    'companyName' => null,
    'choiceUrl' => null,
])
@php
    $readOnly = $editable ? false : ($application ? $application->status->value !== 'DRAFT' : false);
    $cocCertificates = $certificates->where('type', 'CoC');
    $displayCompany = $companyName ?? $application?->company?->name;
    $marketValue = old('market', $application?->market?->value ?? \App\Domain\Market::Export->value);
    $isExport = $marketValue !== \App\Domain\Market::Domestic->value;
@endphp
<div class="space-y-4">
    <x-breadcrumb :items="['Senarai QR', 'Maklumat Eksport']" />
    <x-progress-steps :current="2" :total="2" />
    <x-card class="px-5 py-5">
        <form action="{{ $action }}" method="post" enctype="multipart/form-data" class="grid gap-4">
            @csrf
            <input type="hidden" name="productKind" value="PRODUCE">
            <x-market-choice :value="$marketValue" :read-only="$readOnly" />
            @if ($choiceUrl)
                <a href="{{ $choiceUrl }}" class="text-sm font-semibold text-brand">Tukar jenis</a>
            @endif
            @if (session('error'))
                <x-error-text>{{ session('error') }}</x-error-text>
            @endif
            <section class="grid gap-3">
                <h2 class="text-sm font-bold text-brand">Maklumat Keluaran</h2>
                @if ($displayCompany)
                    <x-field label="Nama Syarikat">
                        <x-input :value="$displayCompany" readonly />
                    </x-field>
                @endif
                <x-field label="Jenis Keluaran Pertanian" required hint="Taip untuk cari. Jika tiada dalam senarai, tekan + untuk tambah.">
                    <x-produce-type-field
                        :types="$produceTypes"
                        :selected="$application?->produce_type_id"
                        :disabled="$readOnly"
                        :required="! $readOnly"
                    />
                </x-field>
                <x-field label="Varieti" required>
                    <x-input name="variety" :value="$application?->variety" :readonly="$readOnly" required />
                </x-field>
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Gred" required>
                        <x-input name="grade" :value="$application?->grade" :readonly="$readOnly" required />
                    </x-field>
                    <x-field label="Saiz">
                        <x-input name="size" :value="$application?->size" :readonly="$readOnly" />
                    </x-field>
                </div>
                <x-field label="Kuantiti Bilangan" required>
                    <x-input name="quantity" type="number" :value="$application?->quantity" :readonly="$readOnly" required />
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
                <h2 class="text-sm font-bold text-brand">Maklumat Ladang</h2>
                <x-field label="Nama Ladang" required>
                    <x-input name="farmName" :value="$application?->farm_name" :readonly="$readOnly" required />
                </x-field>
                <x-field label="No. Lot">
                    <x-input name="lotNo" :value="$application?->lot_no" :readonly="$readOnly" />
                </x-field>
                <x-field label="Lokasi ladang">
                    <x-input name="farmLocation" :value="$application?->farm_location" :readonly="$readOnly" placeholder="Cari ladang atau alamat" autocomplete="off" />
                </x-field>
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Latitud">
                        <x-input name="farmLat" type="number" step="any" :value="$application?->farm_lat" :readonly="$readOnly" />
                    </x-field>
                    <x-field label="Longitud">
                        <x-input name="farmLng" type="number" step="any" :value="$application?->farm_lng" :readonly="$readOnly" />
                    </x-field>
                </div>
                @unless ($readOnly)
                    <x-farm-map
                        :lat="$application?->farm_lat"
                        :lng="$application?->farm_lng"
                        :interactive="true"
                    />
                @elseif ($application?->hasFarmCoordinates())
                    <x-farm-map
                        :lat="$application->farm_lat"
                        :lng="$application->farm_lng"
                    />
                @endunless
            </section>
            <section class="grid gap-3 border-t border-border pt-4" data-export-section @unless ($isExport) hidden @endunless>
                <h2 class="text-sm font-bold text-brand">Maklumat Eksport</h2>
                <x-field label="No Sijil CoC" :required="$isExport">
                    <x-select name="cocCertificateId" :disabled="$readOnly || ! $isExport">
                        <option value="">—</option>
                        @foreach ($cocCertificates as $certificate)
                            <option value="{{ $certificate->id }}" @selected($application?->coc_certificate_id === $certificate->id)>{{ $certificate->certificate_no }}</option>
                        @endforeach
                    </x-select>
                </x-field>
                <input type="hidden" name="cocNumber" value="{{ $application?->coc_number }}" @disabled(! $isExport && ! $readOnly)>
                <x-field label="Destinasi" :required="$isExport">
                    <x-input name="destinationCountry" :value="$application?->destination_country" :readonly="$readOnly" :required="$isExport" :disabled="! $isExport && ! $readOnly" data-export-required />
                </x-field>
                <x-field label="Tarikh Eksport">
                    <x-input name="exportDate" type="date" :value="$application?->export_date?->toDateString()" :readonly="$readOnly" :disabled="! $isExport && ! $readOnly" />
                </x-field>
                <x-field label="Pengimport" :required="$isExport">
                    <x-input name="importerName" :value="$application?->importer_name" :readonly="$readOnly" :required="$isExport" :disabled="! $isExport && ! $readOnly" data-export-required />
                </x-field>
                <x-field label="Alamat Pengimport" :required="$isExport">
                    <x-textarea name="importerAddress" :readonly="$readOnly" :required="$isExport" :disabled="! $isExport && ! $readOnly" data-export-required>{{ $application?->importer_address }}</x-textarea>
                </x-field>
            </section>
            <x-market-toggle :read-only="$readOnly" />
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
