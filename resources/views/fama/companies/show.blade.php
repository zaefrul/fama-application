<x-layouts.fama title="{{ $company->name }}">
    <div class="space-y-4">
        <x-page-title :title="$company->name" :subtitle="$company->registration_no.' · '.$company->external_source" />
        <x-card>
            <x-fama-company-form
                :action="url('/fama/companies/'.$company->id)"
                :company="$company"
                :error="$error"
                submit-label="Simpan Maklumat"
            />
        </x-card>
        <x-card>
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="font-semibold">Kod QR</h2>
                <a href="{{ route('fama.companies.qr.create', $company) }}"><x-button type="button">Cipta QR</x-button></a>
            </div>
            @if ($qrs->isEmpty())
                <p class="text-sm text-muted">Tiada QR. Cipta QR untuk paparan awam.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($qrs as $qr)
                        @php $application = $applications->firstWhere('id', $qr->application_id); @endphp
                        <li>
                            <x-card class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="font-semibold">{{ $qr->qr_code }}</p>
                                    <p class="text-xs text-muted">{{ $application?->variety ?: $qr->application?->produceType?->name }}</p>
                                </div>
                                <x-status-badge :qr="$qr->status" />
                                <div class="flex gap-2">
                                    <a href="{{ route('fama.companies.qr.edit', [$company, $qr->application_id]) }}" class="text-sm font-semibold text-brand">Kemaskini</a>
                                    <a href="{{ route('qr.download', $qr) }}?format=png&size=5" class="text-sm font-semibold text-brand">Muat turun</a>
                                </div>
                            </x-card>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
        <x-card>
            <h2 class="mb-2 font-semibold">Keluaran</h2>
            <p class="text-sm">{{ $company->produce->map(fn ($row) => $row->produceType?->name)->filter()->join(', ') ?: '—' }}</p>
        </x-card>
        <x-card>
            <h2 class="mb-3 font-semibold">Sijil</h2>
            <form action="{{ url('/fama/companies/'.$company->id.'/certificates') }}" method="post" enctype="multipart/form-data" class="mb-4 grid gap-3 md:grid-cols-2">
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
            </form>
            @if ($error)<x-error-text>{{ $error }}</x-error-text>@endif
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ($company->certificates as $certificate)
                    <div class="space-y-2 rounded-xl border border-border bg-surface-muted p-2">
                        <x-document-preview :src="\App\Services\JejakService::certificatePreviewPath($certificate->type, $certificate->document_path)" :alt="$certificate->type" class="mb-2 h-20 w-full object-cover" />
                        <p class="font-semibold">SIJIL {{ $certificate->type }}</p>
                        <p class="text-xs text-muted">{{ $certificate->certificate_no }}</p>
                        <form action="{{ url('/fama/companies/'.$company->id.'/certificates/delete') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $certificate->id }}">
                            <x-button type="submit" variant="danger">Buang</x-button>
                        </form>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-layouts.fama>
