<x-layouts.exporter title="Sijil">
    <div class="space-y-4">
        <x-page-title title="Sijil" subtitle="Muat naik salinan sijil (JPG, PNG, WEBP atau PDF, maksimum 5MB). Tiada pengesahan pihak berkuasa untuk V1." />
        <x-company-nav />
        <x-card>
            <form action="{{ route('exporter.certificates') }}" method="post" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2">
                @csrf
                <x-field label="Jenis">
                    <x-select name="type">
                        <option>HACCP</option>
                        <option>MyGAP</option>
                        <option>CoC</option>
                        <option>FITOSANITASI</option>
                        <option value="ISO 22000">ISO 22000</option>
                        <option>HALAL</option>
                    </x-select>
                </x-field>
                <x-field label="No. Sijil" required><x-input name="certificateNo" required /></x-field>
                <x-field label="Tarikh Keluar"><x-input name="issueDate" type="date" /></x-field>
                <x-field label="Tarikh Tamat"><x-input name="expiryDate" type="date" /></x-field>
                <x-field label="Fail sijil" required>
                    <x-input name="document" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" required />
                </x-field>
                <div class="flex items-end"><x-button type="submit">+ Muat Naik Sijil</x-button></div>
                <div class="md:col-span-2"><x-error-text>{{ $error }}</x-error-text></div>
            </form>
        </x-card>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($certificates as $certificate)
                <x-card class="min-w-0 space-y-2">
                    <x-document-preview :src="\App\Services\JejakService::certificatePreviewPath($certificate->type, $certificate->document_path)" :alt="$certificate->type" />
                    <p class="truncate text-sm font-semibold">SIJIL {{ $certificate->type }}</p>
                    <p class="truncate text-xs text-muted">{{ $certificate->certificate_no }}</p>
                    <a href="{{ $certificate->document_path }}" target="_blank" rel="noreferrer" class="text-xs font-semibold text-brand">Buka fail</a>
                    <form action="{{ url('/exporter/company/certificates/delete') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $certificate->id }}">
                        <x-button type="submit" variant="danger">Buang</x-button>
                    </form>
                </x-card>
            @endforeach
        </div>
    </div>
</x-layouts.exporter>
